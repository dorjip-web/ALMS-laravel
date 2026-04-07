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
        // Prefer the singular legacy table first since some installs use `adhoc_request`.
        $table = $this->detectAdhocTable();

        $rows = [];
        if ($table && Schema::hasTable($table)) {
            $hasEmployees = Schema::hasTable('employees') || Schema::hasTable('tab1');

            $q = DB::table($table . ' as a');
            $hasDepartment = Schema::hasTable('department');
            $select = ['a.*'];

            // Join to employee source (prefer `employees`, fall back to `tab1`).
            if ($hasEmployees) {
                // prefer `tab1` (legacy main employee table) then fall back to `employees`
                $employeeTable = Schema::hasTable('tab1') ? 'tab1' : 'employees';
                $employeeCols = Schema::getColumnListing($employeeTable);
                $adhocCols = Schema::getColumnListing($table);
                $hasDepartment = Schema::hasTable('department');

                // build select list (use alias `a.*` to avoid alias/table.* issues)
                $select = ['a.*'];
                // add employee display fallback columns
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

                // Add designation: prefer adhoc table column, then employee table column, else '-'
                if (! empty($employeeCols) && in_array('designation', $employeeCols, true) && in_array('designation', $adhocCols, true)) {
                    $select[] = DB::raw("COALESCE(a.designation, e.designation, '-') as designation");
                } elseif (! empty($employeeCols) && in_array('designation', $employeeCols, true)) {
                    $select[] = DB::raw("COALESCE(e.designation, '-') as designation");
                } elseif (in_array('designation', $adhocCols, true)) {
                    $select[] = 'a.designation as designation';
                } else {
                    $select[] = DB::raw("'-' as designation");
                }

                // department select handled below if department table exists

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

                // Join to department table (if present) and select department_name
                if (Schema::hasTable('department')) {
                    $deptCols = Schema::getColumnListing('department');
                    if (! empty($deptCols) && in_array('department_name', $deptCols, true)) {
                        $select[] = DB::raw("COALESCE(d.department_name, '-') as department_name");
                    } else {
                        $select[] = DB::raw("'-' as department_name");
                    }

                    $q->leftJoin('department as d', function ($join) use ($adhocCols, $employeeCols, $deptCols) {
                        if (in_array('department_id', $employeeCols, true) && in_array('department_id', $deptCols, true)) {
                            $join->on('e.department_id', '=', 'd.department_id');
                        } elseif (in_array('department_id', $adhocCols, true) && in_array('department_id', $deptCols, true)) {
                            $join->on('a.department_id', '=', 'd.department_id');
                        }
                    });
                } else {
                    $select[] = DB::raw("'-' as department_name");
                }

                // apply department filter if requested
                try {
                    // prefer created_at ordering where present
                    if (Schema::hasColumn($table, 'created_at')) {
                        $q->orderByDesc('a.created_at');
                    } elseif (Schema::hasColumn($table, 'id')) {
                        $q->orderByDesc('a.id');
                    }
                    $rows = $q->select($select)->get()->map(fn($r) => (array) $r)->toArray();

                    // If no rows found via join, attempt a raw fallback (no joins)
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
                } catch (\Throwable $e) {
                    // Try raw fallback when the joined query throws (e.g., collation or join errors)
                    logger()->warning('AdminAdhocController join query failed, attempting raw fallback', ['error' => $e->getMessage()]);
                    try {
                        $rawQ = DB::table($table);
                        if (Schema::hasColumn($table, 'date')) {
                            $rawQ->orderByDesc('date');
                        } elseif (Schema::hasColumn($table, 'created_at')) {
                            $rawQ->orderByDesc('created_at');
                        } elseif (Schema::hasColumn($table, 'id')) {
                            $rawQ->orderByDesc('id');
                        }
                        $rows = $rawQ->select($table . '.*')->limit(200)->get()->map(fn($r) => (array) $r)->toArray();
                    } catch (\Throwable $e2) {
                        logger()->error('AdminAdhocController raw fallback also failed', ['error' => $e2->getMessage()]);
                        $rows = [];
                    }
                }

                // department select will be appended after join block below
            }

            try {
                // prefer created_at ordering where present (use alias `a`)
                if (Schema::hasColumn($table, 'created_at')) {
                    $q->orderByDesc('a.created_at');
                } elseif (Schema::hasColumn($table, 'id')) {
                    $q->orderByDesc('a.id');
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
                        // raw fallback prepared (no debug logging)
                        $rows = $rawQ->select($table . '.*')->limit(200)->get()->map(fn($r) => (array) $r)->toArray();
                    } catch (\Throwable $e) {
                        logger()->warning('AdminAdhocController raw fallback failed', ['error' => $e->getMessage()]);
                    }
                }

                // Build department map for lookups (used when raw rows don't include joined department_name)
                $deptMap = [];
                if (Schema::hasTable('department')) {
                    try {
                        $deptMap = DB::table('department')->pluck('department_name', 'department_id')->toArray();
                    } catch (\Throwable $e) {
                        logger()->warning('AdminAdhocController failed to build department map', ['error' => $e->getMessage()]);
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

                    // Ensure department_name exists: prefer existing value, else lookup by department_id
                    if (empty($row['department_name'])) {
                        if (! empty($row['department_id']) && isset($deptMap[$row['department_id']])) {
                            $row['department_name'] = $deptMap[$row['department_id']];
                        } else {
                            $row['department_name'] = $row['department_name'] ?? '-';
                        }
                    }
                }
                unset($row);
                // (debug logging removed)
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

    /**
     * Return the adhoc table name to use for admin operations.
     * Prefer the legacy singular `adhoc_request` when present, otherwise use `adhoc_requests`.
     */
    private function detectAdhocTable(): ?string
    {
        if (Schema::hasTable('adhoc_request')) {
            return 'adhoc_request';
        }
        if (Schema::hasTable('adhoc_requests')) {
            return 'adhoc_requests';
        }
        return null;
    }

    /**
     * Find the primary/key column name used by the adhoc table in this install.
     */
    private function findAdhocPkColumn(string $table): ?string
    {
        $candidates = ['adhoc_request_id', 'adhoc_id', 'application_id', 'id', 'employee_id'];
        foreach ($candidates as $c) {
            if (Schema::hasColumn($table, $c)) {
                return $c;
            }
        }
        return null;
    }

    public function edit(Request $request, $id)
    {
        $adminLoggedIn = Session::get('admin_logged_in', false);
        if (! $adminLoggedIn) {
            return redirect()->route('admin.login');
        }

        $table = $this->detectAdhocTable();
        if (! $table) {
            return redirect()->route('admin.adhoc')->with('flash_error', 'Adhoc table not found.');
        }

        $pk = $this->findAdhocPkColumn($table);
        $record = $pk ? DB::table($table)->where($pk, $id)->first() : null;

        if (! $record) {
            return redirect()->route('admin.adhoc')->with('flash_error', 'Record not found');
        }

        $employees = [];
        if (Schema::hasTable('tab1')) {
            $hasDesignation = Schema::hasColumn('tab1', 'designation');
            $employees = $hasDesignation
                ? DB::table('tab1')->select('employee_id','employee_name','designation')->orderBy('employee_name')->get()
                : DB::table('tab1')->select('employee_id','employee_name')->orderBy('employee_name')->get();
        } elseif (Schema::hasTable('employees')) {
            $hasDesignation = Schema::hasColumn('employees', 'designation');
            $employees = $hasDesignation
                ? DB::table('employees')->select('employee_id','name as employee_name','designation')->orderBy('name')->get()
                : DB::table('employees')->select('employee_id','name as employee_name')->orderBy('name')->get();
        }

        $departments = [];
        if (Schema::hasTable('department')) {
            $departments = DB::table('department')->select('department_id','department_name')->orderBy('department_name')->get();
        }

        return view('admin_adhoc_edit', [
            'record' => (array) $record,
            'employees' => $employees,
            'departments' => $departments,
        ]);
    }

    public function update(Request $request, $id)
    {
        $table = $this->detectAdhocTable();
        if (! $table) {
            return redirect()->route('admin.adhoc')->with('flash_error', 'Adhoc table not found.');
        }

        $data = $request->validate([
            'employee_id' => 'required|integer',
            'date' => 'required|date',
            'purpose' => 'required|string',
            'remarks' => 'nullable|string|max:255',
            'designation' => 'nullable|string|max:255',
            'department_id' => 'nullable|integer',
        ]);

        // Only include designation in the update if the adhoc table actually has the column
        $updateData = array_merge($data, ['updated_at' => now()]);
        if (! Schema::hasColumn($table, 'designation')) {
            unset($updateData['designation']);
        }
        if (! Schema::hasColumn($table, 'department_id')) {
            unset($updateData['department_id']);
        }

        $pk = $this->findAdhocPkColumn($table);
        if ($pk) {
            DB::table($table)->where($pk, $id)->update($updateData);
        }

        return redirect()->route('admin.adhoc')->with('flash_success', 'Adhoc request updated');
    }

    public function delete(Request $request, $id)
    {
        $table = $this->detectAdhocTable();
        if (! $table) {
            return redirect()->route('admin.adhoc')->with('flash_error', 'Adhoc table not found.');
        }

        $pk = $this->findAdhocPkColumn($table);
        if ($pk) {
            DB::table($table)->where($pk, $id)->delete();
        }

        return redirect()->route('admin.adhoc')->with('flash_success', 'Adhoc request deleted');
    }

    /**
     * Export adhoc requests as CSV (honours department filter).
     */
    public function export(Request $request)
    {
        $table = $this->detectAdhocTable();
        if (! $table || ! Schema::hasTable($table)) {
            return redirect()->route('admin.adhoc')->with('flash_error', 'Adhoc table not found.');
        }

        $dept = $request->input('department_id', '');

        // Fetch raw rows from adhoc table (limit to reasonable size)
        $rawRows = DB::table($table)->orderByDesc('created_at')->limit(10000)->get()->map(fn($r) => (array) $r)->toArray();

        // Build employee map
        $employeeMap = [];
        if (Schema::hasTable('tab1')) {
            $emps = DB::table('tab1')->select('employee_id','employee_name','designation','department_id')->get();
            foreach ($emps as $e) {
                $employeeMap[$e->employee_id] = (array) $e;
            }
        } elseif (Schema::hasTable('employees')) {
            $emps = DB::table('employees')->select('employee_id','name as employee_name','designation','department_id')->get();
            foreach ($emps as $e) {
                $employeeMap[$e->employee_id] = (array) $e;
            }
        }

        // Build department map
        $deptMap = [];
        if (Schema::hasTable('department')) {
            $deptMap = DB::table('department')->pluck('department_name', 'department_id')->toArray();
        }

        // Prepare CSV rows matching the admin table columns
        $columns = ['Date','Purpose','Remarks','Department','Employee','Designation','Created'];

        $rows = [];
        foreach ($rawRows as $r) {
            // apply department filter if present
            $rowDeptId = $r['department_id'] ?? null;
            if (! empty($dept) && (string)$dept !== (string)($rowDeptId ?? '')) {
                // if not matching, also check employee's department
                $empDept = null;
                if (! empty($r['employee_id']) && isset($employeeMap[$r['employee_id']])) {
                    $empDept = $employeeMap[$r['employee_id']]['department_id'] ?? null;
                }
                if ((string)$dept !== (string)($empDept ?? '')) {
                    continue;
                }
            }

            // employee name resolution
            $employeeName = $r['employee_name'] ?? null;
            if (empty($employeeName) && ! empty($r['employee_id']) && isset($employeeMap[$r['employee_id']])) {
                $employeeName = $employeeMap[$r['employee_id']]['employee_name'] ?? null;
            }
            if (empty($employeeName)) {
                $employeeName = $r['eid'] ?? ($r['employee_id'] ?? '-');
            }

            // designation resolution
            $designation = $r['designation'] ?? null;
            if (empty($designation) && ! empty($r['employee_id']) && isset($employeeMap[$r['employee_id']])) {
                $designation = $employeeMap[$r['employee_id']]['designation'] ?? '';
            }

            // department name resolution
            $departmentName = $r['department_name'] ?? null;
            if (empty($departmentName)) {
                $deptId = $r['department_id'] ?? null;
                if (empty($deptId) && ! empty($r['employee_id']) && isset($employeeMap[$r['employee_id']])) {
                    $deptId = $employeeMap[$r['employee_id']]['department_id'] ?? null;
                }
                if (! empty($deptId) && isset($deptMap[$deptId])) {
                    $departmentName = $deptMap[$deptId];
                }
            }
            $departmentName = $departmentName ?? '-';

            $rows[] = [
                $r['date'] ?? '',
                $r['purpose'] ?? '',
                $r['remarks'] ?? '',
                $departmentName,
                $employeeName,
                $designation ?? '',
                $r['created_at'] ?? '',
            ];
        }

        $filename = 'adhoc_requests_' . date('Ymd_His') . '.csv';
        $callback = function() use ($columns, $rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, $columns);
            foreach ($rows as $r) {
                fputcsv($out, $r);
            }
            fclose($out);
        };

        return response()->streamDownload($callback, $filename, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }
}
