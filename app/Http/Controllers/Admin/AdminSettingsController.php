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
        // Load admin accounts from common tables if available
        $admins = [];
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('admins')) {
                $admins = \Illuminate\Support\Facades\DB::table('admins')->orderBy('id')->get()->map(fn($r)=> (array) $r)->toArray();
            } elseif (\Illuminate\Support\Facades\Schema::hasTable('users')) {
                // assume users table may contain admin flag
                $cols = \Illuminate\Support\Facades\Schema::getColumnListing('users');
                if (in_array('is_admin', $cols, true)) {
                    $admins = \Illuminate\Support\Facades\DB::table('users')->where('is_admin',1)->orderBy('id')->get()->map(fn($r)=> (array) $r)->toArray();
                }
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

    public function toggle()
    {
        // TODO: implement toggle action
        return redirect()->route('admin.dashboard')->with('flash_success', 'Toggled (stub)');
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
                    $admin = (array) \Illuminate\Support\Facades\DB::table('admins')->where('id', $id)->first();
                } elseif (\Illuminate\Support\Facades\Schema::hasTable('users')) {
                    $admin = (array) \Illuminate\Support\Facades\DB::table('users')->where('id', $id)->where('is_admin',1)->first() ?? [];
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
            if (\Illuminate\Support\Facades\Schema::hasTable('admins')) {
                $payload = [
                    'name' => $data['name'],
                    'username' => $data['username'],
                    'active' => (int)$data['active'],
                    'updated_at' => now(),
                ];
                if (!empty($data['password'])) {
                    $payload['password'] = bcrypt($data['password']);
                }
                if ($id) {
                    \Illuminate\Support\Facades\DB::table('admins')->where('id',$id)->update($payload);
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
