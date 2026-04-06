<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB as DBFacade;

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
            $query = DBFacade::table($table . ' as a')
                ->leftJoin('tab1 as e', 'a.employee_id', '=', 'e.employee_id')
                ->select('a.*', DBFacade::raw("COALESCE(e.employee_name, '') as employee_name"))
                ->orderByDesc('a.created_at');

            if ($dept) {
                $query->where('e.department_id', $dept);
            }

            $rows = $query->get()->map(fn($r) => (array) $r)->toArray();
        }

        $departments = [];
        if (Schema::hasTable('department')) {
            $departments = DBFacade::table('department')->select('department_id','department_name')->orderBy('department_name')->get();
        }

        $employees = [];
        if (Schema::hasTable('tab1')) {
            $empQ = DBFacade::table('tab1')->select('employee_id','employee_name','department_id')->orderBy('employee_name');
            if ($dept) $empQ->where('department_id', $dept);
            $employees = $empQ->get();
        }

        return view('admin_adhoc_requests', [
            'tableExists' => (bool) $table,
            'rows' => $rows,
            'username' => Session::get('admin_name') ?? Session::get('admin_user'),
            'departments' => $departments,
            'dept' => $dept,
            'employees' => $employees,
        ]);
    }

    public function store(Request $request)
    {
        $adminLoggedIn = Session::get('admin_logged_in', false);
        if (! $adminLoggedIn) {
            return redirect()->route('admin.login');
        }

        $table = Schema::hasTable('adhoc_requests') ? 'adhoc_requests' : (Schema::hasTable('adhoc_request') ? 'adhoc_request' : null);
        if (! $table) {
            return redirect()->route('admin.adhoc')->with('flash_error', 'Adhoc requests table not found.');
        }

        $data = $request->validate([
            'employee_id' => ['required', 'integer'],
            'date' => ['required', 'date'],
            'purpose' => ['required', 'string'],
            'remarks' => ['nullable', 'string', 'max:255'],
        ]);

        DBFacade::table($table)->insert(array_merge($data, ['created_at' => now(), 'updated_at' => now()]));

        return redirect()->route('admin.adhoc')->with('flash_success', 'Adhoc request added');
    }

    public function edit(Request $request, $id)
    {
        $adminLoggedIn = Session::get('admin_logged_in', false);
        if (! $adminLoggedIn) {
            return redirect()->route('admin.login');
        }

        $table = Schema::hasTable('adhoc_requests') ? 'adhoc_requests' : (Schema::hasTable('adhoc_request') ? 'adhoc_request' : null);
        if (! $table) return redirect()->route('admin.adhoc')->with('flash_error', 'Adhoc requests table not found.');

        $record = DBFacade::table($table)->where(function($q) use ($id){ $q->where('id', $id)->orWhere('adhoc_id', $id)->orWhere('application_id', $id); })->first();
        if (! $record) return redirect()->route('admin.adhoc')->with('flash_error', 'Record not found');

        $departments = [];
        if (Schema::hasTable('department')) {
            $departments = DBFacade::table('department')->select('department_id','department_name')->orderBy('department_name')->get();
        }
        $employees = [];
        if (Schema::hasTable('tab1')) {
            $employees = DBFacade::table('tab1')->select('employee_id','employee_name','department_id')->orderBy('employee_name')->get();
        }

        return view('admin_adhoc_edit', ['record' => (array)$record, 'departments' => $departments, 'employees' => $employees]);
    }

    public function update(Request $request, $id)
    {
        $adminLoggedIn = Session::get('admin_logged_in', false);
        if (! $adminLoggedIn) {
            return redirect()->route('admin.login');
        }

        $table = Schema::hasTable('adhoc_requests') ? 'adhoc_requests' : (Schema::hasTable('adhoc_request') ? 'adhoc_request' : null);
        if (! $table) return redirect()->route('admin.adhoc')->with('flash_error', 'Adhoc requests table not found.');

        $data = $request->validate([
            'employee_id' => ['required', 'integer'],
            'date' => ['required', 'date'],
            'purpose' => ['required', 'string'],
            'remarks' => ['nullable', 'string', 'max:255'],
        ]);

        DBFacade::table($table)->where(function($q) use ($id){ $q->where('id', $id)->orWhere('adhoc_id', $id)->orWhere('application_id', $id); })->update(array_merge($data, ['updated_at' => now()]));

        return redirect()->route('admin.adhoc')->with('flash_success', 'Adhoc request updated');
    }

    public function delete(Request $request, $id)
    {
        $adminLoggedIn = Session::get('admin_logged_in', false);
        if (! $adminLoggedIn) {
            return redirect()->route('admin.login');
        }

        $table = Schema::hasTable('adhoc_requests') ? 'adhoc_requests' : (Schema::hasTable('adhoc_request') ? 'adhoc_request' : null);
        if (! $table) return redirect()->route('admin.adhoc')->with('flash_error', 'Adhoc requests table not found.');

        DBFacade::table($table)->where(function($q) use ($id){ $q->where('id', $id)->orWhere('adhoc_id', $id)->orWhere('application_id', $id); })->delete();

        return redirect()->route('admin.adhoc')->with('flash_success', 'Adhoc request deleted');
    }
}
