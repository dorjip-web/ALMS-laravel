<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class AttendanceLogController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->input('filter', '');
        $date = $request->input('date', '');
        $fromDate = $request->input('from_date', '');
        $dept = $request->input('department_id', '');

        // Fetch all departments for dropdown
        $departments = DB::table('department')->select('department_id', 'department_name')->orderBy('department_name')->get();

        // Dashboard counts (use selected date or today)
        $base = DB::table('attendance as a')
            ->join('tab1 as t', 't.employee_id', '=', 'a.employee_id');

        // Determine whether a date range was provided (from_date + date)
        $isRange = $fromDate && $date;

        // Build an attendance base specifically for the target date or range so counts are consistent
        $attendanceBase = DB::table('attendance as a')
            ->join('tab1 as t', 't.employee_id', '=', 'a.employee_id');

        if ($isRange) {
            $attendanceBase->whereBetween('a.attendance_date', [$fromDate, $date]);
        } else {
            $targetDate = $date ?: now()->toDateString();
            $attendanceBase->where('a.attendance_date', $targetDate);
        }

        if ($dept) {
            $attendanceBase->where('t.department_id', $dept);
            $base->where('t.department_id', $dept);
        }

        // total attendance rows for target date (not used for staff percent)
        $total = (clone $attendanceBase)->count();

        // Present: staff who have both checkin AND checkout for the target date
        $present = (clone $attendanceBase)
            ->whereNotNull('a.checkin')
            ->whereNotNull('a.checkout')
            ->distinct('a.employee_id')
            ->count('a.employee_id');

        // Missing checkout, late and onTime counts from attendance rows for the date
        $missing = (clone $attendanceBase)->where('a.checkout_status', 'missing')->count();
        $late = (clone $attendanceBase)->where('a.checkin_status', 'Late')->count();
        $onTime = (clone $attendanceBase)->where('a.checkin_status', 'On Time')->count();

        // total staff (from legacy employee table), optionally filtered by department
        $totalStaffQuery = DB::table('tab1');
        if ($dept) {
            $totalStaffQuery->where('department_id', $dept);
        }
        $totalStaff = $totalStaffQuery->count();

        // Absent: staff without both checkin+checkout on target date (counted from total staff)
        $absent = max(0, $totalStaff - $present);

        // Attendance percent = present staff / total staff
        $attendancePercent = $totalStaff > 0 ? round(($present / $totalStaff) * 100, 1) : 0;

        // total staff (from legacy employee table), optionally filtered by department
        $totalStaffQuery = DB::table('tab1');
        if ($dept) {
            $totalStaffQuery->where('department_id', $dept);
        }
        $totalStaff = $totalStaffQuery->count();

        // Main query - different approach for "absent" because absent employees may not have attendance rows
        if ($filter === 'absent') {
            $query = DB::table('tab1 as t')
                ->leftJoin('attendance as a', function ($join) use ($isRange, $fromDate, $date) {
                    $join->on('t.employee_id', '=', 'a.employee_id');
                    if ($isRange) {
                        $join->whereBetween('a.attendance_date', [$fromDate, $date]);
                    } else {
                        $join->where('a.attendance_date', $date ?: now()->toDateString());
                    }
                })
                ->leftJoin('department as d', 'd.department_id', '=', 't.department_id')
                ->select(
                    't.employee_id',
                    't.employee_name',
                    't.designation',
                    't.department_id',
                    'd.department_name',
                    DB::raw('NULL as attendance_date'),
                    'a.shift_type',
                    'a.checkin',
                    'a.checkin_address',
                    'a.checkin_status',
                    'a.checkout',
                    'a.checkout_address',
                    'a.checkout_status',
                    'a.remarks'
                );

            if ($dept) {
                $query->where('t.department_id', $dept);
            }

            // Absent means no attendance row for the date OR both checkin and checkout are null
            $query->where(function ($q) {
                $q->whereNull('a.employee_id')
                  ->orWhere(function ($q2) {
                      $q2->whereNull('a.checkin')->whereNull('a.checkout');
                  });
            });

            $logs = $query->orderBy('t.employee_name')->get();
        } else {
            $query = DB::table('attendance as a')
                ->join('tab1 as t', 't.employee_id', '=', 'a.employee_id')
                ->leftJoin('department as d', 'd.department_id', '=', 't.department_id')
                ->select(
                    't.employee_id',
                    't.employee_name',
                    't.designation',
                    't.department_id',
                    'd.department_name',
                    'a.attendance_date',
                    'a.shift_type',
                    'a.checkin',
                    'a.checkin_address',
                    'a.checkin_status',
                    'a.checkout',
                    'a.checkout_address',
                    'a.checkout_status',
                    'a.remarks'
                )
                ;

            if ($isRange) {
                $query->whereBetween('a.attendance_date', [$fromDate, $date]);
            } else {
                $query->where('a.attendance_date', $targetDate);
            }

            if ($dept) {
                $query->where('t.department_id', $dept);
            }

            if ($filter == 'late') {
                $query->where('a.checkin_status', 'Late');
            }
            if ($filter == 'on_time') {
                $query->where('a.checkin_status', 'On Time');
            }
            if ($filter == 'missing_checkout') {
                $query->where('a.checkout_status', 'Missing');
            }
            if ($filter == 'present') {
                $query->whereNotNull('a.checkin')->whereNotNull('a.checkout');
            }

            $logs = $query->orderByDesc('a.attendance_date')->get();
        }

        // Resolve stored addresses (may be Nominatim reverse URLs) into
        // human-readable strings for the admin UI and compute Remarks.
        foreach ($logs as $row) {
            $row->checkin_address = $this->displayAddressForUI($row->checkin_address ?? '');
            $row->checkout_address = $this->displayAddressForUI($row->checkout_address ?? '');

            // Determine a date to use for cross-table lookups.
            $rowDate = $row->attendance_date ?? ($date ?: now()->toDateString());

            // If both checkin and checkout exist => Present
            if (! empty($row->checkin) && ! empty($row->checkout)) {
                $row->remarks = 'Present';
                continue;
            }

            // If checked in but no checkout => consult adhoc requests for that date
            if (! empty($row->checkin) && empty($row->checkout)) {
                $adhocTable = Schema::hasTable('adhoc_requests') ? 'adhoc_requests' : (Schema::hasTable('adhoc_request') ? 'adhoc_request' : null);
                if ($adhocTable) {
                    $adhocQuery = DB::table($adhocTable)
                        ->where(function ($q) use ($row, $rowDate) {
                            if (! empty($row->employee_id)) {
                                $q->where('employee_id', $row->employee_id);
                            }
                            $q->whereDate('date', $rowDate);
                        });

                    // choose a safe ordering column for adhoc table
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
                            $row->remarks = ucfirst($purpose);
                        } else {
                            $row->remarks = $req->remarks ?? ( $purpose !== '' ? ucfirst($purpose) : 'Adhoc' );
                        }
                        continue;
                    }
                }

                // No adhoc request found for this checked-in-but-not-checked-out row => mark as Bunking
                $row->remarks = 'Bunking';
                continue;
            }

            // No checkin and no checkout => consult leave and tour tables
            $marked = false;
            if (Schema::hasTable('leave_application') && ! empty($row->employee_id)) {
                $leaveQuery = DB::table('leave_application')
                    ->where('employee_id', $row->employee_id)
                    ->whereDate('from_date', '<=', $rowDate)
                    ->whereDate('to_date', '>=', $rowDate);

                // choose a safe ordering column
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
                        $row->remarks = 'On Leave';
                        $marked = true;
                    }
                }
            }

            if (! $marked && Schema::hasTable('tour_records') && ! empty($row->employee_id)) {
                $tourQuery = DB::table('tour_records')
                    ->where('employee_id', $row->employee_id)
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
                    $row->remarks = 'On Tour';
                    $marked = true;
                }
            }

            if (! $marked) {
                $row->remarks = 'Absent';
            }
        }

        $from_date = $fromDate;
        return view('admin.attendance_logs.index', compact('present', 'missing', 'late', 'absent', 'logs', 'filter', 'date', 'from_date', 'dept', 'departments', 'attendancePercent', 'total', 'onTime', 'totalStaff'));
    }

    private function reverseGeocode($lat, $lon): string
    {
        if (! is_numeric($lat) || ! is_numeric($lon)) {
            return '';
        }

        try {
            Log::debug('Admin reverseGeocode: Calling Nominatim', ['lat' => $lat, 'lon' => $lon]);
            $response = Http::withoutVerifying()
                ->withHeaders([
                    'User-Agent' => 'EmployeeAttendanceSystem/1.0',
                ])->timeout(10)->get('https://nominatim.openstreetmap.org/reverse', [
                    'format' => 'jsonv2',
                    'addressdetails' => 1,
                    'lat' => $lat,
                    'lon' => $lon,
                ]);

            if (! $response->successful()) {
                Log::warning('Admin reverseGeocode: API returned error', ['status' => $response->status(), 'body' => $response->body()]);
                return '';
            }

            $data = $response->json();
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

                return implode(', ', $parts);
            }
        } catch (\Exception $e) {
            Log::error('Admin reverseGeocode: Exception', ['error' => $e->getMessage()]);
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
}
