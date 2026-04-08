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
}
