<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class MsDashboardController extends Controller
{
    public function index(Request $request): View
    {
        $msUser = $this->resolveMsUser($request);

        if (! $msUser['authorized']) {
            return view('ms_dashboard', [
                'authorized' => false,
                'username' => Auth::user()->name,
                'summary' => ['pending' => 0, 'approved' => 0, 'rejected' => 0],
                'forwardedRequests' => [],
                'directRequests' => [],
                'recentDecisions' => [],
                'onTourCount' => 0,
                'onTourStaff' => [],
            ]);
        }

        $summary = ['pending' => 0, 'approved' => 0, 'rejected' => 0];
        if (Schema::hasTable('leave_application')) {
            // Count pending MS actions including both forwarded by HoD and direct requests
            $pending = DB::table('leave_application')
                ->whereRaw("LOWER(COALESCE(medical_superintendent_status, '')) = ?", ['pending'])
                ->where(function ($q): void {
                    $q->whereRaw("LOWER(COALESCE(HoD_status, '')) = ?", ['forwarded'])
                        ->orWhereNull('HoD_status')
                        ->orWhere('HoD_status', '')
                        ->orWhereRaw("LOWER(COALESCE(HoD_status, '')) = ?", ['skipped']);
                })
                ->count();

            $approved = DB::table('leave_application')
                ->whereRaw("LOWER(COALESCE(medical_superintendent_status, '')) = ?", ['approved'])
                ->count();

            $rejected = DB::table('leave_application')
                ->whereRaw("LOWER(COALESCE(medical_superintendent_status, '')) = ?", ['rejected'])
                ->count();

            $summary = [
                'pending' => $pending,
                'approved' => $approved,
                'rejected' => $rejected,
            ];
        }

        $forwardedRequests = [];
        if (Schema::hasTable('leave_application') && Schema::hasTable('tab1') && Schema::hasTable('leave_type')) {
            $forwardedRequests = DB::table('leave_application as la')
                ->join('tab1 as e', 'la.employee_id', '=', 'e.employee_id')
                ->join('leave_type as lt', 'la.leave_type_id', '=', 'lt.leave_type_id')
                ->whereRaw("LOWER(COALESCE(la.HoD_status, '')) = ?", ['forwarded'])
                ->whereRaw("LOWER(COALESCE(la.medical_superintendent_status, '')) = ?", ['pending'])
                ->orderByDesc('la.applied_at')
                ->select([
                    'la.application_id',
                    'e.employee_name',
                    'lt.leave_name as leave_type',
                    'la.from_date',
                    'la.to_date',
                    'la.total_days',
                    'la.hod_note',
                ])
                ->get()
                ->map(fn ($r) => (array) $r)
                ->toArray();
        }

        $directRequests = [];
        if (Schema::hasTable('leave_application') && Schema::hasTable('tab1') && Schema::hasTable('leave_type')) {
            $directRequests = DB::table('leave_application as la')
                ->join('tab1 as e', 'la.employee_id', '=', 'e.employee_id')
                ->join('leave_type as lt', 'la.leave_type_id', '=', 'lt.leave_type_id')
                ->where(function ($q): void {
                    $q->whereNull('la.HoD_status')
                        ->orWhere('la.HoD_status', '')
                        ->orWhereRaw('LOWER(la.HoD_status) = ?', ['skipped']);
                })
                ->whereRaw("LOWER(COALESCE(la.medical_superintendent_status, '')) = ?", ['pending'])
                ->orderByDesc('la.applied_at')
                ->select([
                    'la.application_id',
                    'e.employee_name',
                    'lt.leave_name as leave_type',
                    'la.from_date',
                    'la.to_date',
                    'la.total_days',
                    'la.reason',
                ])
                ->get()
                ->map(fn ($r) => (array) $r)
                ->toArray();
        }

        $recentDecisions = [];
        if (Schema::hasTable('leave_application') && Schema::hasTable('tab1') && Schema::hasTable('leave_type')) {
            $query = DB::table('leave_application as la')
                ->join('tab1 as e', 'la.employee_id', '=', 'e.employee_id')
                ->join('leave_type as lt', 'la.leave_type_id', '=', 'lt.leave_type_id')
                ->whereRaw("LOWER(COALESCE(la.medical_superintendent_status, '')) IN (?, ?)", ['approved', 'rejected'])
                ->orderByDesc('la.medical_superintendent_action_at')
                ->limit(5)
                ->select([
                    'e.employee_name',
                    'lt.leave_name',
                    'la.medical_superintendent_status',
                    'la.medical_superintendent_action_at',
                ]);

            if (Schema::hasColumn('leave_application', 'medical_superintendent_action_by')) {
                $query->where('la.medical_superintendent_action_by', $msUser['employee_id']);
            }

            $recentDecisions = $query->get()->map(fn ($r) => (array) $r)->toArray();
        }

        $onTourStaff = [];
        $onTourCount = 0;
        if (Schema::hasTable('tour_records') && Schema::hasTable('tab1')) {
            $today = now('Asia/Thimphu')->toDateString();
            $hasDepartmentTable = Schema::hasTable('department');

            $tourQuery = DB::table('tour_records as tr')
                ->join('tab1 as e', 'tr.employee_id', '=', 'e.employee_id')
                // include tours that have already started OR are scheduled to start in future
                ->where(function ($q) use ($today): void {
                    $q->whereDate('tr.start_date', '<=', $today)
                        ->orWhereDate('tr.start_date', '>=', $today);
                })
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
                'e.designation',
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

            try {
                \Illuminate\Support\Facades\Log::debug('MS onTour fetched', ['count' => $onTourCount]);
            } catch (\Throwable $e) {
                // ignore
            }
        }

        $totalStaff = 0;
        if (Schema::hasTable('tab1')) {
            $totalStaff = DB::table('tab1')->count();
        }

        // Adhoc requests count (support either table name)
        $adhocCount = 0;
        $adhocTable = null;
        if (Schema::hasTable('adhoc_requests')) {
            $adhocTable = 'adhoc_requests';
        } elseif (Schema::hasTable('adhoc_request')) {
            $adhocTable = 'adhoc_request';
        }
        if ($adhocTable) {
            $adhocCount = DB::table($adhocTable)->count();
        }

        return view('ms_dashboard', [
            'authorized' => true,
            'username' => ($msUser['employee_name'] ?: Auth::user()->name) && $msUser['authorized'] ? 'Medical Superintendent' : ($msUser['employee_name'] ?: Auth::user()->name),
            'summary' => $summary,
            'adhocCount' => $adhocCount,
            'forwardedRequests' => $forwardedRequests,
            'directRequests' => $directRequests,
            'recentDecisions' => $recentDecisions,
            'onTourCount' => $onTourCount,
            'onTourStaff' => $onTourStaff,
            'totalStaff' => $totalStaff,
        ]);
    }

    public function staffList(Request $request): \Illuminate\View\View|\Illuminate\Http\RedirectResponse
    {
        $msUser = $this->resolveMsUser($request);

        if (! $msUser['authorized']) {
            return redirect(route('dashboard'))->with('flash_error', 'Access denied. You are not assigned as Medical Superintendent.');
        }

        if (! Schema::hasTable('tab1')) {
            return view('ms_staff_list', ['staff' => [], 'authorized' => true, 'username' => $msUser['employee_name'] ?? Auth::user()->name, 'departments' => [], 'dept' => '']);
        }
        $dept = $request->input('department_id', '');

        $query = DB::table('tab1 as t')
            ->select('t.employee_id', 't.employee_name', 't.eid', 't.designation', 't.department_id');

        if (Schema::hasTable('department')) {
            $query->leftJoin('department as d', 't.department_id', '=', 'd.department_id')
                ->addSelect(DB::raw("COALESCE(d.department_name, '-') as department_name"));
        } else {
            $query->addSelect(DB::raw("'-' as department_name"));
        }

        if ($dept) {
            $query->where('t.department_id', $dept);
        }

        $staff = $query->orderBy('t.employee_name')->get()->map(fn($r) => (array) $r)->toArray();

        $departments = [];
        if (Schema::hasTable('department')) {
            $departments = DB::table('department')->select('department_id', 'department_name')->orderBy('department_name')->get();
        }

        return view('ms_staff_list', [
            'authorized' => true,
            'staff' => $staff,
            'username' => $msUser['employee_name'] ?? Auth::user()->name,
            'departments' => $departments,
            'dept' => $dept,
        ]);
    }

    public function pending(Request $request): \Illuminate\View\View|\Illuminate\Http\RedirectResponse
    {
        $msUser = $this->resolveMsUser($request);

        if (! $msUser['authorized']) {
            return redirect(route('dashboard'))->with('flash_error', 'Access denied. You are not assigned as Medical Superintendent.');
        }

        $forwardedRequests = [];
        $directRequests = [];

        if (Schema::hasTable('leave_application') && Schema::hasTable('tab1') && Schema::hasTable('leave_type')) {
            $forwardedRequests = DB::table('leave_application as la')
                ->join('tab1 as e', 'la.employee_id', '=', 'e.employee_id')
                ->join('leave_type as lt', 'la.leave_type_id', '=', 'lt.leave_type_id')
                ->whereRaw("LOWER(COALESCE(la.HoD_status, '')) = ?", ['forwarded'])
                ->whereRaw("LOWER(COALESCE(la.medical_superintendent_status, '')) = ?", ['pending'])
                ->orderByDesc('la.applied_at')
                ->select([
                    'la.application_id',
                    'e.employee_name',
                    'lt.leave_name as leave_type',
                    'la.from_date',
                    'la.to_date',
                    'la.total_days',
                    'la.hod_note',
                ])
                ->get()
                ->map(fn ($r) => (array) $r)
                ->toArray();

            $directRequests = DB::table('leave_application as la')
                ->join('tab1 as e', 'la.employee_id', '=', 'e.employee_id')
                ->join('leave_type as lt', 'la.leave_type_id', '=', 'lt.leave_type_id')
                ->where(function ($q): void {
                    $q->whereNull('la.HoD_status')
                        ->orWhere('la.HoD_status', '')
                        ->orWhereRaw('LOWER(la.HoD_status) = ?', ['skipped']);
                })
                ->whereRaw("LOWER(COALESCE(la.medical_superintendent_status, '')) = ?", ['pending'])
                ->orderByDesc('la.applied_at')
                ->select([
                    'la.application_id',
                    'e.employee_name',
                    'lt.leave_name as leave_type',
                    'la.from_date',
                    'la.to_date',
                    'la.total_days',
                    'la.reason',
                ])
                ->get()
                ->map(fn ($r) => (array) $r)
                ->toArray();
        }

        return view('ms_pending', [
            'authorized' => true,
            'username' => $msUser['employee_name'] ?? Auth::user()->name,
            'forwardedRequests' => $forwardedRequests,
            'directRequests' => $directRequests,
        ]);
    }

    public function onTourList(Request $request): \Illuminate\View\View|\Illuminate\Http\RedirectResponse
    {
        $msUser = $this->resolveMsUser($request);
        if (! $msUser['authorized']) {
            return redirect(route('dashboard'))->with('flash_error', 'Access denied. You are not assigned as Medical Superintendent.');
        }

        $dept = $request->input('department_id', '');

        $onTourStaff = [];
        if (Schema::hasTable('tour_records') && Schema::hasTable('tab1')) {
            $today = now('Asia/Thimphu')->toDateString();
            $hasDepartmentTable = Schema::hasTable('department');

            $tourQuery = DB::table('tour_records as tr')
                ->join('tab1 as e', 'tr.employee_id', '=', 'e.employee_id')
                ->where(function ($q) use ($today): void {
                    $q->whereDate('tr.start_date', '<=', $today)
                        ->orWhereDate('tr.start_date', '>=', $today);
                })
                ->where(function ($q) use ($today): void {
                    $q->whereNull('tr.end_date')
                        ->orWhereDate('tr.end_date', '>=', $today);
                });

            if ($dept) {
                $tourQuery->where('e.department_id', $dept);
            }

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
        }

        // also provide employees for the create form (filtered by dept if present)
        $employees = [];
        if (Schema::hasTable('tab1')) {
            $empQuery = DB::table('tab1')->select('employee_id','employee_name','department_id');
            if ($dept) {
                $empQuery->where('department_id', $dept);
            }
            $employees = $empQuery->orderBy('employee_name')->get();
        }

        return view('ms_on_tour', [
            'authorized' => true,
            'username' => $msUser['employee_name'] ?? Auth::user()->name,
            'onTourStaff' => $onTourStaff,
            'departments' => (Schema::hasTable('department') ? DB::table('department')->select('department_id','department_name')->orderBy('department_name')->get() : []),
            'dept' => $dept,
            'employees' => $employees,
        ]);
    }

    // Edit tour record form
    public function editTour(Request $request, $id)
    {
        $msUser = $this->resolveMsUser($request);
        if (! $msUser['authorized']) {
            return redirect(route('dashboard'))->with('flash_error', 'Access denied.');
        }

        if (! Schema::hasTable('tour_records')) {
            return redirect()->back()->with('flash_error', 'Tour records not available.');
        }

        // Use `tour_id` when present, otherwise fall back to `id` or `employee_id`.
        $record = DB::table('tour_records')
            ->when(Schema::hasColumn('tour_records', 'tour_id'), fn($q) => $q->where('tour_id', $id), fn($q) => $q->when(Schema::hasColumn('tour_records', 'id'), fn($qq) => $qq->where('id', $id), fn($qq) => $qq->where('employee_id', $id)))
            ->first();
        if (! $record) {
            return redirect()->route('ms.on_tour')->with('flash_error', 'Record not found');
        }

        $departments = Schema::hasTable('department') ? DB::table('department')->select('department_id','department_name')->orderBy('department_name')->get() : [];

        return view('ms_on_tour_edit', [
            'record' => (array) $record,
            'departments' => $departments,
        ]);
    }

    // Update tour record
    public function updateTour(Request $request, $id)
    {
        $msUser = $this->resolveMsUser($request);
        if (! $msUser['authorized']) {
            return redirect(route('dashboard'))->with('flash_error', 'Access denied.');
        }

        $data = $request->validate([
            'place' => 'nullable|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'purpose' => 'nullable|string',
        ]);

        DB::table('tour_records')
            ->when(Schema::hasColumn('tour_records', 'tour_id'), fn($q) => $q->where('tour_id', $id), fn($q) => $q->when(Schema::hasColumn('tour_records', 'id'), fn($qq) => $qq->where('id', $id), fn($qq) => $qq->where('employee_id', $id)))
            ->update($data + ['updated_at' => now()]);

        return redirect()->route('ms.on_tour')->with('flash_success', 'Tour record updated');
    }

    // Delete tour record
    public function deleteTour(Request $request, $id)
    {
        $msUser = $this->resolveMsUser($request);
        if (! $msUser['authorized']) {
            return redirect(route('dashboard'))->with('flash_error', 'Access denied.');
        }

        DB::table('tour_records')
            ->when(Schema::hasColumn('tour_records', 'tour_id'), fn($q) => $q->where('tour_id', $id), fn($q) => $q->when(Schema::hasColumn('tour_records', 'id'), fn($qq) => $qq->where('id', $id), fn($qq) => $qq->where('employee_id', $id)))
            ->delete();
        return redirect()->route('ms.on_tour')->with('flash_success', 'Tour record deleted');
    }

    // Store new tour record
    public function storeTour(Request $request)
    {
        $msUser = $this->resolveMsUser($request);
        if (! $msUser['authorized']) {
            return redirect(route('dashboard'))->with('flash_error', 'Access denied.');
        }

        $data = $request->validate([
            'employee_id' => 'required|integer',
            'place' => 'nullable|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'purpose' => 'nullable|string',
        ]);

        DB::table('tour_records')->insert(array_merge($data, ['created_at' => now(), 'updated_at' => now()]));

        return redirect()->route('ms.on_tour')->with('flash_success', 'Tour record added');
    }

    public function recentList(Request $request): \Illuminate\View\View|\Illuminate\Http\RedirectResponse
    {
        $msUser = $this->resolveMsUser($request);
        if (! $msUser['authorized']) {
            return redirect(route('dashboard'))->with('flash_error', 'Access denied. You are not assigned as Medical Superintendent.');
        }

        $recentDecisions = [];
        if (Schema::hasTable('leave_application') && Schema::hasTable('tab1') && Schema::hasTable('leave_type')) {
            $query = DB::table('leave_application as la')
                ->join('tab1 as e', 'la.employee_id', '=', 'e.employee_id')
                ->join('leave_type as lt', 'la.leave_type_id', '=', 'lt.leave_type_id')
                ->whereRaw("LOWER(COALESCE(la.medical_superintendent_status, '')) IN (?, ?)", ['approved', 'rejected'])
                ->orderByDesc('la.medical_superintendent_action_at')
                ->limit(50)
                ->select([
                    'e.employee_name',
                    'lt.leave_name',
                    'la.medical_superintendent_status',
                    'la.medical_superintendent_action_at',
                ]);

            if (Schema::hasColumn('leave_application', 'medical_superintendent_action_by')) {
                $query->where('la.medical_superintendent_action_by', $msUser['employee_id']);
            }

            $recentDecisions = $query->get()->map(fn ($r) => (array) $r)->toArray();
        }

        return view('ms_recent', [
            'authorized' => true,
            'username' => $msUser['employee_name'] ?? Auth::user()->name,
            'recentDecisions' => $recentDecisions,
        ]);
    }

    public function processAction(Request $request): RedirectResponse
    {
        $msUser = $this->resolveMsUser($request);

        if (! $msUser['authorized']) {
            return redirect()->route('dashboard')->with('flash_error', 'Access denied. You are not assigned as MS.');
        }

        if (! Schema::hasTable('leave_application')) {
            return back()->with('message', 'Leave application table not found.');
        }

        $payload = $request->validate([
            'request_id' => ['nullable', 'integer'],
            'application_id' => ['nullable', 'integer'],
            'action' => ['required', 'string'],
        ]);

        $applicationId = (int) ($payload['request_id'] ?? $payload['application_id'] ?? 0);
        if ($applicationId <= 0) {
            return redirect()->route('ms.dashboard');
        }

        $action = strtolower(trim($payload['action']));
        $newStatus = ($action === 'approve' || $action === 'approved') ? 'approved' : 'rejected';

        $update = ['medical_superintendent_status' => $newStatus];
        if (Schema::hasColumn('leave_application', 'medical_superintendent_action_by')) {
            $update['medical_superintendent_action_by'] = $msUser['employee_id'];
        }
        if (Schema::hasColumn('leave_application', 'medical_superintendent_action_at')) {
            $update['medical_superintendent_action_at'] = now('Asia/Thimphu')->toDateTimeString();
        }

        DB::table('leave_application')
            ->where('application_id', $applicationId)
            ->update($this->filterColumns('leave_application', $update));

        $leave = DB::table('leave_application')
            ->where('application_id', $applicationId)
            ->select('employee_id', 'leave_type_id', 'from_date')
            ->first();

        if ($leave && Schema::hasTable('leave_balance')) {
            $year = Carbon::parse($leave->from_date)->year;
            $balance = DB::table('leave_balance')
                ->where('employee_id', $leave->employee_id)
                ->where('leave_type_id', $leave->leave_type_id)
                ->where('year', $year);

            $balanceColumns = Schema::getColumnListing('leave_balance');

            if (in_array('used_leave', $balanceColumns, true)) {
                // Recompute used leave from approved applications to prevent over-deduction.
                $approvedDays = (float) (DB::table('leave_application')
                    ->where('employee_id', $leave->employee_id)
                    ->where('leave_type_id', $leave->leave_type_id)
                    ->whereYear('from_date', $year)
                    ->whereRaw("LOWER(COALESCE(medical_superintendent_status, '')) = ?", ['approved'])
                    ->sum('total_days') ?? 0);

                $balance->update([
                    'used_leave' => DB::raw('LEAST(COALESCE(max_leave_per_year, 0), ' . $approvedDays . ')'),
                ]);
            }
        }

        // Prefer returning to the same page the MS was on. If no referrer, fall back to MS dashboard.
        $response = redirect()->back()->with('message', 'Request processed: ' . ucfirst($newStatus));
        if (url()->previous() === url()->current()) {
            // No previous URL available (or same), fallback to dashboard
            $response = redirect()->route('ms.dashboard')->with('message', 'Request processed: ' . ucfirst($newStatus));
        }

        return $response;
    }

    private function resolveMsUser(Request $request): array
    {
        $user = Auth::user();
        $eid = (string) $request->session()->get('eid', $user->email);

        if (! Schema::hasTable('tab1')) {
            return ['authorized' => false, 'employee_id' => null, 'employee_name' => $user->name];
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
        }

        $roleName = strtolower(trim((string) ($row->role_name ?? '')));
        $isMs = $roleName === 'ms' || str_contains($roleName, 'medical') || str_contains($roleName, 'superintendent');

        return [
            'authorized' => $isMs,
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
