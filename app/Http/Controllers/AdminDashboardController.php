<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class AdminDashboardController extends Controller
{
    /**
     * Display admin dashboard
     */
    public function index()
    {
        $role = Session::get('role', '');
        $adminLoggedIn = Session::get('admin_logged_in', false)
            || (is_string($role) && strtolower($role) === 'admin');
        $adminName = Session::get('admin_name') ?: Session::get('admin_user') ?: 'NTMH';
        
        if (!$adminLoggedIn) {
            return redirect()->route('admin.login');
        }

        $avatarLetters = strtoupper(substr(preg_replace('/\s+/', '', $adminName), 0, 2));

        return view('admin_dashboard', [
            'username' => $adminName,
            'avatar' => $avatarLetters !== '' ? $avatarLetters : 'AD',
        ]);
    }
}
