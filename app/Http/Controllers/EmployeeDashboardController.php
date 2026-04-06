<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class EmployeeDashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::user();
        $now = now('Asia/Thimphu');

        $employee = $this->resolveEmployee($request, $user->id, $user->name, $user->email);
        $employeeId = $employee['employee_id'] ?? null;
        $departmentId = $employee['department_id'] ?? null;

        $hodName = 'HoD';
        if ($employeeId && Schema::hasTable('department_hod') && Schema::hasTable('tab1')) {
            $hod = DB::table('department_hod as dh')
                ->join('tab1 as e', 'dh.employee_id', '=', 'e.employee_id')
                ->where('dh.department_id', $departmentId)
                ->select('e.employee_name')
                ->first();
            $hodName = $hod->employee_name ?? 'HoD';
        }

        $leaveTypes = [];
        if (Schema::hasTable('leave_type')) {
            $leaveTypes = DB::table('leave_type')
                ->select('leave_type_id', 'leave_name')
                ->where('status', 'active')
                ->orderBy('leave_name')
                ->get()
                ->map(fn ($r) => ['leave_type_id' => (int) $r->leave_type_id, 'leave_name' => $r->leave_name])
                ->toArray();
        }

        $leaveBalances = [];
        if ($employeeId && Schema::hasTable('leave_balance')) {
            $rows = DB::table('leave_balance')
                ->select('leave_type_id', 'remaining_leave')
                ->where('employee_id', $employeeId)
                ->where('year', $now->year)
                ->get();

            foreach ($rows as $row) {
                $leaveBalances[(int) $row->leave_type_id] = $row->remaining_leave;
            }
        }

        $leaveApplications = [];
        if ($employeeId && Schema::hasTable('leave_application') && Schema::hasTable('leave_type')) {
            $leaveApplications = DB::table('leave_application as la')
                ->join('leave_type as lt', 'la.leave_type_id', '=', 'lt.leave_type_id')
                ->where('la.employee_id', $employeeId)
                ->orderByDesc('la.applied_at')
                ->select([
                    'la.application_id',
                    'lt.leave_name as type',
                    'la.from_date as start_date',
                    'la.to_date as end_date',
                    'la.reason',
                    'la.total_days as days',
                    'la.HoD_status as hod_status',
                    'la.medical_superintendent_status as ms_status',
                ])
                ->get()
                ->map(fn ($r) => (array) $r)
                ->toArray();
        }

        $attendanceToday = [];
        if ($employeeId && Schema::hasTable('attendance')) {
            $att = DB::table('attendance')
                ->where('employee_id', $employeeId)
                ->whereDate('attendance_date', $now->toDateString())
                ->first();
            $attendanceToday = $att ? (array) $att : [];

            if (! empty($attendanceToday)) {
                $attendanceToday['checkin_address'] = $this->displayAddressForUI($attendanceToday['checkin_address'] ?? '');
                $attendanceToday['checkout_address'] = $this->displayAddressForUI($attendanceToday['checkout_address'] ?? '');
            }
        }

        $hasMorning = ! empty($attendanceToday['checkin']);
        $hasEvening = ! empty($attendanceToday['checkout']);

        $monthly = ['days' => 0, 'morning' => 0, 'evening' => 0];
        if ($employeeId && Schema::hasTable('attendance')) {
            $monthStart = $now->copy()->startOfMonth()->toDateString();
            $monthEnd = $now->copy()->endOfMonth()->toDateString();

            $sum = DB::table('attendance')
                ->where('employee_id', $employeeId)
                ->whereBetween('attendance_date', [$monthStart, $monthEnd])
                ->selectRaw('COUNT(*) AS days, SUM(CASE WHEN checkin IS NOT NULL THEN 1 ELSE 0 END) AS morning, SUM(CASE WHEN checkout IS NOT NULL THEN 1 ELSE 0 END) AS evening')
                ->first();

            $monthly['days'] = (int) ($sum->days ?? 0);
            $monthly['morning'] = (int) ($sum->morning ?? 0);
            $monthly['evening'] = (int) ($sum->evening ?? 0);
        }

        $notifications = [];
        if (! $hasMorning) {
            $notifications[] = 'Reminder: Please check-in.';
        }
        if ($hasMorning && ! $hasEvening) {
            $notifications[] = 'Reminder: Your check-out is pending.';
        }

        return view('dashboard', [
            'employee' => $employee,
            'attendanceToday' => $attendanceToday,
            'hasMorning' => $hasMorning,
            'hasEvening' => $hasEvening,
            'monthly' => $monthly,
            'notifications' => $notifications,
            'leaveTypes' => $leaveTypes,
            'leaveBalances' => $leaveBalances,
            'leaveApplications' => $leaveApplications,
            'hodName' => $hodName,
            'departmentId' => (int) ($departmentId ?? 0),
        ]);
    }

    public function tour(Request $request): View
    {
        $user = Auth::user();
        $employee = $this->resolveEmployee($request, $user->id, $user->name, $user->email);
        $employeeId = $employee['employee_id'] ?? null;

        return view('tour', [
            'employee' => $employee,
            'tourRecords' => $this->loadTourRecords($employee, $employeeId, $user->id),
        ]);
    }

    public function submitTour(Request $request): RedirectResponse
    {
        if (! Schema::hasTable('tour_records')) {
            return back()->with('flash_error', 'Tour records table not found.');
        }

        $payload = $request->validate([
            'place' => ['required', 'string', 'max:255'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'purpose' => ['required', 'string', 'max:1000'],
            'office_order_pdf' => ['nullable', 'file', 'mimes:pdf', 'max:5120'],
        ]);

        $user = Auth::user();
        $employee = $this->resolveEmployee($request, $user->id, $user->name, $user->email);

        // Ensure we have a numeric tab1.employee_id matching the employee
        $employeeId = $employee['employee_id'] ?? null;
        if (empty($employeeId) && Schema::hasTable('tab1')) {
            $candidate = DB::table('tab1')
                ->where(function ($q) use ($user, $employee) {
                    $q->where('eid', $user->email);
                    if (! empty($employee['eid'])) {
                        $q->orWhere('eid', $employee['eid']);
                    }
                    $q->orWhere('employee_id', $user->id);
                })
                ->select('employee_id', 'department_id')
                ->first();

            if ($candidate) {
                $employeeId = $candidate->employee_id;
                $employee['department_id'] = $candidate->department_id ?? $employee['department_id'] ?? null;
            }
        }

        // Fallback to users.id only if no tab1 mapping found — but prefer null so queries that join tab1 don't match incorrect ids
        if (empty($employeeId)) {
            $employeeId = null;
        }
        $start = Carbon::parse($payload['start_date']);
        $end = Carbon::parse($payload['end_date']);

        $tzNow = now('Asia/Thimphu')->startOfDay();
        if ($start->lt($tzNow) || $end->lt($tzNow)) {
            return back()->with('flash_error', 'Tour dates cannot be in the past.');
        }

        $totalDays = $start->diffInDays($end) + 1;

        // Prevent overlapping tour submissions for the same employee and dates
        $overlapExists = DB::table('tour_records')
            ->where('employee_id', $employeeId)
            ->whereRaw('NOT (end_date < ? OR start_date > ?)', [$start->toDateString(), $end->toDateString()])
            ->exists();

        if ($overlapExists) {
            return back()->with('flash_error', 'Tour overlaps an existing tour for the selected dates.');
        }

        $pdfPath = null;
        if ($request->hasFile('office_order_pdf')) {
            $file = $request->file('office_order_pdf');
            $filename = 'office_order_' . $employeeId . '_' . time() . '.' . $file->getClientOriginalExtension();
            $pdfPath = $file->storeAs('office_orders', $filename, 'public');
        }

        $insert = [
            'employee_id' => $employeeId,
            'department_id' => $employee['department_id'] ?? null,
            'place' => $payload['place'],
            'start_date' => $payload['start_date'],
            'end_date' => $payload['end_date'],
            'total_days' => $totalDays,
            'purpose' => $payload['purpose'],
            'office_order_pdf' => $pdfPath,
            'created_at' => now('Asia/Thimphu')->toDateTimeString(),
            'updated_at' => now('Asia/Thimphu')->toDateTimeString(),
        ];

        $insertData = $this->filterColumns('tour_records', $insert);
        if (empty($insertData)) {
            return back()->with('flash_error', 'Tour records table columns do not match expected fields.');
        }

        // Log insert for debugging if available
        try {
            \Illuminate\Support\Facades\Log::debug('submitTour inserting', ['insert' => $insertData]);
        } catch (\Throwable $e) {
            // ignore
        }

        DB::table('tour_records')->insert($insertData);

        return redirect()->route('dashboard.tour')->with('flash_success', 'Tour record saved successfully.');
    }

    private function loadTourRecords(array $employee, ?int $employeeId, int $userId): array
    {
        $tourRecords = [];

        if (! Schema::hasTable('tour_records')) {
            return $tourRecords;
        }

        $tourColumns = Schema::getColumnListing('tour_records');
        $tourQuery = DB::table('tour_records');

        if ($employeeId && in_array('employee_id', $tourColumns, true)) {
            $tourQuery->where('employee_id', $employeeId);
        } elseif (! empty($employee['eid']) && in_array('eid', $tourColumns, true)) {
            $tourQuery->where('eid', $employee['eid']);
        } elseif (in_array('user_id', $tourColumns, true)) {
            $tourQuery->where('user_id', $userId);
        }

        if (in_array('start_date', $tourColumns, true)) {
            $tourQuery->orderByDesc('start_date');
        } elseif (in_array('created_at', $tourColumns, true)) {
            $tourQuery->orderByDesc('created_at');
        } elseif (in_array('tour_id', $tourColumns, true)) {
            $tourQuery->orderByDesc('tour_id');
        } elseif (in_array('id', $tourColumns, true)) {
            $tourQuery->orderByDesc('id');
        }

        $tourRows = $tourQuery
            ->limit(50)
            ->get()
            ->map(fn ($r) => (array) $r)
            ->toArray();

        foreach ($tourRows as $row) {
            $startDate = $row['start_date'] ?? $row['tour_date'] ?? $row['date'] ?? $row['from_date'] ?? null;
            $endDate = $row['to_date'] ?? $row['end_date'] ?? null;
            $totalDays = $row['total_days'] ?? null;

            if (($totalDays === null || $totalDays === '') && ! empty($startDate) && ! empty($endDate)) {
                try {
                    $totalDays = Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate)) + 1;
                } catch (\Throwable $e) {
                    $totalDays = '-';
                }
            }

            $tourRecords[] = [
                'date' => $startDate ?? '-',
                'to_date' => $endDate ?? '-',
                'destination' => $row['place'] ?? $row['destination'] ?? $row['location'] ?? '-',
                'purpose' => $row['purpose'] ?? $row['reason'] ?? $row['remarks'] ?? '-',
                'total_days' => $totalDays ?? '-',
                'office_order_pdf' => $row['office_order_pdf'] ?? null,
            ];
        }

        return $tourRecords;
    }

    public function uploadProfilePicture(Request $request): RedirectResponse
    {
        $request->validate([
            'profile_picture' => ['required', 'image', 'mimes:jpeg,png,gif,webp', 'max:2048'],
        ]);

        $user = Auth::user();

        if ($user->profile_picture) {
            $oldPath = public_path('profile_pictures/' . $user->profile_picture);
            if (file_exists($oldPath)) {
                unlink($oldPath);
            }
        }

        $file = $request->file('profile_picture');
        $filename = $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();

        $dir = public_path('profile_pictures');
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $file->move($dir, $filename);

        DB::table('users')->where('id', $user->id)->update(['profile_picture' => $filename]);

        return back()->with('profile_success', 'Profile picture updated.');
    }

    public function attendance(Request $request): RedirectResponse
    {
        $request->validate([
            'action' => ['required', 'in:checkin,checkout'],
            'lat' => ['nullable', 'numeric'],
            'lon' => ['nullable', 'numeric'],
        ]);

        if (! Schema::hasTable('attendance')) {
            return back()->with('flash_error', 'Attendance table not found.');
        }

        $user = Auth::user();
        $employee = $this->resolveEmployee($request, $user->id, $user->name, $user->email);
        $employeeId = $employee['employee_id'] ?? $user->id;

        
        $departmentId = (int) ($employee['department_id'] ?? 0);
        $tzNow = now('Asia/Thimphu');
        $time = $tzNow->format('H:i:s');
        $today = $tzNow->toDateString();
        $lat = $request->input('lat');
        $lon = $request->input('lon');
        Log::debug('Attendance action', ['action' => $request->input('action'), 'lat' => $lat, 'lon' => $lon]);
        // Prefer immediate reverse-geocoding when lat/lon are available so
        // the stored address is human-readable. If the lookup fails, fall
        // back to storing the Nominatim reverse URL so the UI can resolve
        // it later.
        if (is_numeric($lat) && is_numeric($lon)) {
            $resolved = $this->reverseGeocode($lat, $lon);
            if (trim($resolved) !== '') {
                $safeAddress = $resolved;
            } else {
                $safeAddress = 'https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=' . rawurlencode($lat) . '&lon=' . rawurlencode($lon);
            }
        } else {
            $address = $this->reverseGeocode($lat, $lon);
            if (trim($address) !== '') {
                $safeAddress = $address;
            } else {
                $safeAddress = 'Location unavailable';
            }
        }

        $todayRow = DB::table('attendance')
            ->where('employee_id', $employeeId)
            ->whereDate('attendance_date', $today)
            ->first();

        $normalDepartments = [1, 2, 4, 5, 6];
        $isNormal = in_array($departmentId, $normalDepartments, true);

            if ($request->input('action') === 'checkin') {
                if ($todayRow) {
                    return back()->with('flash_error', 'Already checked in.');
                }

                // If checking in after 09:15, late reason is mandatory only for normal departments
                $isLateByCutoff = $time > '09:15:00';
                $lateReasonInput = trim((string) $request->input('late_reason', ''));
                if ($isLateByCutoff && $isNormal && $lateReasonInput === '') {
                    return back()->with('flash_error', 'Late reason is required for check-in after 09:15 AM.');
                }

            $shift = null;
            if ($departmentId === 3) {
                if ($time >= '08:00:00' && $time < '14:00:00') {
                    $shift = 'morning';
                } elseif ($time >= '14:00:00' && $time < '20:00:00') {
                    $shift = 'evening';
                } else {
                    $shift = 'night';
                }
            }

            $insert = [
                'employee_id' => $employeeId,
                'attendance_date' => $today,
                'checkin' => $tzNow->toDateTimeString(),
                'checkin_address' => $safeAddress,
                'checkin_status' => $isNormal ? ($time <= '09:15:00' ? 'On Time' : 'Late') : null,
                'shift_type' => $shift,
                    'late_reason' => $lateReasonInput ?: null,
                    // persist computed remarks (uses same logic as admin UI)
                    'remarks' => $this->computeRemarksForEmployeeDate($employeeId, $today, true, false),
                    ];

            $insertData = $this->filterColumns('attendance', $insert);
            if (empty($insertData)) {
                return back()->with('flash_error', 'Attendance table columns do not match expected fields.');
            }
            DB::table('attendance')->insert($insertData);

            return back()->with('flash_success', 'Checked in successfully.');
        }

        if (! $todayRow) {
            return back()->with('flash_error', 'Check-in required first.');
        }
        if (! empty($todayRow->checkout)) {
            return back()->with('flash_error', 'Already checked out.');
        }
        if ($isNormal && $time < '14:55:00') {
            return back()->with('flash_error', 'Checkout allowed after 2:55 PM.');
        }

        $update = [
            'checkout' => $tzNow->toDateTimeString(),
            'checkout_address' => $safeAddress,
            // Ensure DB NOT NULL constraint satisfied by always setting a valid status
            'checkout_status' => 'completed',
            // checkout implies both checkin+checkout => present
            'remarks' => 'Present',
        ];
        $updateData = $this->filterColumns('attendance', $update);
        if (empty($updateData)) {
            return back()->with('flash_error', 'Attendance table columns do not match expected fields.');
        }

        $query = DB::table('attendance')
            ->where('employee_id', $employeeId)
            ->whereDate('attendance_date', $today);
        if (isset($todayRow->attendance_id)) {
            $query->where('attendance_id', $todayRow->attendance_id);
        }
        $query->update($updateData);

        return back()->with('flash_success', 'Checked out successfully.');
    }

    public function attendanceSummary(Request $request): View
    {
        $user = Auth::user();
        $employee = $this->resolveEmployee($request, $user->id, $user->name, $user->email);
        $employeeId = $employee['employee_id'] ?? $user->id;

        if (! Schema::hasTable('attendance')) {
            return view('dashboard_attendance', ['rows' => []]);
        }

        $query = DB::table('attendance')
            ->where('employee_id', $employeeId)
            ->orderByDesc('attendance_date')
            ->limit(500);

        $rows = $query->get()->map(fn($r) => (array) $r)->toArray();

        return view('dashboard_attendance', [
            'employee' => $employee,
            'rows' => $rows,
        ]);
    }

    public function leaveForm(Request $request): View
    {
        $user = Auth::user();
        $employee = $this->resolveEmployee($request, $user->id, $user->name, $user->email);
        $employeeId = $employee['employee_id'] ?? $user->id;

        $leaveTypes = [];
        if (Schema::hasTable('leave_type')) {
            $leaveTypes = DB::table('leave_type')
                ->select('leave_type_id', 'leave_name')
                ->where('status', 'active')
                ->orderBy('leave_name')
                ->get()
                ->map(fn ($r) => ['leave_type_id' => (int) $r->leave_type_id, 'leave_name' => $r->leave_name])
                ->toArray();
        }

        $leaveBalances = [];
        if ($employeeId && Schema::hasTable('leave_balance')) {
            $now = now('Asia/Thimphu');
            $rows = DB::table('leave_balance')
                ->select('leave_type_id', 'remaining_leave')
                ->where('employee_id', $employeeId)
                ->where('year', $now->year)
                ->get();

            foreach ($rows as $row) {
                $leaveBalances[(int) $row->leave_type_id] = $row->remaining_leave;
            }
        }

        $leaveApplications = [];
        if ($employeeId && Schema::hasTable('leave_application') && Schema::hasTable('leave_type')) {
            $leaveApplications = DB::table('leave_application as la')
                ->join('leave_type as lt', 'la.leave_type_id', '=', 'lt.leave_type_id')
                ->where('la.employee_id', $employeeId)
                ->orderByDesc('la.applied_at')
                ->select([
                    'la.application_id',
                    'lt.leave_name as type',
                    'la.from_date as start_date',
                    'la.to_date as end_date',
                    'la.reason',
                    'la.total_days as days',
                    'la.HoD_status as hod_status',
                    'la.medical_superintendent_status as ms_status',
                ])
                ->get()
                ->map(fn ($r) => (array) $r)
                ->toArray();
        }

        return view('leave_form', [
            'employee' => $employee,
            'leaveTypes' => $leaveTypes,
            'leaveBalances' => $leaveBalances,
            'leaveApplications' => $leaveApplications,
        ]);
    }

    public function adhocRequests(Request $request): View
    {
        $user = Auth::user();
        $employee = $this->resolveEmployee($request, $user->id, $user->name, $user->email);
        $employeeId = $employee['employee_id'] ?? $user->id;

        // detect adhoc table name
        $table = null;
        if (Schema::hasTable('adhoc_requests')) {
            $table = 'adhoc_requests';
        } elseif (Schema::hasTable('adhoc_request')) {
            $table = 'adhoc_request';
        }

        $rows = [];
        if ($table) {
            $cols = Schema::getColumnListing($table);
            $q = DB::table($table);
            // Match any identifier present in the adhoc table so the
            // employee sees their requests regardless of which column was used
            // when the request was created (employee_id, eid, or user_id).
            $q->where(function ($sub) use ($cols, $employeeId, $employee, $user) {
                $matched = false;
                if (! empty($employeeId) && in_array('employee_id', $cols, true)) {
                    $sub->where('employee_id', $employeeId);
                    $matched = true;
                }
                if (! empty($employee['eid']) && in_array('eid', $cols, true)) {
                    if ($matched) {
                        $sub->orWhere('eid', $employee['eid']);
                    } else {
                        $sub->where('eid', $employee['eid']);
                        $matched = true;
                    }
                }
                if (in_array('user_id', $cols, true)) {
                    if ($matched) {
                        $sub->orWhere('user_id', $user->id);
                    } else {
                        $sub->where('user_id', $user->id);
                    }
                }
            });

            if (in_array('created_at', $cols, true)) {
                $q->orderByDesc('created_at');
            } elseif (in_array('id', $cols, true)) {
                $q->orderByDesc('id');
            }

            try {
                $rows = $q->limit(200)->get()->map(fn($r) => (array) $r)->toArray();
            } catch (\Throwable $e) {
                logger()->error('EmployeeDashboardController adhoc query failed', ['error' => $e->getMessage(), 'table' => $table]);
                $rows = [];
            }

            // Diagnostic logging to help debug empty results in HTTP context
            try {
                // Runtime DB name and full table count (HTTP context)
                $dbInfo = DB::select('select database() as db');
                $runtimeCount = DB::table($table)->count();

                $sql = (string) $q->toSql();
                $bindings = $q->getBindings();
                logger()->debug('EmployeeDashboardController adhoc debug', [
                    'table' => $table,
                    'cols' => $cols,
                    'employeeId' => $employeeId,
                    'employee_eid' => $employee['eid'] ?? null,
                    'user_id' => $user->id,
                    'sql' => $sql,
                    'bindings' => $bindings,
                    'rows_count' => count($rows),
                    'runtime_table_count' => $runtimeCount,
                    'runtime_database' => $dbInfo[0]->db ?? null,
                ]);
            } catch (\Throwable $e) {
                logger()->warning('EmployeeDashboardController adhoc logging failed', ['error' => $e->getMessage()]);
            }
        }

        return view('adhoc_requests', [
            'employee' => $employee,
            'rows' => $rows,
            'tableExists' => (bool) $table,
        ]);
    }

    public function submitAdhocRequest(Request $request): RedirectResponse
    {
        if (! Schema::hasTable('adhoc_request') && ! Schema::hasTable('adhoc_requests')) {
            return back()->with('flash_error', 'Adhoc requests table not found.');
        }

        $table = Schema::hasTable('adhoc_request') ? 'adhoc_request' : 'adhoc_requests';

        $payload = $request->validate([
            'date' => ['required', 'date', 'after_or_equal:today'],
            'purpose' => ['required', 'in:meeting,emergency'],
            'remarks' => ['nullable', 'string', 'max:255'],
        ]);

        $user = Auth::user();
        $employee = $this->resolveEmployee($request, $user->id, $user->name, $user->email);
        $employeeId = $employee['employee_id'] ?? $user->id;

        // Normalize date to YYYY-MM-DD for reliable comparison
        try {
            $canonicalDate = \Illuminate\Support\Carbon::parse($payload['date'])->toDateString();
        } catch (\Throwable $e) {
            $canonicalDate = $payload['date'];
        }

        // Prevent duplicate adhoc requests on the same date for the same employee
        $cols = Schema::getColumnListing($table);
        $existQuery = DB::table($table)->whereRaw("DATE(`date`) = ?", [$canonicalDate]);
        if ($employeeId && in_array('employee_id', $cols, true)) {
            $existQuery->where('employee_id', $employeeId);
        } elseif (! empty($employee['eid']) && in_array('eid', $cols, true)) {
            $existQuery->where('eid', $employee['eid']);
        } elseif (in_array('user_id', $cols, true)) {
            $existQuery->where('user_id', $user->id);
        }

        if ($existQuery->exists()) {
            return back()->with('flash_error', 'An adhoc request already exists for the selected date.');
        }

        $insert = [
            'employee_id' => $employeeId,
            'date' => $canonicalDate,
            'purpose' => $payload['purpose'],
            'remarks' => $payload['remarks'] ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        $insertData = $this->filterColumns($table, $insert);
        if (empty($insertData)) {
            return back()->with('flash_error', 'Adhoc requests table columns do not match expected fields.');
        }

        DB::table($table)->insert($insertData);

        return redirect()->route('dashboard.adhoc_requests')->with('flash_success', 'Adhoc request submitted.');
    }

    public function submitLeave(Request $request): RedirectResponse
    {
        if (! Schema::hasTable('leave_application')) {
            return back()->with('flash_error', 'Leave application table not found.');
        }

        $payload = $request->validate([
            'leave_type_id' => ['required', 'integer'],
            'submit_to' => ['required', 'in:hod,ms'],
            'from_date' => ['required', 'date'],
            'to_date' => ['required', 'date', 'after_or_equal:from_date'],
            'reason' => ['required', 'string', 'max:1000'],
        ]);

        $user = Auth::user();
        $employee = $this->resolveEmployee($request, $user->id, $user->name, $user->email);
        $employeeId = $employee['employee_id'] ?? $user->id;

        $from = Carbon::parse($payload['from_date']);
        $to = Carbon::parse($payload['to_date']);

        $tzNow = now('Asia/Thimphu')->startOfDay();
        if ($from->lt($tzNow) || $to->lt($tzNow)) {
            return back()->with('flash_error', 'Leave dates cannot be in the past.');
        }

        $totalDaysInput = trim((string) $request->input('total_days', ''));
        if ($totalDaysInput !== '') {
            $normalized = str_replace(',', '.', $totalDaysInput);
            if (! is_numeric($normalized) || (float) $normalized <= 0) {
                return back()->with('flash_error', 'Total days must be a positive number (e.g. 1 or 0.5).');
            }
            $days = (float) $normalized;
        } else {
            $days = $from->diffInDays($to) + 1;
        }

        // Prevent overlapping leave submissions for the same employee and dates
        $overlapExists = DB::table('leave_application')
            ->where('employee_id', $employeeId)
            ->whereRaw('NOT (to_date < ? OR from_date > ?)', [$from->toDateString(), $to->toDateString()])
            ->exists();

        if ($overlapExists) {
            return back()->with('flash_error', 'Leave overlaps an existing leave for the selected dates.');
        }

        $submitTo = strtolower((string) $payload['submit_to']);
        $hodStatus = $submitTo === 'hod' ? 'pending' : null;
        $msStatus = 'pending';

        $insert = [
            'employee_id' => $employeeId,
            'leave_type_id' => $payload['leave_type_id'],
            'from_date' => $payload['from_date'],
            'to_date' => $payload['to_date'],
            'total_days' => $days,
            'reason' => $payload['reason'],
            'HoD_status' => $hodStatus,
            'HoD_action_by' => null,
            'HoD_action_at' => null,
            'medical_superintendent_status' => $msStatus,
            'medical_superintendent_action_by' => null,
            'medical_superintendent_action_at' => null,
            'applied_at' => now('Asia/Thimphu')->toDateTimeString(),
            'created_at' => now(),
            'updated_at' => now(),
        ];

        $insertData = $this->filterColumns('leave_application', $insert);
        if (empty($insertData)) {
            return back()->with('flash_error', 'Leave table columns do not match expected fields.');
        }
        DB::table('leave_application')->insert($insertData);

        return redirect()->route('dashboard.leave_form')
            ->with('flash_success', 'Leave submitted successfully.');
    }

    private function resolveEmployee(Request $request, int $userId, string $name, string $email): array
    {
        if (! Schema::hasTable('tab1')) {
            return [
                'employee_id' => $userId,
                'employee_name' => $name,
                'eid' => $email,
                'department_id' => null,
                'designation' => 'Employee',
                'status' => 'active',
                'department_name' => '-',
                'role_name' => '-',
            ];
        }

        $eid = $request->session()->get('eid', $email);

        $query = DB::table('tab1 as t')
            ->select([
                't.employee_id',
                't.employee_name',
                't.eid',
                't.department_id',
                't.designation',
                't.status',
            ]);

        if (Schema::hasTable('department')) {
            $query->leftJoin('department as d', 'd.department_id', '=', 't.department_id')
                ->addSelect('d.department_name');
        }

        if (Schema::hasTable('role')) {
            $query->leftJoin('role as r', 'r.role_id', '=', 't.role_id')
                ->addSelect('r.role_name');
        }

        $row = $query->where('t.eid', $eid)->first();
        if (! $row) {
            $row = $query->where('t.employee_id', $userId)->first();
        }

        if (! $row) {
            return [
                'employee_id' => $userId,
                'employee_name' => $name,
                'eid' => $eid,
                'department_id' => null,
                'designation' => 'Employee',
                'status' => 'active',
                'department_name' => '-',
                'role_name' => '-',
            ];
        }

        return (array) $row;
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

    private function reverseGeocode($lat, $lon): string
    {
        if (! is_numeric($lat) || ! is_numeric($lon)) {
            return '';
        }

        try {
            Log::debug('reverseGeocode: Calling Nominatim', ['lat' => $lat, 'lon' => $lon]);
            $response = Http::withoutVerifying()
                ->withHeaders([
                    'User-Agent' => 'EmployeeAttendanceSystem/1.0',
                ])->timeout(10)->get('https://nominatim.openstreetmap.org/reverse', [
                    'format' => 'jsonv2',
                    'addressdetails' => 1,
                    'lat' => $lat,
                    'lon' => $lon,
                ]);

            Log::debug('reverseGeocode: Response status', ['status' => $response->status()]);

            if (! $response->successful()) {
                Log::warning('reverseGeocode: API returned error', ['status' => $response->status(), 'body' => $response->body()]);
                return '';
            }

            $data = $response->json();
            Log::debug('reverseGeocode: Response data', ['data' => $data]);

            if (! empty($data['address'])) {
                $address = $data['address'];

                $building =
                    $data['name']
                    ?? $address['hospital']
                    ?? $address['amenity']
                    ?? $address['building']
                    ?? '';

                $road = $address['road'] ?? '';

                $city =
                    $address['city']
                    ?? $address['town']
                    ?? $address['village']
                    ?? $address['county']
                    ?? '';

                $country = $address['country'] ?? '';

                $parts = array_filter([$building, $road, $city, $country]);

                $result = implode(', ', $parts);
                Log::debug('reverseGeocode: Success', ['result' => $result]);
                return $result;
            }
            
            Log::warning('reverseGeocode: No address in response', ['data' => $data]);
        } catch (\Exception $e) {
            Log::error('reverseGeocode: Exception', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return '';
        }

        return '';
    }

    private function displayAddressForUI($stored): string
    {
        $stored = trim((string) $stored);

        if ($stored === '') {
            return '';
        }

        if (str_contains($stored, 'nominatim.openstreetmap.org/reverse')) {
            $parts = parse_url($stored);

            if (! empty($parts['query'])) {
                parse_str($parts['query'], $query);

                if (! empty($query['lat']) && ! empty($query['lon'])) {
                    $name = $this->reverseGeocode($query['lat'], $query['lon']);

                    return $name ?: $stored;
                }
            }
        }

        return $stored;
    }

    /**
     * Compute a remarks string for a given employee and date using the
     * same heuristics as the admin UI (adhoc, leave, tour, present, bunking).
     */
    private function computeRemarksForEmployeeDate($employeeId, $rowDate, $hasCheckin = false, $hasCheckout = false): ?string
    {
        // Both checkin and checkout => Present
        if (! empty($hasCheckin) && ! empty($hasCheckout)) {
            return 'Present';
        }

        // Checked-in but no checkout => look for adhoc requests
        if (! empty($hasCheckin) && empty($hasCheckout)) {
            $adhocTable = Schema::hasTable('adhoc_requests') ? 'adhoc_requests' : (Schema::hasTable('adhoc_request') ? 'adhoc_request' : null);
            if ($adhocTable) {
                $adhocQuery = DB::table($adhocTable)
                    ->where(function ($q) use ($employeeId, $rowDate) {
                        if (! empty($employeeId)) {
                            $q->where('employee_id', $employeeId);
                        }
                        $q->whereDate('date', $rowDate);
                    });

                if (Schema::hasColumn($adhocTable, 'id')) {
                    $adhocQuery->orderByDesc('id');
                } elseif (Schema::hasColumn($adhocTable, 'application_id')) {
                    $adhocQuery->orderByDesc('application_id');
                } elseif (Schema::hasColumn($adhocTable, 'applied_at')) {
                    $adhocQuery->orderByDesc('applied_at');
                } elseif (Schema::hasColumn($adhocTable, 'created_at')) {
                    $adhocQuery->orderByDesc('created_at');
                }

                $req = $adhocQuery->first();
                if ($req) {
                    $purpose = strtolower(trim((string) ($req->purpose ?? '')));
                    if (in_array($purpose, ['meeting', 'emergency'], true)) {
                        return ucfirst($purpose);
                    }
                    return $req->remarks ?? ($purpose !== '' ? ucfirst($purpose) : 'Adhoc');
                }
            }

            return 'Bunking';
        }

        // No checkin and no checkout => check leave and tour
        if (Schema::hasTable('leave_application') && ! empty($employeeId)) {
            $leaveQuery = DB::table('leave_application')
                ->where('employee_id', $employeeId)
                ->whereDate('from_date', '<=', $rowDate)
                ->whereDate('to_date', '>=', $rowDate);

            if (Schema::hasColumn('leave_application', 'application_id')) {
                $leaveQuery->orderByDesc('application_id');
            } elseif (Schema::hasColumn('leave_application', 'applied_at')) {
                $leaveQuery->orderByDesc('applied_at');
            } elseif (Schema::hasColumn('leave_application', 'created_at')) {
                $leaveQuery->orderByDesc('created_at');
            }

            $leave = $leaveQuery->first();
            if ($leave) {
                $msStatus = strtolower((string) ($leave->medical_superintendent_status ?? ''));
                if ($msStatus === 'approved') {
                    return 'On Leave';
                }
            }
        }

        if (Schema::hasTable('tour_records') && ! empty($employeeId)) {
            $tourQuery = DB::table('tour_records')
                ->where('employee_id', $employeeId)
                ->whereDate('start_date', '<=', $rowDate)
                ->where(function ($q) use ($rowDate) {
                    $q->whereNull('end_date')->orWhereDate('end_date', '>=', $rowDate);
                });

            if (Schema::hasColumn('tour_records', 'id')) {
                $tourQuery->orderByDesc('id');
            } elseif (Schema::hasColumn('tour_records', 'created_at')) {
                $tourQuery->orderByDesc('created_at');
            }

            $tour = $tourQuery->first();
            if ($tour) {
                return 'On Tour';
            }
        }

        return 'Absent';
    }
}