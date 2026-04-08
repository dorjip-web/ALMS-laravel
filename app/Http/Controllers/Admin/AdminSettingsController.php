<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class AdminSettingsController extends Controller
{
    public function create()
    {
        return view('admin.settings.add-admin');
    }

    public function index()
    {
        // Load admin accounts from `admin` or `admins` table (prefer) or fallback to `users`
        $admins = [];
        try {
            $table = \Illuminate\Support\Facades\Schema::hasTable('admin') ? 'admin' : (\Illuminate\Support\Facades\Schema::hasTable('admins') ? 'admins' : null);
            if ($table) {
                $cols = \Illuminate\Support\Facades\Schema::getColumnListing($table);
                $pk = in_array('admin_id', $cols, true) ? 'admin_id' : (in_array('id', $cols, true) ? 'id' : ($cols[0] ?? 'id'));
                $rows = \Illuminate\Support\Facades\DB::table($table)->orderBy($pk)->get();
                $admins = $rows->map(fn($r) => (array) $r)->toArray();
            } elseif (\Illuminate\Support\Facades\Schema::hasTable('users')) {
                $rows = \Illuminate\Support\Facades\DB::table('users')->where('is_admin',1)->orderBy('id')->get();
                $admins = $rows->map(fn($r)=> (array) $r)->toArray();
            }
        } catch (\Throwable $e) {
            $admins = [];
        }

        return view('admin.settings.index', ['admins' => $admins, 'activeNav' => 'settings']);
    }

    public function store(Request $request)
    {
        // TODO: implement admin creation
        return redirect()->route('admin.dashboard')->with('flash_success', 'Admin added (stub)');
    }

    public function changePassword()
    {
        return view('admin.settings.change-admin-password');
    }

    public function updatePassword(Request $request)
    {
        // TODO: implement password change
        return redirect()->route('admin.dashboard')->with('flash_success', 'Password changed (stub)');
    }

    public function edit()
    {
        return view('admin.settings.edit-admin');
    }

    public function update(Request $request)
    {
        // TODO: implement admin update
        return redirect()->route('admin.dashboard')->with('flash_success', 'Admin updated (stub)');
    }

    public function toggle(Request $request, $id)
    {
        // Improved toggle: handle numeric (0/1) and string status columns ("Active"/"Inactive").
        try {
            $table = \Illuminate\Support\Facades\Schema::hasTable('admin') ? 'admin' : (\Illuminate\Support\Facades\Schema::hasTable('admins') ? 'admins' : null);
            $handled = false;
            $now = now();

            // tryToggle now returns the new value string (or numeric) when successful, false otherwise
            $tryToggle = function($tableName, $pkField, $activeField, $desired = null) use ($id, $now) {
                $row = \Illuminate\Support\Facades\DB::table($tableName)->where($pkField, $id)->first();
                if (!$row) return false;
                $current = $row->{$activeField} ?? null;
                $cols = \Illuminate\Support\Facades\Schema::getColumnListing($tableName);

                // If caller provided a desired state, use that (normalized by column type)
                if ($desired !== null) {
                    $d = strtolower(trim((string)$desired));
                    if (is_numeric($current)) {
                        // numeric columns expect 1/0
                        if ($d === 'active' || $d === '1' || $d === 'true') { $new = 1; }
                        else { $new = 0; }
                    } else {
                        // use string labels for non-numeric
                        if ($d === '1' || $d === 'true') { $new = 'Active'; }
                        elseif ($d === '0') { $new = 'Inactive'; }
                        else { $new = (strtolower($d) === 'active' ? 'Active' : 'Inactive'); }
                    }
                } else {
                    // Determine new value by inverting current
                    if (is_numeric($current)) {
                        $new = ($current ? 0 : 1);
                    } else {
                        $cur = strtolower(trim((string)$current));
                        if ($cur === 'active' || $cur === '1' || $cur === 'true') { $new = 'Inactive'; }
                        else { $new = 'Active'; }
                    }
                }

                $update = [$activeField => $new];
                if (in_array('updated_at', $cols, true)) { $update['updated_at'] = $now; }
                \Illuminate\Support\Facades\DB::table($tableName)->where($pkField, $id)->update($update);
                logger()->info('Toggled admin', ['table' => $tableName, 'pk' => $pkField, 'active' => $activeField, 'id' => $id, 'new' => $new]);
                return $new;
            };

            if ($table) {
                $cols = \Illuminate\Support\Facades\Schema::getColumnListing($table);
                $pk = in_array('admin_id', $cols, true) ? 'admin_id' : (in_array('id', $cols, true) ? 'id' : null);
                if ($pk !== null) {
                    // prefer these common active column names
                    foreach (['active','is_active','status','isadmin_active'] as $candidate) {
                        if (in_array($candidate, $cols, true)) {
                            $res = $tryToggle($table, $pk, $candidate, $request->input('desired', null));
                            if ($res !== false) { $handled = true; $newState = $res; break; }
                        }
                    }
                }
            }

            if (!$handled && \Illuminate\Support\Facades\Schema::hasTable('users')) {
                $cols = \Illuminate\Support\Facades\Schema::getColumnListing('users');
                $pk = 'id';
                foreach (['is_active','active','status'] as $candidate) {
                    if (in_array($candidate, $cols, true)) {
                        $res = $tryToggle('users', $pk, $candidate, $request->input('desired', null));
                        if ($res !== false) { $handled = true; $newState = $res; break; }
                    }
                }
            }

            if (!$handled) {
                throw new \Exception('No suitable active/status column found to toggle');
            }

        } catch (\Throwable $e) {
            logger()->error('Failed toggling admin', ['error' => $e->getMessage(), 'id' => $id]);
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['error' => $e->getMessage()], 500);
            }
            return redirect()->back()->with('flash_error', 'Failed to toggle admin: ' . $e->getMessage());
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['ok' => true, 'new' => $newState ?? null]);
        }

        return redirect()->route('admin.settings.index')->with('flash_success', 'Admin status updated');
    }

    /**
     * Show manage form for create/edit
     */
    public function manage(Request $request, $id = null)
    {
        $admin = [];
        try {
            if ($id) {
                $table = \Illuminate\Support\Facades\Schema::hasTable('admin') ? 'admin' : (\Illuminate\Support\Facades\Schema::hasTable('admins') ? 'admins' : null);
                if ($table) {
                    $pk = in_array('admin_id', \Illuminate\Support\Facades\Schema::getColumnListing($table), true) ? 'admin_id' : 'id';
                    $admin = (array) (\Illuminate\Support\Facades\DB::table($table)->where($pk, $id)->first() ?? []);
                } elseif (\Illuminate\Support\Facades\Schema::hasTable('users')) {
                    $admin = (array) (\Illuminate\Support\Facades\DB::table('users')->where('id', $id)->where('is_admin',1)->first() ?? []);
                }
            }
        } catch (\Throwable $e) {
            $admin = [];
        }

        return view('admin.settings.manage', ['admin' => $admin, 'activeNav' => 'settings']);
    }

    /**
     * Save admin (create or update). This is a minimal implementation: it prefers `admins` table, otherwise uses `users` with `is_admin` flag if present.
     */
    public function save(Request $request, $id = null)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255',
            'password' => 'nullable|string|min:6',
        ]);

        try {
            // support singular `admin` or plural `admins` table
            $table = \Illuminate\Support\Facades\Schema::hasTable('admin') ? 'admin' : (\Illuminate\Support\Facades\Schema::hasTable('admins') ? 'admins' : null);
            if ($table) {
                $cols = \Illuminate\Support\Facades\Schema::getColumnListing($table);
                $payload = [];
                // map to admin_name/username as in your DB
                $payload['admin_name'] = $data['name'];
                $payload['username'] = $data['username'];
                if (in_array('password', $cols, true) && !empty($data['password'])) { $payload['password'] = bcrypt($data['password']); }
                $payload['updated_at'] = now();

                $pk = in_array('admin_id', $cols, true) ? 'admin_id' : (in_array('id', $cols, true) ? 'id' : null);
                if ($id) {
                    \Illuminate\Support\Facades\DB::table($table)->where($pk,$id)->update($payload);
                } else {
                    $payload['created_at'] = now();
                    \Illuminate\Support\Facades\DB::table($table)->insert($payload);
                }
            } elseif (\Illuminate\Support\Facades\Schema::hasTable('users')) {
                $cols = \Illuminate\Support\Facades\Schema::getColumnListing('users');
                $payload = [
                    'name' => $data['name'],
                    'email' => $data['username'],
                    'is_admin' => 1,
                    'updated_at' => now(),
                ];
                if (in_array('password', $cols, true) && !empty($data['password'])) {
                    $payload['password'] = bcrypt($data['password']);
                }
                if ($id) {
                    \Illuminate\Support\Facades\DB::table('users')->where('id',$id)->update($payload);
                } else {
                    if (in_array('created_at', $cols, true)) { $payload['created_at'] = now(); }
                    \Illuminate\Support\Facades\DB::table('users')->insert($payload);
                }
            }
        } catch (\Throwable $e) {
            logger()->error('Failed saving admin', ['error' => $e->getMessage()]);
            return redirect()->back()->with('flash_error','Failed to save admin');
        }

        return redirect()->route('admin.settings.index')->with('flash_success','Admin saved');
    }
}
