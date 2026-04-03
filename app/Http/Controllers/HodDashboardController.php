<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class HodDashboardController extends Controller
{
    public function index(Request $request): View
    {
        $hodUser = $this->resolveHodUser($request);

        if (! $hodUser['authorized']) {
            return view('hod_dashboard', [
                'authorized' => false,
                'username' => Auth::user()->name,
                'summary' => ['pending' => 0, 'forwarded' => 0, 'rejected' => 0],
                'pending' => [],
                'recent' => [],
                'onTourCount' => 0,
                'onTourStaff' => [],
            ]);
        }

        $hodId = $hodUser['employee_id'];

        $pending = [];
        if (
            Schema::hasTable('leave_application')
            && Schema::hasTable('tab1')
            && Schema::hasTable('leave_type')
            && Schema::hasTable('department_hod')
        ) {
            $pending = DB::table('leave_application as la')
                ->join('tab1 as e', 'la.employee_id', '=', 'e.employee_id')
                ->join('leave_type as lt', 'la.leave_type_id', '=', 'lt.leave_type_id')
                ->join('department_hod as dh', 'e.department_id', '=', 'dh.department_id')
                ->where('dh.employee_id', $hodId)
                ->whereRaw('LOWER(la.HoD_status) = ?', ['pending'])
                ->orderByDesc('la.applied_at')
                ->select([
                    'la.application_id',
                    'e.employee_name',
                    'lt.leave_name',
                    'la.from_date',
                    'la.to_date',
                    'la.total_days',
                    'la.reason',
                ])
                ->get()
                ->map(fn ($r) => (array) $r)
                ->toArray();
        }

        $summary = ['pending' => 0, 'forwarded' => 0, 'rejected' => 0];
        if (
            Schema::hasTable('leave_application')
            && Schema::hasTable('tab1')
            && Schema::hasTable('department_hod')
        ) {
            $fetched = DB::table('leave_application as la')
                ->join('tab1 as e', 'la.employee_id', '=', 'e.employee_id')
                ->join('department_hod as dh', 'e.department_id', '=', 'dh.department_id')
                ->where('dh.employee_id', $hodId)
                ->selectRaw('SUM(CASE WHEN LOWER(la.HoD_status) = ? THEN 1 ELSE 0 END) AS pending', ['pending'])
                ->selectRaw('SUM(CASE WHEN LOWER(la.HoD_status) = ? THEN 1 ELSE 0 END) AS forwarded', ['forwarded'])
                ->selectRaw('SUM(CASE WHEN LOWER(la.HoD_status) = ? THEN 1 ELSE 0 END) AS rejected', ['rejected'])
                ->first();

            if ($fetched) {
                $summary = [
                    'pending' => (int) ($fetched->pending ?? 0),
                    'forwarded' => (int) ($fetched->forwarded ?? 0),
                    'rejected' => (int) ($fetched->rejected ?? 0),
                ];
            }
        }

        $recent = [];
        if (Schema::hasTable('leave_application') && Schema::hasTable('tab1') && Schema::hasTable('leave_type')) {
            $recent = DB::table('leave_application as la')
                ->join('tab1 as e', 'la.employee_id', '=', 'e.employee_id')
                ->join('leave_type as lt', 'la.leave_type_id', '=', 'lt.leave_type_id')
                ->where('la.HoD_action_by', $hodId)
                ->whereRaw('LOWER(la.HoD_status) IN (?, ?)', ['forwarded', 'rejected'])
                ->orderByDesc('la.HoD_action_at')
                ->limit(10)
                ->select([
                    'e.employee_name as employee',
                    'lt.leave_name as leave_name',
                    'la.HoD_status as action',
                    'la.HoD_action_at as action_at',
                ])
                ->get()
                ->map(fn ($r) => (array) $r)
                ->toArray();
        }

        $onTourStaff = [];
        $onTourCount = 0;
        if (Schema::hasTable('tour_records') && Schema::hasTable('tab1') && Schema::hasTable('department_hod')) {
            $today = now('Asia/Thimphu')->toDateString();
            $hasDepartmentTable = Schema::hasTable('department');

            $tourQuery = DB::table('tour_records as tr')
                ->join('tab1 as e', 'tr.employee_id', '=', 'e.employee_id')
                ->join('department_hod as dh', 'e.department_id', '=', 'dh.department_id')
                ->where('dh.employee_id', $hodId)
                ->whereDate('tr.start_date', '<=', $today)
                ->where(function ($q) use ($today): void {
                    $q->whereNull('tr.end_date')
                        ->orWhereDate('tr.end_date', '>=', $today);
                });

            if ($hasDepartmentTable) {
                $tourQuery->leftJoin('department as d', 'e.department_id', '=', 'd.department_id');
            }

            $tourSelect = [
                'tr.employee_id',
                'e.employee_name',
                'tr.place',
                'tr.start_date',
                'tr.end_date',
                'tr.purpose',
                'tr.office_order_pdf',
            ];
            $tourSelect[] = $hasDepartmentTable
                ? DB::raw("COALESCE(d.department_name, '-') as department_name")
                : DB::raw("'-' as department_name");

            $onTourStaff = $tourQuery
                ->orderBy('e.employee_name')
                ->select($tourSelect)
                ->get()
                ->map(fn ($r) => (array) $r)
                ->toArray();

            $onTourCount = count(array_unique(array_map(fn ($r) => (int) ($r['employee_id'] ?? 0), $onTourStaff)));
        }

        $totalStaff = 0;
        if (Schema::hasTable('tab1') && Schema::hasTable('department_hod')) {
            $deptIds = DB::table('department_hod')->where('employee_id', $hodId)->pluck('department_id')->toArray();
            $deptIds = array_values(array_filter($deptIds, fn($v) => $v !== null && $v !== ''));
            if (! empty($deptIds)) {
                $totalStaff = DB::table('tab1')->whereIn('department_id', $deptIds)->count();
            }
        }

        return view('hod_dashboard', [
            'authorized' => true,
            'username' => $hodUser['employee_name'] ?: Auth::user()->name,
            'summary' => $summary,
            'pending' => $pending,
            'recent' => $recent,
            'onTourCount' => $onTourCount,
            'onTourStaff' => $onTourStaff,
            'totalStaff' => $totalStaff,
        ]);
    }

    public function processAction(Request $request): RedirectResponse
    {
        $hodUser = $this->resolveHodUser($request);

        if (! $hodUser['authorized']) {
            return redirect(route('dashboard'))->with('flash_error', 'Access denied. You are not assigned as HoD.');
        }

        if (! Schema::hasTable('leave_application')) {
            return back()->with('message', 'Leave application table not found.');
        }

        $payload = $request->validate([
            'request_id' => ['required', 'integer'],
            'action' => ['required', 'in:Forward,Reject'],
        ]);

        $status = $payload['action'] === 'Forward' ? 'forwarded' : 'rejected';

        $update = [
            'HoD_status' => $status,
            'HoD_action_by' => $hodUser['employee_id'],
            'HoD_action_at' => now('Asia/Thimphu')->toDateTimeString(),
        ];

        if ($payload['action'] === 'Forward') {
            $update['medical_superintendent_status'] = 'pending';
        }

        $updateData = $this->filterColumns('leave_application', $update);
        if (empty($updateData)) {
            return back()->with('message', 'Leave table columns do not match expected fields.');
        }

        DB::table('leave_application')
            ->where('application_id', $payload['request_id'])
            ->update($updateData);

        return redirect(route('hod.dashboard'))->with('message', 'Request processed successfully');
    }

    public function staffList(Request $request): View|RedirectResponse
    {
        $hodUser = $this->resolveHodUser($request);

        if (! $hodUser['authorized']) {
            return redirect(route('dashboard'))->with('flash_error', 'Access denied. You are not assigned as HoD.');
        }

        $hodId = $hodUser['employee_id'];

        if (! Schema::hasTable('tab1') || ! Schema::hasTable('department_hod')) {
            return view('hod_staff_list', ['staff' => [], 'authorized' => true]);
        }

        $deptIds = DB::table('department_hod')->where('employee_id', $hodId)->pluck('department_id')->toArray();

        $deptIds = array_values(array_filter($deptIds, fn($v) => $v !== null && $v !== ''));

        if (empty($deptIds)) {
            return view('hod_staff_list', [
                'authorized' => true,
                'staff' => [],
                'username' => $hodUser['employee_name'] ?? Auth::user()->name,
            ]);
        }

        $query = DB::table('tab1 as t')
            ->whereIn('t.department_id', $deptIds)
            ->select('t.employee_id', 't.employee_name', 't.eid', 't.designation', 't.department_id');

        if (Schema::hasTable('department')) {
            $query->leftJoin('department as d', 't.department_id', '=', 'd.department_id')
                ->addSelect(DB::raw("COALESCE(d.department_name, '-') as department_name"));
        } else {
            $query->addSelect(DB::raw("'-' as department_name"));
        }

        $staff = $query->orderBy('t.employee_name')->get()->map(fn($r) => (array) $r)->toArray();

        // Load any per-employee 'unit' values saved in session by this HoD
        $units = (array) session('hod_units', []);

        return view('hod_staff_list', [
            'authorized' => true,
            'staff' => $staff,
            'username' => $hodUser['employee_name'] ?? Auth::user()->name,
            'units' => $units,
        ]);
    }

    public function updateUnit(Request $request): RedirectResponse
    {
        $hodUser = $this->resolveHodUser($request);

        if (! $hodUser['authorized']) {
            return redirect(route('dashboard'))->with('flash_error', 'Access denied. You are not assigned as HoD.');
        }

        $payload = $request->validate([
            'employee_id' => ['required', 'integer'],
            'unit' => ['nullable', 'string', 'max:100'],
        ]);

        $units = (array) session('hod_units', []);
        if (trim((string) $payload['unit']) === '') {
            unset($units[$payload['employee_id']]);
        } else {
            $units[$payload['employee_id']] = trim((string) $payload['unit']);
        }

        session(['hod_units' => $units]);

        return back()->with('flash_success', 'Unit saved.');
    }

    private function resolveHodUser(Request $request): array
    {
        $user = Auth::user();
        $eid = (string) $request->session()->get('eid', $user->email);

        if (! Schema::hasTable('tab1')) {
            return [
                'authorized' => false,
                'employee_id' => null,
                'employee_name' => $user->name,
            ];
        }

        $query = DB::table('tab1 as t')
            ->select('t.employee_id', 't.employee_name')
            ->where('t.eid', $eid);

        if (Schema::hasTable('role')) {
            $query->leftJoin('role as r', 'r.role_id', '=', 't.role_id')
                ->addSelect('r.role_name');
        }

        $row = $query->first();
        if (! $row) {
            $fallback = DB::table('tab1 as t')
                ->select('t.employee_id', 't.employee_name')
                ->where('t.employee_id', $user->id);

            if (Schema::hasTable('role')) {
                $fallback->leftJoin('role as r', 'r.role_id', '=', 't.role_id')
                    ->addSelect('r.role_name');
            }

            $row = $fallback->first();
            if (! $row) {
                return [
                    'authorized' => false,
                    'employee_id' => null,
                    'employee_name' => $user->name,
                ];
            }
        }

        $role = strtolower((string) ($row->role_name ?? ''));

        return [
            'authorized' => $role === 'hod',
            'employee_id' => $row->employee_id ?? null,
            'employee_name' => $row->employee_name ?? $user->name,
        ];
    }

    private function filterColumns(string $table, array $payload): array
    {
        $columns = Schema::getColumnListing($table);

        return array_filter(
            $payload,
            fn ($value, $key) => in_array($key, $columns, true),
            ARRAY_FILTER_USE_BOTH
        );
    }
}
