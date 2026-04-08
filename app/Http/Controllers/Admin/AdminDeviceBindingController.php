<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;

class AdminDeviceBindingController extends Controller
{
    public function index(Request $request)
    {
        $adminLoggedIn = Session::get('admin_logged_in', false);
        if (! $adminLoggedIn) {
            return redirect()->route('admin.login');
        }

        if (! Schema::hasTable('device_bindings')) {
            return view('admin_device_bindings', [
                'rows' => [],
                'flags' => [],
                'username' => Session::get('admin_name') ?? Session::get('admin_user'),
                'activeNav' => 'device_bindings',
                'error' => 'device_bindings table not present',
            ]);
        }

        $bindings = DB::table('device_bindings')->get()->map(fn($r) => (array) $r)->toArray();

        // Determine employee source
        $employeeTable = null;
        if (Schema::hasTable('tab1')) {
            $employeeTable = 'tab1';
        } elseif (Schema::hasTable('employees')) {
            $employeeTable = 'employees';
        }

        $employeeMap = [];
        $employeeList = [];
        // Prefer `tab1` as the main employee source; fall back to `employees` only if absent
        if (Schema::hasTable('tab1')) {
            $cols = Schema::getColumnListing('tab1');
            $select = ['employee_id'];
            if (in_array('eid', $cols, true)) {
                $select[] = 'eid';
            }
            if (in_array('employee_name', $cols, true)) {
                $select[] = 'employee_name';
            }
            $emps = DB::table('tab1')->select($select)->orderBy('employee_name')->get();
            foreach ($emps as $e) {
                $e = (array) $e;
                $employeeMap[$e['employee_id']] = $e;
            }
            // pass list for rebind select
            $employeeList = $emps->map(fn($x) => (array) $x)->toArray();
        } elseif (Schema::hasTable('employees')) {
            $cols = Schema::getColumnListing('employees');
            $select = ['employee_id'];
            if (in_array('eid', $cols, true)) {
                $select[] = 'eid';
            }
            if (in_array('name', $cols, true)) {
                $select[] = DB::raw('name as employee_name');
            }
            $emps = DB::table('employees')->select($select)->orderBy('name')->get();
            foreach ($emps as $e) {
                $e = (array) $e;
                $employeeMap[$e['employee_id']] = $e;
            }
            $employeeList = $emps->map(fn($x) => (array) $x)->toArray();
        }

        // Build counts and token maps
        $perEmployee = [];
        $tokenMap = [];
        foreach ($bindings as $b) {
            $eid = $b['employee_id'] ?? null;
            if ($eid !== null) {
                $perEmployee[$eid] = ($perEmployee[$eid] ?? 0) + 1;
            }
            $token = $b['device_token'] ?? null;
            if ($token) {
                $tokenMap[$token] = $tokenMap[$token] ?? [];
                if (! in_array($eid, $tokenMap[$token], true)) {
                    $tokenMap[$token][] = $eid;
                }
            }
        }

        $rows = [];
        foreach ($bindings as $b) {
            $eid = $b['employee_id'] ?? null;
            $token = $b['device_token'] ?? null;

            $employee = $employeeMap[$eid] ?? null;

            $multipleDevices = $eid !== null && ($perEmployee[$eid] ?? 0) > 1;
            $tokenShared = $token !== null && isset($tokenMap[$token]) && count(array_filter($tokenMap[$token], fn($v)=> $v !== null)) > 1;

            $missingEid = false;
            if ($eid === null) {
                // binding has no employee FK -> suspicious
                $missingEid = true;
            } else {
                if ($employee === null) {
                    // referenced employee does not exist in employee table
                    $missingEid = true;
                } else {
                    // employee exists but has no eid value
                    if (empty($employee['eid'] ?? null)) {
                        $missingEid = true;
                    }
                }
            }

            $suspicious = $tokenShared || $missingEid || $multipleDevices;

            $rows[] = array_merge($b, [
                'employee_name' => $employee['employee_name'] ?? ($b['employee_name'] ?? null) ?? '-',
                'employee_eid' => $employee['eid'] ?? ($b['eid'] ?? null) ?? null,
                'multiple_devices' => $multipleDevices,
                'token_shared' => $tokenShared,
                'missing_eid' => $missingEid,
                'suspicious' => $suspicious,
            ]);
        }

        // Normalize primary key name to `id` for view convenience (handles device_binding_id)
        $pkName = $this->findBindingPk();
        if ($pkName) {
            foreach ($rows as &$r) {
                if (isset($r[$pkName]) && empty($r['id'])) {
                    $r['id'] = $r[$pkName];
                }
            }
            unset($r);
        }

        return view('admin_device_bindings', [
            'rows' => $rows,
            'username' => Session::get('admin_name') ?? Session::get('admin_user'),
            'activeNav' => 'device_bindings',
            'employees' => $employeeList,
        ]);
    }

    /**
     * Find primary key column of device_bindings table.
     */
    private function findBindingPk(): ?string
    {
        if (! Schema::hasTable('device_bindings')) {
            return null;
        }
        $candidates = ['id', 'binding_id', 'device_binding_id'];
        $cols = Schema::getColumnListing('device_bindings');
        foreach ($candidates as $c) {
            if (in_array($c, $cols, true)) {
                return $c;
            }
        }
        if (in_array('id', $cols, true)) {
            return 'id';
        }
        return $cols[0] ?? null;
    }

    public function unbind(Request $request, $id)
    {
        $adminLoggedIn = Session::get('admin_logged_in', false);
        if (! $adminLoggedIn) {
            return redirect()->route('admin.login');
        }

        $pk = $this->findBindingPk();
        if (! $pk) {
            return redirect()->route('admin.device_bindings')->with('flash_error', 'Device bindings table missing');
        }

        try {
            DB::table('device_bindings')->where($pk, $id)->delete();
            return redirect()->route('admin.device_bindings');
        } catch (\Throwable $e) {
            logger()->error('Failed to unbind device', ['error' => $e->getMessage()]);
            return redirect()->route('admin.device_bindings')->with('flash_error', 'Failed to remove binding');
        }
    }

    public function rebind(Request $request, $id)
    {
        $adminLoggedIn = Session::get('admin_logged_in', false);
        if (! $adminLoggedIn) {
            return redirect()->route('admin.login');
        }

        $data = $request->validate([
            'employee_id' => 'required|integer',
        ]);

        $employeeId = $data['employee_id'];

        // ensure employee exists in either tab1 or employees
        $found = false;
        if (Schema::hasTable('tab1')) {
            $found = DB::table('tab1')->where('employee_id', $employeeId)->exists();
        }
        if (! $found && Schema::hasTable('employees')) {
            $found = DB::table('employees')->where('employee_id', $employeeId)->exists();
        }

        if (! $found) {
            return redirect()->route('admin.device_bindings')->with('flash_error', 'Employee not found');
        }

        $pk = $this->findBindingPk();
        if (! $pk) {
            return redirect()->route('admin.device_bindings')->with('flash_error', 'Device bindings table missing');
        }

        try {
            DB::table('device_bindings')->where($pk, $id)->update([
                'employee_id' => $employeeId,
                'bind_date' => now(),
                'updated_at' => now(),
            ]);
            return redirect()->route('admin.device_bindings')->with('flash_success', 'Binding updated');
        } catch (\Throwable $e) {
            logger()->error('Failed to rebind device', ['error' => $e->getMessage()]);
            return redirect()->route('admin.device_bindings')->with('flash_error', 'Failed to update binding');
        }
    }
}
