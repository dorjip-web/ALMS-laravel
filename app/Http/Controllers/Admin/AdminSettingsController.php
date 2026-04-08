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
        // Load admin accounts from `admins` table (prefer) or fallback to `users`
        $admins = [];
        $source = null;
        $count = 0;
        $dbName = null;
        $hasAdmins = false;
        try {
            $dbName = \Illuminate\Support\Facades\DB::connection()->getDatabaseName();
            $hasAdmins = \Illuminate\Support\Facades\Schema::hasTable('admins');

            if ($hasAdmins) {
                $rows = \Illuminate\Support\Facades\DB::table('admins')->orderBy('admin_id')->get();
                $admins = $rows->map(fn($r) => (array) $r)->toArray();
                $source = 'admins';
                $count = $rows->count();
            } elseif (\Illuminate\Support\Facades\Schema::hasTable('users')) {
                $rows = \Illuminate\Support\Facades\DB::table('users')->where('is_admin',1)->orderBy('id')->get();
                $admins = $rows->map(fn($r)=> (array) $r)->toArray();
                $source = 'users';
                $count = $rows->count();
            }
        } catch (\Throwable $e) {
            $admins = [];
        }

        return view('admin.settings.index', ['admins' => $admins, 'activeNav' => 'settings', 'source' => $source, 'count' => $count, 'dbName' => $dbName, 'hasAdmins' => $hasAdmins]);
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
            if (\Illuminate\Support\Facades\Schema::hasTable('admins')) {
                $cols = \Illuminate\Support\Facades\Schema::getColumnListing('admins');
                // prefer admin_id as PK
                $pk = in_array('admin_id', $cols, true) ? 'admin_id' : (in_array('id', $cols, true) ? 'id' : null);
                if ($pk === null) { throw new \Exception('No primary key found for admins table'); }
                // determine active column
                $activeCol = in_array('active', $cols, true) ? 'active' : (in_array('is_active', $cols, true) ? 'is_active' : null);
                if ($activeCol === null) { throw new \Exception('No active column on admins table'); }
                $row = \Illuminate\Support\Facades\DB::table('admins')->where($pk, $id)->first();
                if ($row) {
                    $current = $row->{$activeCol} ?? 0;
                    $new = $current ? 0 : 1;
                    \Illuminate\Support\Facades\DB::table('admins')->where($pk, $id)->update([$activeCol => $new, 'updated_at' => now()]);
                }
            } elseif (\Illuminate\Support\Facades\Schema::hasTable('users')) {
                $cols = \Illuminate\Support\Facades\Schema::getColumnListing('users');
                if (in_array('is_active', $cols, true) || in_array('active', $cols, true)) {
                    $activeCol = in_array('is_active', $cols, true) ? 'is_active' : 'active';
                    $row = \Illuminate\Support\Facades\DB::table('users')->where('id', $id)->first();
                    if ($row) {
                        $current = $row->{$activeCol} ?? 0; $new = $current ? 0 : 1;
                        \Illuminate\Support\Facades\DB::table('users')->where('id', $id)->update([$activeCol => $new, 'updated_at' => now()]);
                    }
                } else {
                    throw new \Exception('No active column on users table');
                }
            }
        } catch (\Throwable $e) {
            logger()->error('Failed toggling admin', ['error' => $e->getMessage(), 'id' => $id]);
            return redirect()->back()->with('flash_error', 'Failed to toggle admin: ' . $e->getMessage());
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
                if (\Illuminate\Support\Facades\Schema::hasTable('admins')) {
                    $admin = (array) (\Illuminate\Support\Facades\DB::table('admins')->where('admin_id', $id)->first() ?? []);
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
            if (\Illuminate\Support\Facades\Schema::hasTable('admins')) {
                $cols = \Illuminate\Support\Facades\Schema::getColumnListing('admins');
                $payload = [];
                $payload['admin_name'] = $data['name'];
                $payload['username'] = $data['username'];
                if (in_array('password', $cols, true) && !empty($data['password'])) { $payload['password'] = bcrypt($data['password']); }
                $payload['updated_at'] = now();

                if ($id) {
                    \Illuminate\Support\Facades\DB::table('admins')->where('admin_id',$id)->update($payload);
                } else {
                    $payload['created_at'] = now();
                    \Illuminate\Support\Facades\DB::table('admins')->insert($payload);
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
