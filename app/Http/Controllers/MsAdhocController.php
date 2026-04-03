<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MsAdhocController extends Controller
{
    public function index()
    {
        $requests = collect();
        $table = null;
        if (Schema::hasTable('adhoc_requests')) {
            $table = 'adhoc_requests';
        } elseif (Schema::hasTable('adhoc_request')) {
            $table = 'adhoc_request';
        }

        if ($table && Schema::hasTable('tab1')) {
            $qb = DB::table($table . ' as a')
                ->join('tab1 as t', 'a.employee_id', '=', 't.employee_id');

            if (Schema::hasTable('department')) {
                $qb->leftJoin('department as d', 't.department_id', '=', 'd.department_id')
                   ->select('a.*', 't.employee_name as name', 'd.department_name as department');
            } else {
                $qb->select('a.*', 't.employee_name as name', 't.department_id as department');
            }

            $requests = $qb->orderBy('a.date', 'desc')->get();
        }

        return view('ms.adhoc.index', compact('requests'));
    }
}
