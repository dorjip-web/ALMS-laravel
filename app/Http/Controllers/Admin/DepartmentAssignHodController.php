<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DepartmentAssignHodController extends Controller
{
    public function assign($id)
    {
        $department = DB::table('department')->where('department_id', $id)->first();
        if (!$department) {
            abort(404, 'Department not found.');
        }
        $current_hod_id = DB::table('department_hod')->where('department_id', $id)->value('employee_id');
        $employees = DB::table('tab1')->orderBy('employee_name')->get(['employee_id', 'employee_name']);
        return view('admin.departments.assign_hod', [
            'department' => $department,
            'current_hod_id' => $current_hod_id,
            'employees' => $employees,
        ]);
    }

    public function store(Request $request, $id)
    {
        $request->validate([
            'employee_id' => 'required|integer|min:1',
        ]);
        $exists = DB::table('department_hod')->where('department_id', $id)->exists();
        if ($exists) {
            DB::table('department_hod')->where('department_id', $id)->update(['employee_id' => $request->employee_id]);
        } else {
            DB::table('department_hod')->insert([
                'department_id' => $id,
                'employee_id' => $request->employee_id,
            ]);
        }
        return redirect()->route('admin.departments_hods.index');
    }
}
