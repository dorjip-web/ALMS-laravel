<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserCreateController extends Controller
{
    public function create()
    {
        $departments = DB::table('department')->orderBy('department_id')->get();
        return view('admin.users.create', [
            'departments' => $departments,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'eid' => 'required',
            'employee_name' => 'required',
            'designation' => 'required',
            'username' => 'required',
            'password' => 'required',
            'department_id' => 'required',
            'role_id' => 'required',
            'status' => 'required',
        ]);
        DB::table('tab1')->insert([
            'eid' => $request->eid,
            'employee_name' => $request->employee_name,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'designation' => $request->designation,
            'department_id' => $request->department_id,
            'role_id' => $request->role_id,
            'status' => $request->status,
        ]);
        return redirect()->route('admin.users.index');
    }
}
