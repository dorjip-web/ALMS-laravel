<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class UserEditController extends Controller
{
    public function edit($id)
    {
        $user = DB::table('tab1')->where('employee_id', $id)->first();
        if (!$user) {
            return abort(404, 'User not found.');
        }
        $departments = DB::table('department')->orderBy('department_id')->get();
        return view('admin.users.edit', [
            'user' => $user,
            'username' => 'NTMH',
            'departments' => $departments,
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'eid' => 'required',
            'employee_name' => 'required',
            'designation' => 'required',
            'status' => 'required',
        ]);
        $data = [
            'eid' => $request->eid,
            'employee_name' => $request->employee_name,
            'username' => $request->username,
            'designation' => $request->designation,
            'department_id' => $request->department_id,
            'role_id' => $request->role_id,
            'status' => $request->status,
        ];
        // Password update logic can be added here if needed
        DB::table('tab1')->where('employee_id', $id)->update($data);
        return redirect()->route('admin.users.index');
    }
}
