<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DepartmentEditController extends Controller
{
    public function edit($id)
    {
        $department = DB::table('department')->where('department_id', $id)->first();
        if (!$department) {
            abort(404, 'Department not found.');
        }
        return view('admin.departments.edit', [
            'department' => $department,
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'department_name' => 'required',
            'status' => 'required|in:active,inactive',
        ]);
        DB::table('department')->where('department_id', $id)->update([
            'department_name' => $request->department_name,
            'status' => $request->status,
        ]);
        return redirect()->route('admin.departments_hods.index');
    }
}
