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

        // determine which adhoc table actually exists (prefer the one with rows)
        $candidates = ['adhoc_requests', 'adhoc_request'];
        $table = null;
        foreach ($candidates as $cand) {
            if (! Schema::hasTable($cand)) {
                continue;
            }

            // prefer a table that already contains rows so admin sees existing data
            try {
                $cnt = DB::table($cand)->limit(1)->count();
            } catch (\Throwable $e) {
                $cnt = 0;
            }

            if ($cnt > 0) {
                $table = $cand;
                break;
            }

            // remember the first existing table as a fallback
            if ($table === null) {
                $table = $cand;
            }
        }

        $rows = [];
        if ($table && Schema::hasTable($table)) {
            $hasEmployees = Schema::hasTable('employees') || Schema::hasTable('tab1');

            $q = DB::table($table . ' as a');

            // Join to employee source (prefer `employees`, fall back to `tab1`).
            if ($hasEmployees) {
                $employeeTable = Schema::hasTable('employees') ? 'employees' : 'tab1';
                $employeeCols = Schema::getColumnListing($employeeTable);
                $adhocCols = Schema::getColumnListing($table);

                $q->leftJoin($employeeTable . ' as e', function ($join) use ($adhocCols, $employeeCols) {
                    // prefer matching employee_id
                    if (in_array('employee_id', $adhocCols, true) && in_array('employee_id', $employeeCols, true)) {
                        $join->on('a.employee_id', '=', 'e.employee_id');
                    }

                    // match on eid if present
                    if (in_array('eid', $adhocCols, true) && in_array('eid', $employeeCols, true)) {
                        $join->orOn('a.eid', '=', 'e.eid');
                    }

                    // match on user_id if present
                    if (in_array('user_id', $adhocCols, true) && in_array('user_id', $employeeCols, true)) {
                        $join->orOn('a.user_id', '=', 'e.user_id');
                    }
                });

                // apply department filter if requested
                if ($dept) {
                    $q->where('e.department_id', $dept);
                }
            }

            // Add department name if available
            $hasDepartment = Schema::hasTable('department');
            if ($hasDepartment) {
                $q->leftJoin('department as d', 'e.department_id', '=', 'd.department_id');
            }

            $select = ['a.*'];
            if ($hasEmployees) {
                $employeeDisplayParts = [];
                if (! empty($employeeCols) && in_array('employee_name', $employeeCols, true)) {
                    $employeeDisplayParts[] = 'e.employee_name';
                }
                if (! empty($employeeCols) && in_array('name', $employeeCols, true)) {
                    $employeeDisplayParts[] = 'e.name';
                }
                if (! empty($employeeCols) && in_array('eid', $employeeCols, true)) {
                    $employeeDisplayParts[] = 'e.eid';
                }
                if (! empty($employeeCols) && in_array('employee_id', $employeeCols, true)) {
                    $employeeDisplayParts[] = 'e.employee_id';
                }

                if (! empty($employeeDisplayParts)) {
                    $select[] = DB::raw('COALESCE(' . implode(', ', $employeeDisplayParts) . ", '-') as employee_name");
                } else {
                    $select[] = DB::raw("'-' as employee_name");
                }

                if ($hasDepartment) {
                    // use department_name if available
                    $deptCols = Schema::getColumnListing('department');
                    if (in_array('department_name', $deptCols, true)) {
                        $select[] = DB::raw("COALESCE(d.department_name, '-') as department_name");
                    } else {
                        $select[] = DB::raw("'-' as department_name");
                    }
                }
            }

            try {
                // prefer created_at ordering where present
                if (Schema::hasColumn($table, 'created_at')) {
                    $q->orderByDesc($table . '.created_at');
                } elseif (Schema::hasColumn($table, 'id')) {
                    $q->orderByDesc($table . '.id');
                }
                $rows = $q->select($select)->get()->map(fn($r) => (array) $r)->toArray();

                // If no rows found via join (or join caused mismatches), attempt a raw fallback
                if (empty($rows)) {
                    try {
                        $rawQ = DB::table($table);
                        // choose a safe ordering column
                        if (Schema::hasColumn($table, 'date')) {
                            $rawQ->orderByDesc('date');
                        } elseif (Schema::hasColumn($table, 'created_at')) {
                            $rawQ->orderByDesc('created_at');
                        } elseif (Schema::hasColumn($table, 'id')) {
                            $rawQ->orderByDesc('id');
                        }
                        $rows = $rawQ->select($table . '.*')->limit(200)->get()->map(fn($r) => (array) $r)->toArray();
                    } catch (\Throwable $e) {
                        logger()->debug('AdminAdhocController raw fallback failed', ['error' => $e->getMessage()]);
                    }
                }

                // Normalize row keys so views and routes can rely on common names
                foreach ($rows as &$row) {
                    // Normalize primary id: prefer known column names used across installs
                    $pkCandidates = ['adhoc_request_id', 'adhoc_id', 'application_id', 'id'];
                    foreach ($pkCandidates as $pk) {
                        if (array_key_exists($pk, $row) && ! empty($row[$pk])) {
                            $row['id'] = $row['id'] ?? $row[$pk];
                            $row['adhoc_id'] = $row['adhoc_id'] ?? $row[$pk];
                            break;
                        }
                    }

                    // Ensure an employee display value exists
                    if (empty($row['employee_name'])) {
                        if (! empty($row['eid'])) {
                            $row['employee_name'] = $row['eid'];
                        } elseif (! empty($row['employee_id'])) {
                            $row['employee_name'] = (string) $row['employee_id'];
                        }
                    }
                }
                unset($row);
            } catch (\Throwable $e) {
                // If something went wrong querying the table (missing table, permissions),
                // avoid blowing up the admin page — fall back to empty list and log.
                logger()->error('AdminAdhocController index query failed', ['error' => $e->getMessage()]);
                $rows = [];
            }
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
            'tableName' => $table,
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
