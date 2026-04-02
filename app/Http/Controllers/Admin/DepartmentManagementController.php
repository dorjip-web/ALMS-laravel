<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class DepartmentManagementController extends Controller
{
    public function index()
    {
        $rows = DB::table('department as d')
            ->leftJoin('department_hod as m', 'd.department_id', '=', 'm.department_id')
            ->leftJoin('tab1 as u', 'm.employee_id', '=', 'u.employee_id')
            ->select([
                'd.department_id',
                'd.department_name',
                'd.status',
                'u.employee_name as hod_name',
            ])->get();
        return view('admin.departments.index', [
            'rows' => $rows,
            'username' => 'NTMH',
        ]);
    }
}
