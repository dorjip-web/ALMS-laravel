<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Tab1;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function evaluateCheckin(Request $request, $attendanceId)
    {
        $attendance = DB::table('attendance')->where('attendance_id', $attendanceId)->first();

        if (! $attendance) {
            return response()->json(['status' => 'Attendance record not found']);
        }

        $departmentId = $attendance->department_id;
        $checkin      = strtotime($attendance->checkin);
        $status       = "On Time";

        // ✅ Rule applies only to Administration, Acupuncture, Jamched, OPD, Tsubched
        if (in_array($departmentId, [1, 2, 4, 5, 6])) {
            $onTimeLimit = strtotime('09:15:00');

            if ($checkin <= $onTimeLimit) {
                $status = "On Time";
                DB::table('attendance')
                    ->where('attendance_id', $attendanceId)
                    ->update([
                        'checkin_status' => $status,
                        'late_reason' => null
                    ]);
            } else {
                // Late requires reason
                $reason = $request->input('late_reason');
                if (! $reason || trim($reason) === '') {
                    return response()->json([
                        'attendance_id' => $attendanceId,
                        'department_id' => $departmentId,
                        'checkin' => $attendance->checkin,
                        'checkin_status' => 'Late',
                        'message' => 'Check-in not allowed without reason for being late.'
                    ]);
                }

                $status = "Late";
                DB::table('attendance')
                    ->where('attendance_id', $attendanceId)
                    ->update([
                        'checkin_status' => $status,
                        'late_reason' => $reason
                    ]);
            }
        }

        return response()->json([
            'attendance_id' => $attendanceId,
            'department_id' => $departmentId,
            'checkin' => $attendance->checkin,
            'checkin_status' => $status,
            'late_reason' => $attendance->late_reason ?? null
        ]);
    }

    public function checkout(Request $request)
    {
        $currentTime = Carbon::now();
        $cutoffTime = Carbon::today()->setTime(16, 30);

        // Get today's attendance record for the logged-in user
        $attendance = Tab1::where('employee_id', Auth::id())
            ->whereDate('attendance_date', Carbon::today())
            ->first();

        if (! $attendance) {
            return response()->json([
                'status' => 'error',
                'message' => 'No attendance record found.'
            ], 404);
        }

        // Departments restricted by cutoff rule
        $restrictedDepartments = [1, 2, 4, 5, 6];

        // Apply cutoff only if department is in restricted list
        if (in_array($attendance->department_id, $restrictedDepartments)) {
            if ($currentTime->greaterThan($cutoffTime)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Checkout disabled after 4:30 PM for your department.'
                ], 403);
            }
        }

        // Update checkout info
        $attendance->checkout = $currentTime;
        $attendance->checkout_status = 'completed';
        $attendance->checkout_address = $request->ip();
        $attendance->save();

        return response()->json(['status' => 'success', 'message' => 'Checkout complete']);
    }
}
