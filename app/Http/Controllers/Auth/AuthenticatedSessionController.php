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
        $employeeId = $legacyUser->employee_id ?? $legacyUser->eid ?? $legacyUser->employeeId ?? null;
        if ($employeeId !== null && Schema::hasTable('device_bindings')) {
            // If the browser already has a device token, check whether that token
            // is bound to a different employee. If so, refuse login (prevent
            // multiple employees using the same physical device).
            $incomingToken = $request->cookie('device_token');
            if ($incomingToken) {
                $existingByToken = DB::table('device_bindings')->where('device_token', $incomingToken)->first();
                if ($existingByToken && (string) $existingByToken->employee_id !== (string) $employeeId) {
                    return back()->withErrors([
                        'username' => 'This device is already bound to another user. Contact administrator to rebind.'
                    ])->onlyInput('username');
                }
            }

            // Normalize parent lookup across possible legacy column names to
            // avoid inserting an employee_id value that doesn't actually exist
            // in the `tab1` table (which can trigger FK constraint failures).
            $parentRow = DB::table('tab1')
                ->where('employee_id', $employeeId)
                ->orWhere('eid', $employeeId)
                ->orWhere('employeeId', $employeeId)
                ->first();

            $binding = null;
            if ($parentRow) {
                // Use the canonical FK value present in the parent row if available.
                $fkValue = $parentRow->employee_id ?? $parentRow->eid ?? $parentRow->employeeId ?? $employeeId;
                $binding = DB::table('device_bindings')->where('employee_id', $fkValue)->first();
            }

            if (! $binding) {
                // Only insert a new binding if the parent row actually exists
                // (prevents FK violations) and no other device token blocks us.
                if ($parentRow) {
                    $deviceToken = (string) Str::uuid();

                    DB::table('device_bindings')->insert([
                        'employee_id' => $fkValue,
                        'device_token' => $deviceToken,
                        'bind_date' => now(),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    $secure = $request->isSecure() || app()->environment('production');
                    cookie()->queue(cookie(
                        'device_token',
                        $deviceToken,
                        525600, // minutes (1 year)
                        null,
                        null,
                        $secure,
                        true // httpOnly
                    ));
                } else {
                    $request->session()->flash('device_binding_warning', 'Device binding skipped: legacy employee record not found. Contact admin.');
                }
            } else {
                // Subsequent login: require matching token in cookie
                $storedToken = $request->cookie('device_token');
                if (! $storedToken || $storedToken !== $binding->device_token) {
                    return back()->withErrors([
                        'username' => 'Device not authorized. Contact administrator for rebind.'
                    ])->onlyInput('username');
                }
            }
        }

        Auth::login($appUser, $request->boolean('remember'));

        // Keep the session 'eid' as the legacy table's `eid` (used by dashboard lookups).
        // Use $employeeId for device binding (FK) separately.
        $request->session()->put('eid', $legacyUser->eid ?? null);
        $request->session()->put('username', $legacyUser->username ?? $username);

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard', absolute: false));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}