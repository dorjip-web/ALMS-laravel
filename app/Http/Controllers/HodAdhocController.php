<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class HodAdhocController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $eid = (string) $request->session()->get('eid', $user->email);

        $row = null;
        if (Schema::hasTable('tab1')) {
            $row = DB::table('tab1 as t')->select('t.employee_id')->where('t.eid', $eid)->first();
            if (! $row) {
                $row = DB::table('tab1 as t')->select('t.employee_id')->where('t.employee_id', $user->id)->first();
            }
        }

        $hodId = $row->employee_id ?? null;
        $deptIds = [];
        if ($hodId && Schema::hasTable('department_hod')) {
            $deptIds = DB::table('department_hod')->where('employee_id', $hodId)->pluck('department_id')->toArray();
            $deptIds = array_values(array_filter($deptIds, fn($v) => $v !== null && $v !== ''));
        }

        $requests = collect();
        $table = null;
        if (Schema::hasTable('adhoc_requests')) {
            $table = 'adhoc_requests';
        } elseif (Schema::hasTable('adhoc_request')) {
            $table = 'adhoc_request';
        }

        if (! empty($deptIds) && $table && Schema::hasTable('tab1')) {
            $qb = DB::table($table . ' as a')
                ->join('tab1 as t', 'a.employee_id', '=', 't.employee_id')
                ->whereIn('t.department_id', $deptIds);

            if (Schema::hasTable('department')) {
                $qb->leftJoin('department as d', 't.department_id', '=', 'd.department_id')
                   ->select('a.*', 't.employee_name as name', 'd.department_name as department');
            } else {
                $qb->select('a.*', 't.employee_name as name', 't.department_id as department');
            }

            $requests = $qb->orderBy('a.date', 'desc')->get();
        }

        return view('hod.adhoc.index', compact('requests'));
    }
}
