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

        return view('admin_adhoc_edit', [
            'record' => (array) $record,
            'employees' => $employees,
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
        ]);

        // Only include designation in the update if the adhoc table actually has the column
        $updateData = array_merge($data, ['updated_at' => now()]);
        if (! Schema::hasColumn($table, 'designation')) {
            unset($updateData['designation']);
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
}
