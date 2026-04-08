<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class AdminSettingsController extends Controller
{
    /**
     * Detect table/column names for admin records.
     * Returns array: [table, pk, name_col, username_col, active_col, has_password]
     */
    private function detectAdminSchema(): array
    {
        // prefer `admins` table
        if (\Illuminate\Support\Facades\Schema::hasTable('admins')) {
            $cols = \Illuminate\Support\Facades\Schema::getColumnListing('admins');
            $pk = in_array('admin_id', $cols, true) ? 'admin_id' : (in_array('id', $cols, true) ? 'id' : $cols[0] ?? 'id');
            $name = in_array('name', $cols, true) ? 'name' : (in_array('admin_name', $cols, true) ? 'admin_name' : ($cols[1] ?? 'name'));
            $username = in_array('username', $cols, true) ? 'username' : (in_array('email', $cols, true) ? 'email' : $name);
            $active = in_array('active', $cols, true) ? 'active' : (in_array('is_active', $cols, true) ? 'is_active' : null);
            $hasPassword = in_array('password', $cols, true);
            return ['table' => 'admins', 'pk' => $pk, 'name' => $name, 'username' => $username, 'active' => $active, 'has_password' => $hasPassword];
        }

        // fallback to users table
        if (\Illuminate\Support\Facades\Schema::hasTable('users')) {
            $cols = \Illuminate\Support\Facades\Schema::getColumnListing('users');
            $pk = in_array('id', $cols, true) ? 'id' : ($cols[0] ?? 'id');
            $name = in_array('name', $cols, true) ? 'name' : ($cols[1] ?? 'name');
            $username = in_array('email', $cols, true) ? 'email' : (in_array('username', $cols, true) ? 'username' : $name);
            $active = in_array('is_active', $cols, true) ? 'is_active' : (in_array('active', $cols, true) ? 'active' : null);
            $hasPassword = in_array('password', $cols, true);
            return ['table' => 'users', 'pk' => $pk, 'name' => $name, 'username' => $username, 'active' => $active, 'has_password' => $hasPassword];
        }

        return ['table' => null, 'pk' => 'id', 'name' => 'name', 'username' => 'username', 'active' => null, 'has_password' => false];
    }
    public function create()
    {
        return view('admin.settings.add-admin');
    }

    public function index()
    {
        // Load admin accounts from detected admin/users table
        $admins = [];
        try {
            $schema = $this->detectAdminSchema();
            if ($schema['table']) {
                $q = \Illuminate\Support\Facades\DB::table($schema['table']);
                // if using users table, filter by is_admin when present
                if ($schema['table'] === 'users') {
                    $cols = \Illuminate\Support\Facades\Schema::getColumnListing('users');
                    if (in_array('is_admin', $cols, true)) { $q = $q->where('is_admin', 1); }
                }
                $rows = $q->orderBy($schema['pk'])->get()->map(fn($r)=> (array) $r)->toArray();
                $admins = $rows;
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
        try {
            $schema = $this->detectAdminSchema();
            if ($schema['table']) {
                $row = \Illuminate\Support\Facades\DB::table($schema['table'])->where($schema['pk'], $id)->first();
                if ($row && $schema['active']) {
                    $current = $row->{$schema['active']} ?? 0;
                    $new = $current ? 0 : 1;
                    \Illuminate\Support\Facades\DB::table($schema['table'])->where($schema['pk'], $id)->update([$schema['active'] => $new, 'updated_at' => now()]);
                }
            }
        } catch (\Throwable $e) {
            logger()->error('Failed toggling admin', ['error' => $e->getMessage(), 'id' => $id]);
            return redirect()->back()->with('flash_error', 'Failed to toggle admin');
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
                $schema = $this->detectAdminSchema();
                if ($schema['table']) {
                    $q = \Illuminate\Support\Facades\DB::table($schema['table'])->where($schema['pk'], $id);
                    if ($schema['table'] === 'users') {
                        $cols = \Illuminate\Support\Facades\Schema::getColumnListing('users');
                        if (in_array('is_admin', $cols, true)) { $q = $q->where('is_admin', 1); }
                    }
                    $admin = (array) ($q->first() ?? []);
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
            'active' => 'required|in:0,1',
        ]);

        try {
            $schema = $this->detectAdminSchema();
            if ($schema['table'] === 'admins') {
                $payload = [];
                $payload[$schema['name']] = $data['name'];
                $payload[$schema['username']] = $data['username'];
                if ($schema['active'] !== null) { $payload[$schema['active']] = (int)$data['active']; }
                $payload['updated_at'] = now();
                if (!empty($data['password']) && $schema['has_password']) { $payload['password'] = bcrypt($data['password']); }

                if ($id) {
                    \Illuminate\Support\Facades\DB::table($schema['table'])->where($schema['pk'],$id)->update($payload);
                } else {
                    $payload['created_at'] = now();
                    \Illuminate\Support\Facades\DB::table($schema['table'])->insert($payload);
                }
            } elseif ($schema['table'] === 'users') {
                $cols = \Illuminate\Support\Facades\Schema::getColumnListing('users');
                $payload = [];
                $payload[$schema['name']] = $data['name'];
                $payload[$schema['username']] = $data['username'];
                if (in_array('is_admin', $cols, true)) { $payload['is_admin'] = 1; }
                $payload['updated_at'] = now();
                if (!empty($data['password']) && in_array('password', $cols, true)) { $payload['password'] = bcrypt($data['password']); }

                if ($id) {
                    \Illuminate\Support\Facades\DB::table('users')->where($schema['pk'],$id)->update($payload);
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
