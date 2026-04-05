<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class AdminLoginController extends Controller
{
    /**
     * Display admin login form
     */
    public function showLoginForm()
    {
        return view('admin_login');
    }

    /**
     * Handle admin login POST request
     */
    public function login(Request $request)
    {
        $username = trim($request->input('username', ''));
        $password = $request->input('password', '');

        if ($username === '' || $password === '') {
            return redirect()->route('admin.login')
                ->with('login_error', 'Please enter username and password.');
        }

        try {
            $admin = null;

            if (DB::connection()->getSchemaBuilder()->hasTable('admin')) {
                $admin = DB::table('admin')
                    ->selectRaw('admin_id AS id, username, password, admin_name AS name')
                    ->whereRaw('LOWER(username) = LOWER(?)', [$username])
                    ->limit(1)
                    ->first();
            }

            // Verify password (supports password_hash and legacy formats: MD5, SHA1, SHA256, SHA512)
            $verified = false;
            $rehashNeeded = false;

            if ($admin && isset($admin->password)) {
                $stored = (string) $admin->password;

                // Try modern password_hash first
                if (password_verify($password, $stored)) {
                    $verified = true;
                    if (password_needs_rehash($stored, PASSWORD_DEFAULT)) {
                        $rehashNeeded = true;
                    }
                } else {
                    // Legacy hash support
                    if (hash_equals($stored, md5($password))) {
                        $verified = true;
                        $rehashNeeded = true;
                    } elseif (hash_equals($stored, sha1($password))) {
                        $verified = true;
                        $rehashNeeded = true;
                    } elseif (hash_equals($stored, hash('sha256', $password))) {
                        $verified = true;
                        $rehashNeeded = true;
                    } elseif (hash_equals($stored, hash('sha512', $password))) {
                        $verified = true;
                        $rehashNeeded = true;
                    } elseif (hash_equals($stored, $password)) {
                        // Plain-text fallback
                        $verified = true;
                        $rehashNeeded = true;
                    }
                }
            }

            if ($verified) {
                // Regenerate Laravel session ID for security
                $request->session()->regenerate();

                // Store admin info in session
                Session::put('admin_logged_in', true);
                Session::put('admin_id', $admin->id ?? null);
                Session::put('admin_user', $admin->username ?? null);
                Session::put('admin_name', $admin->name ?? $admin->username ?? null);

                // Upgrade to password_hash if needed
                if ($rehashNeeded) {
                    try {
                        $newHash = password_hash($password, PASSWORD_DEFAULT);
                        DB::table('admin')
                            ->whereRaw('LOWER(username) = LOWER(?)', [$username])
                            ->update(['password' => $newHash]);
                    } catch (\Exception $e) {
                        Log::debug('Failed to rehash admin password: ' . $e->getMessage());
                    }
                }

                return redirect()->route('admin.dashboard');
            }

            // Log failed attempt
            if ($admin) {
                Log::warning("Admin login failed for username '{$username}': password verification failed for id={$admin->id}.");
            } else {
                Log::warning("Admin login failed for username '{$username}': no matching admin record found.");
            }

            return redirect()->route('admin.login')
                ->with('login_error', 'Invalid username or password.');

        } catch (\Exception $e) {
            Log::error('Admin login error: ' . $e->getMessage());
            return redirect()->route('admin.login')
                ->with('login_error', 'Login failed.');
        }
    }

    /**
     * Handle admin logout
     */
    public function logout(Request $request)
    {
        Session::flush();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('admin.login');
    }
}
