<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserManagementController extends Controller
{
    public function index()
    {
        $users = DB::table('tab1 as t')
            ->leftJoin('department as d', 't.department_id', '=', 'd.department_id')
            ->leftJoin('role as r', 't.role_id', '=', 'r.role_id')
            ->select([
                't.employee_id',
                't.eid',
                't.employee_name',
                't.designation',
                'd.department_name',
                'r.role_name',
                't.status',
            ])->get();

        return view('admin.users.index', [
            'users' => $users,
            'username' => 'NTMH',
        ]);
    }
}
