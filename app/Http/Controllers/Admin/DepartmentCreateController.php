<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DepartmentCreateController extends Controller
{
    public function create()
    {
        return view('admin.departments.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'department_name' => 'required',
        ]);
        $id = DB::table('department')->insertGetId([
            'department_name' => $request->department_name,
            'status' => 'active',
        ]);
        return redirect()->route('admin.departments_hods.index');
    }
}
