<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        if (! Schema::hasTable('tab1')) {
            return back()->withErrors([
                'username' => 'Authentication table tab1 is missing.',
            ])->onlyInput('username');
        }

        $username = trim($credentials['username']);

        $legacyUser = DB::table('tab1')
            ->where('username', $username)
            ->first();

        $storedPassword = (string) ($legacyUser->password ?? '');

        if (! $legacyUser || $storedPassword === '' || ! password_verify($credentials['password'], $storedPassword)) {
            Log::warning('Auth failed', ['username' => $username, 'host' => $request->getSchemeAndHttpHost()]);
            return back()->withErrors([
                'username' => 'Invalid username or password.',
            ])->onlyInput('username');
        }

        // Prevent login for inactive users when the legacy table contains a status column
        if (Schema::hasColumn('tab1', 'status')) {
            $status = strtolower((string) ($legacyUser->status ?? ''));
            if ($status !== 'active') {
                return back()->withErrors([
                    'username' => 'Your account is inactive. Contact administrator.',
                ])->onlyInput('username');
            }
        }

        $localEmail = $username . '@alms.local';
        $appUser = User::query()->firstOrCreate(
            ['email' => $localEmail],
            [
                'name' => $legacyUser->employee_name ?? $legacyUser->username ?? $username,
                'password' => Hash::make(Str::random(32)),
            ]
        );

        // Device binding: determine employee identifier and enforce device ownership.
        $employeeId = $legacyUser->employee_id ?? $legacyUser->eid ?? null;
        // Determine client device type for concurrency checks (simple UA sniff).
        $ua = $request->header('User-Agent') ?? '';
        $isMobile = preg_match('/Mobile|Android|iPhone|iPad|iPod|Windows Phone/i', $ua);
        $deviceType = $isMobile ? 'mobile' : 'desktop';
        if ($employeeId !== null && Schema::hasTable('device_bindings')) {
            // If the browser already has a device token, check whether that token
            // is bound to a different employee. If so, refuse login (prevent
            // multiple employees using the same physical device).
            $incomingToken = $request->cookie('device_token');
                if ($incomingToken) {
                $existingByToken = DB::table('device_bindings')->where('device_token', $incomingToken)->first();
                if ($existingByToken && (string) $existingByToken->employee_id !== (string) $employeeId) {
                    Log::warning('Device binding prevents login', ['username' => $username, 'incoming_token' => $incomingToken, 'host' => $request->getSchemeAndHttpHost()]);
                    return $this->blockLoginResponse($request, 'This device is already bound to another user. Contact administrator to rebind.');
                }
            }

            // Normalize parent lookup across possible legacy column names to
            // avoid inserting an employee_id value that doesn't actually exist
            // in the `tab1` table (which can trigger FK constraint failures).
            $parentRow = DB::table('tab1')
                ->where(function ($q) use ($employeeId) {
                    $q->where('employee_id', $employeeId)
                      ->orWhere('eid', $employeeId);
                })
                ->first();

            $binding = null;
            if ($parentRow) {
                // Use the canonical FK value present in the parent row if available.
                $fkValue = $parentRow->employee_id ?? $parentRow->eid ?? $employeeId;
                $binding = DB::table('device_bindings')->where('employee_id', $fkValue)->first();
                Log::debug('Device binding lookup', ['username' => $username, 'employee_id' => $fkValue, 'binding' => $binding]);
            }

            if (! $binding) {
                // Only insert a new binding if the parent row actually exists
                // (prevents FK violations).
                if ($parentRow) {
                    $incomingToken = $request->cookie('device_token');

                    // Allow new employees to auto-bind their personal device
                    // even if other bindings exist in the system. The earlier
                    // check already prevents takeover when an incoming token
                    // is present and belongs to another employee.
                    $deviceToken = (string) Str::uuid();

                    DB::table('device_bindings')->insert([
                        'employee_id' => $fkValue,
                        'device_token' => $deviceToken,
                        'bind_date' => now(),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    $secure = $request->isSecure() || app()->environment('production');
                    // Queue cookie with SameSite=None so it survives in-app
                    // browsers/webviews. `false` is the $raw param required by
                    // the helper to pass the $sameSite value.
                    cookie()->queue(cookie(
                        'device_token',
                        $deviceToken,
                        525600, // minutes (1 year)
                        null,
                        null,
                        $secure,
                        true, // httpOnly
                        false,
                        'None'
                    ));

                    Log::debug('Device token queued', ['username' => $username, 'employee_id' => $fkValue, 'device_token' => $deviceToken, 'secure' => $secure, 'host' => $request->getSchemeAndHttpHost()]);
                } else {
                    $request->session()->flash('device_binding_warning', 'Device binding skipped: legacy employee record not found. Contact admin.');
                }
            } else {
                // Subsequent login: prefer a matching token in cookie. If the
                // binding exists but the browser has lost the cookie (common
                // on mobile), allow the same employee to reissue the token so
                // they are not blocked after a normal logout. Still prevent a
                // different employee from taking over the device.

                // Concurrency check: if this binding already has an active
                // authenticated session, block the new login to avoid the
                // same user being logged in on two devices simultaneously.
                $bindingSessionId = isset($binding->session_id) ? $binding->session_id : null;
                $bindingDeviceType = isset($binding->device_type) ? $binding->device_type : null;
                Log::debug('Concurrency check start', ['username' => $username, 'employee_id' => $binding->employee_id, 'binding_session_id' => $bindingSessionId]);
                if (! empty($bindingSessionId)) {
                    $sessionDriver = config('session.driver');

                    // Database-backed sessions (common in this app) - check
                    // the `sessions` table for recent activity.
                    if ($sessionDriver === 'database' && Schema::hasTable('sessions')) {
                        $active = DB::table('sessions')->where('id', $bindingSessionId)->first();
                        if ($active) {
                            $lifetimeSeconds = (int) (config('session.lifetime', 120) * 60);
                            $threshold = time() - $lifetimeSeconds;
                            if ((int) ($active->last_activity ?? 0) >= $threshold) {
                                Log::warning('Blocked concurrent login: active session exists (db)', ['username' => $username, 'employee_id' => $binding->employee_id, 'device_type' => $bindingDeviceType, 'host' => $request->getSchemeAndHttpHost()]);
                                $blockedMsg = 'Device not authorized. Please contact the administrator for re-binding.';
                                return $this->blockLoginResponse($request, $blockedMsg);
                            }
                        }

                    // File-based sessions (your environment uses `file`) -
                    // check the session file modification time as a proxy for
                    // recent activity.
                    } elseif ($sessionDriver === 'file') {
                        try {
                            $sessionFile = storage_path('framework/sessions/' . $bindingSessionId);
                            if (file_exists($sessionFile)) {
                                $mtime = filemtime($sessionFile) ?: 0;
                                $lifetimeSeconds = (int) (config('session.lifetime', 120) * 60);
                                if ($mtime >= (time() - $lifetimeSeconds)) {
                                    Log::warning('Blocked concurrent login: active session exists (file)', ['username' => $username, 'employee_id' => $binding->employee_id, 'session_file' => $sessionFile, 'host' => $request->getSchemeAndHttpHost()]);
                                    $blockedMsg = 'Device not authorized. Please contact the administrator for re-binding.';
                                    return $this->blockLoginResponse($request, $blockedMsg);
                                }
                            }
                        } catch (\Throwable $e) {
                            Log::warning('Error checking file session for concurrency', ['error' => $e->getMessage()]);
                        }
                    }
                    // Other session drivers (redis, memcached) currently fall
                    // through to allow login; we can extend checks if needed.
                }
                $storedToken = $request->cookie('device_token');
                if (! $storedToken || $storedToken !== $binding->device_token) {
                    // Allow reissue only when the request comes from the same
                    // device type and there is no active session recorded for
                    // the binding. This avoids forcing an admin rebind for a
                    // legitimate same-device login while still preventing
                    // buddy-punching across different physical devices.
                    $canReissue = false;

                    if ($bindingDeviceType !== null && $bindingDeviceType === $deviceType) {
                        // determine whether the binding's session is active
                        $isActive = false;
                        $sessionDriver = config('session.driver');

                        if (! empty($bindingSessionId)) {
                            if ($sessionDriver === 'database' && Schema::hasTable('sessions')) {
                                $active = DB::table('sessions')->where('id', $bindingSessionId)->first();
                                if ($active) {
                                    $lifetimeSeconds = (int) (config('session.lifetime', 120) * 60);
                                    $threshold = time() - $lifetimeSeconds;
                                    if ((int) ($active->last_activity ?? 0) >= $threshold) {
                                        $isActive = true;
                                    }
                                }
                            } elseif ($sessionDriver === 'file') {
                                try {
                                    $sessionFile = storage_path('framework/sessions/' . $bindingSessionId);
                                    if (file_exists($sessionFile)) {
                                        $mtime = filemtime($sessionFile) ?: 0;
                                        $lifetimeSeconds = (int) (config('session.lifetime', 120) * 60);
                                        if ($mtime >= (time() - $lifetimeSeconds)) {
                                            $isActive = true;
                                        }
                                    }
                                } catch (\Throwable $e) {
                                    Log::warning('Error checking file session for reissue decision', ['error' => $e->getMessage()]);
                                }
                            }
                        }

                        if (! $isActive) {
                            $canReissue = true;
                        }
                    }

                    if ($canReissue) {
                        $newToken = (string) Str::uuid();
                        DB::table('device_bindings')
                            ->where('employee_id', $binding->employee_id)
                            ->update([
                                'device_token' => $newToken,
                                'updated_at' => now(),
                            ]);

                        $secure = $request->isSecure() || app()->environment('production');
                        cookie()->queue(cookie(
                            'device_token',
                            $newToken,
                            525600, // minutes (1 year)
                            null,
                            null,
                            $secure,
                            true,
                            false,
                            'None'
                        ));

                        Log::info('Device token reissued for same device type', ['employee_id' => $binding->employee_id, 'new_token' => $newToken, 'device_type' => $deviceType, 'host' => $request->getSchemeAndHttpHost()]);
                    } else {
                        Log::warning('Device token missing or mismatch - blocking login', ['username' => $username, 'storedToken' => $storedToken ?? null, 'expected' => $binding->device_token, 'binding_device_type' => $bindingDeviceType, 'request_device_type' => $deviceType, 'host' => $request->getSchemeAndHttpHost()]);
                        return $this->blockLoginResponse($request, 'Device not authorized. Please contact the administrator for re-binding.');
                    }
                }
            }
        }

        Log::debug('Before Auth::login', ['username' => $username, 'host' => $request->getSchemeAndHttpHost(), 'cookies' => $request->cookies->all(), 'session_id' => $request->session()->getId()]);

        Auth::login($appUser, $request->boolean('remember'));

        // Keep the session 'eid' as the legacy table's `eid` (used by dashboard lookups).
        // Use $employeeId for device binding (FK) separately.
        $request->session()->put('eid', $legacyUser->eid ?? null);
        $request->session()->put('username', $legacyUser->username ?? $username);

        $request->session()->regenerate();

        // Update device_bindings with active session metadata so we can
        // detect concurrent logins from other devices.
        try {
            if ($employeeId !== null && Schema::hasTable('device_bindings')) {
                $parentRow2 = DB::table('tab1')
                    ->where(function ($q) use ($employeeId) {
                        $q->where('employee_id', $employeeId)
                          ->orWhere('eid', $employeeId);
                    })
                    ->first();

                if ($parentRow2) {
                    $fkValue2 = $parentRow2->employee_id ?? $parentRow2->eid ?? $employeeId;
                    DB::table('device_bindings')
                        ->where('employee_id', $fkValue2)
                        ->update([
                            'session_id' => $request->session()->getId(),
                            'device_type' => $deviceType ?? null,
                            'last_seen' => now(),
                            'updated_at' => now(),
                        ]);
                    Log::debug('Updated device_bindings after login', ['username' => $username, 'employee_id' => $fkValue2, 'session_id' => $request->session()->getId(), 'device_type' => $deviceType ?? null]);
                }
            }
        } catch (\Throwable $e) {
            Log::warning('Failed to update device binding session metadata', ['error' => $e->getMessage(), 'username' => $username]);
        }

        Log::debug('After login', ['username' => $username, 'auth_id' => Auth::id(), 'session_id' => $request->session()->getId(), 'host' => $request->getSchemeAndHttpHost()]);

        return redirect()->intended(route('dashboard', absolute: false));
    }

    public function destroy(Request $request): RedirectResponse
    {
        // Preserve the legacy employee id so we can clear any device binding
        // associated with this session before we invalidate it.
        $eid = $request->session()->get('eid');

        Auth::guard('web')->logout();

        // Do NOT remove the DB binding on logout. Keeping the binding
        // prevents other users from re-binding this physical device when
        // the cookie is cleared, enforcing one-device-per-user.

        // Remove the cookie so the client no longer presents a device token.
        cookie()->queue(cookie()->forget('device_token'));

        // Clear active session id on logout so another device can log in.
        try {
            if ($eid !== null && Schema::hasTable('device_bindings')) {
                DB::table('device_bindings')
                    ->where('employee_id', $eid)
                    ->update([
                        'session_id' => null,
                        'last_seen' => now(),
                        'updated_at' => now(),
                    ]);
            }
        } catch (\Throwable $e) {
            Log::warning('Failed to clear device binding session on logout', ['employee_id' => $eid, 'error' => $e->getMessage()]);
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    /**
     * Return a device-block response that works for both web browsers and
     * mobile/API clients. For JSON/AJAX requests return JSON; otherwise
     * flash a message and return the usual error redirect.
     */
    private function blockLoginResponse(Request $request, string $message)
    {
        // Only return JSON for explicit API/JSON requests. Many mobile
        // webviews send XHR or flexible Accept headers; prefer the HTML
        // flash/redirect so the login page shows the same message as
        // desktop browsers.
        if ($request->expectsJson()) {
            return response()->json(['message' => $message], 423);
        }

        $request->session()->flash('device_block', $message);
        return back()->withErrors([
            'username' => $message,
        ])->onlyInput('username');
    }
}