<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;

class AdminAdhocController extends Controller
{
    public function index(Request $request)
    {
        $adminLoggedIn = Session::get('admin_logged_in', false);
        if (! $adminLoggedIn) {
            return redirect()->route('admin.login');
        }
        $dept = $request->input('department_id', '');

        $table = null;
        if (Schema::hasTable('adhoc_requests')) {
            $table = 'adhoc_requests';
        } elseif (Schema::hasTable('adhoc_request')) {
            $table = 'adhoc_request';
        }

        $rows = [];
        if ($table) {
            $hasEmployees = Schema::hasTable('employees') || Schema::hasTable('tab1');

            $q = DB::table($table . ' as a');

            if ($hasEmployees) {
                if (Schema::hasTable('employees')) {
                    $q->join('employees as e', 'a.employee_id', '=', 'e.employee_id');
                } else {
                    $q->join('tab1 as e', 'a.employee_id', '=', 'e.employee_id');
                }
            }

            if ($dept && $hasEmployees) {
                $q->where('e.department_id', $dept);
            }

            // Add department name if available
            $hasDepartment = Schema::hasTable('department');
            if ($hasDepartment) {
                $q->leftJoin('department as d', 'e.department_id', '=', 'd.department_id');
            }

            $select = [$table . '.*'];
            if ($hasEmployees) {
                $select[] = DB::raw("COALESCE(e.employee_name, e.name, e.eid, '-') as employee_name");
                if ($hasDepartment) {
                    $select[] = DB::raw("COALESCE(d.department_name, '-') as department_name");
                }
            }

            $rows = $q->orderByDesc($table . '.created_at')
                ->select($select)
                ->get()
                ->map(fn($r) => (array) $r)
                ->toArray();
        }

        $departments = [];
        if (Schema::hasTable('department')) {
            $departments = DB::table('department')->select('department_id','department_name')->orderBy('department_name')->get();
        }

        $employees = [];
        if (Schema::hasTable('tab1')) {
            $employees = DB::table('tab1')->select('employee_id','employee_name','department_id')->orderBy('employee_name')->get();
        } elseif (Schema::hasTable('employees')) {
            $employees = DB::table('employees')->select('employee_id','name as employee_name','department_id')->orderBy('name')->get();
        }

        return view('admin_adhoc_requests', [
            'tableExists' => (bool) $table,
            'rows' => $rows,
            'username' => Session::get('admin_name') ?? Session::get('admin_user'),
            'departments' => $departments,
            'employees' => $employees,
            'dept' => $dept,
        ]);
    }

    public function edit(Request $request, $id)
    {
        $adminLoggedIn = Session::get('admin_logged_in', false);
        if (! $adminLoggedIn) {
            return redirect()->route('admin.login');
        }

        $table = Schema::hasTable('adhoc_requests') ? 'adhoc_requests' : (Schema::hasTable('adhoc_request') ? 'adhoc_request' : null);
        if (! $table) {
            return redirect()->route('admin.adhoc')->with('flash_error', 'Adhoc table not found.');
        }

        $record = DB::table($table)
            ->when(Schema::hasColumn($table, 'adhoc_id'), fn($q) => $q->where('adhoc_id', $id), fn($q) => $q->when(Schema::hasColumn($table, 'application_id'), fn($qq) => $qq->where('application_id', $id), fn($qq) => $qq->when(Schema::hasColumn($table, 'id'), fn($qqq) => $qqq->where('id', $id), fn($qqq) => $qqq->where('employee_id', $id))))
            ->first();

        if (! $record) {
            return redirect()->route('admin.adhoc')->with('flash_error', 'Record not found');
        }

        $employees = [];
        if (Schema::hasTable('tab1')) {
            $employees = DB::table('tab1')->select('employee_id','employee_name')->orderBy('employee_name')->get();
        } elseif (Schema::hasTable('employees')) {
            $employees = DB::table('employees')->select('employee_id','name as employee_name')->orderBy('name')->get();
        }

        return view('admin_adhoc_edit', [
            'record' => (array) $record,
            'employees' => $employees,
        ]);
    }

    public function update(Request $request, $id)
    {
        $table = Schema::hasTable('adhoc_requests') ? 'adhoc_requests' : (Schema::hasTable('adhoc_request') ? 'adhoc_request' : null);
        if (! $table) {
            return redirect()->route('admin.adhoc')->with('flash_error', 'Adhoc table not found.');
        }

        $data = $request->validate([
            'employee_id' => 'required|integer',
            'date' => 'required|date',
            'purpose' => 'required|string',
            'remarks' => 'nullable|string|max:255',
        ]);

        DB::table($table)
            ->when(Schema::hasColumn($table, 'adhoc_id'), fn($q) => $q->where('adhoc_id', $id), fn($q) => $q->when(Schema::hasColumn($table, 'application_id'), fn($qq) => $qq->where('application_id', $id), fn($qq) => $qq->when(Schema::hasColumn($table, 'id'), fn($qqq) => $qqq->where('id', $id), fn($qqq) => $qqq->where('employee_id', $id))))
            ->update(array_merge($data, ['updated_at' => now()]));

        return redirect()->route('admin.adhoc')->with('flash_success', 'Adhoc request updated');
    }

    public function delete(Request $request, $id)
    {
        $table = Schema::hasTable('adhoc_requests') ? 'adhoc_requests' : (Schema::hasTable('adhoc_request') ? 'adhoc_request' : null);
        if (! $table) {
            return redirect()->route('admin.adhoc')->with('flash_error', 'Adhoc table not found.');
        }

        DB::table($table)
            ->when(Schema::hasColumn($table, 'adhoc_id'), fn($q) => $q->where('adhoc_id', $id), fn($q) => $q->when(Schema::hasColumn($table, 'application_id'), fn($qq) => $qq->where('application_id', $id), fn($qq) => $qq->when(Schema::hasColumn($table, 'id'), fn($qqq) => $qqq->where('id', $id), fn($qqq) => $qqq->where('employee_id', $id))))
            ->delete();

        return redirect()->route('admin.adhoc')->with('flash_success', 'Adhoc request deleted');
    }
}
