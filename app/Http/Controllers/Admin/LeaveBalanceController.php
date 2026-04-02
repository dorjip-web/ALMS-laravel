<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\LeaveType;
use App\Models\LeaveBalance;
use Illuminate\Support\Facades\DB;

class LeaveBalanceController extends Controller
{
    public function index()
    {
        $employees = Employee::where('status', 'active')->get();
        $leave_types = LeaveType::where('status', 'active')->get();
        $balances = LeaveBalance::join('tab1 as t', 'leave_balance.employee_id', '=', 't.employee_id')
            ->join('leave_type as lt', 'leave_balance.leave_type_id', '=', 'lt.leave_type_id')
            ->select('leave_balance.*', 't.employee_name', 'lt.leave_name')
            ->where('leave_balance.max_leave_per_year', '>', 0)
            ->get();

        return view('admin.leave_balances.index', compact('employees', 'leave_types', 'balances'));
    }

    public function setBalance(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:tab1,employee_id',
            'leave_type_id' => 'required|exists:leave_type,leave_type_id',
            'max_leave' => 'required|integer|min:0',
        ]);

        $exists = LeaveBalance::where([
            'employee_id' => $request->employee_id,
            'leave_type_id' => $request->leave_type_id,
            'year' => now()->year,
        ])->exists();

        if (!$exists) {
            LeaveBalance::create([
                'employee_id' => $request->employee_id,
                'leave_type_id' => $request->leave_type_id,
                'year' => now()->year,
                'max_leave_per_year' => $request->max_leave,
                'used_leave' => 0,
            ]);
        }

        return redirect()->route('admin.leave_balances.index');
    }

    public function adjustBalance(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:tab1,employee_id',
            'leave_type_id' => 'required|exists:leave_type,leave_type_id',
            'adjustment' => 'required|integer',
        ]);

        $balance = LeaveBalance::where([
            'employee_id' => $request->employee_id,
            'leave_type_id' => $request->leave_type_id,
            'year' => now()->year,
        ])->first();

        if ($balance) {
            $new_used = $balance->used_leave + $request->adjustment;
            $new_remaining = $balance->max_leave_per_year - $new_used;
            if ($new_remaining >= 0 && $new_used >= 0) {
                $balance->update([
                    'used_leave' => $new_used,
                ]);
            } else {
                return back()->with('error_message', 'Invalid adjustment!');
            }
        }

        return redirect()->route('admin.leave_balances.index');
    }

    public function resetYear()
    {
        DB::update('UPDATE leave_balance SET used_leave = 0');
        return redirect()->route('admin.leave_balances.index');
    }
}
