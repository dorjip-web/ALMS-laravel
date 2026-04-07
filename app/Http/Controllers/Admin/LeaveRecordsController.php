<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LeaveRecordsController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->input('status', 'all');
        $dept = $request->input('department_id', '');

        // departments for filter
        $departments = DB::table('department')->select('department_id', 'department_name')->orderBy('department_name')->get();

        $query = DB::table('leave_application as la')
            ->leftJoin('tab1 as t', 'la.employee_id', '=', 't.employee_id')
            ->leftJoin('leave_type as lt', 'la.leave_type_id', '=', 'lt.leave_type_id')
            ->leftJoin('department as d', 't.department_id', '=', 'd.department_id')
            ->select(
                DB::raw("COALESCE(t.employee_name, '-') as employee_name"),
                'la.application_id',
                DB::raw("COALESCE(lt.leave_name, '-') as type"),
                'la.from_date',
                'la.to_date',
                'la.reason',
                'la.total_days as days',
                'la.HoD_status',
                'la.medical_superintendent_status',
                DB::raw("COALESCE(d.department_name, '-') as department_name"),
                'la.applied_at'
            )
            ->orderByDesc('la.applied_at');

        if ($dept) {
            $query->where('t.department_id', $dept);
        }

        if ($status !== 'all') {
            if ($status === 'approved') {
                // consider either HoD or MS approved
                $query->where(function ($q) {
                    $q->where('la.medical_superintendent_status', 'approved')
                      ->orWhere('la.HoD_status', 'approved');
                });
            } elseif ($status === 'rejected') {
                $query->where(function ($q) {
                    $q->where('la.medical_superintendent_status', 'rejected')
                      ->orWhere('la.HoD_status', 'rejected');
                });
            } elseif ($status === 'pending') {
                $query->where(function ($q) {
                    $q->where('la.medical_superintendent_status', 'pending')
                      ->orWhere('la.HoD_status', 'pending');
                });
            }
        }

        $records = $query->paginate(50)->appends($request->query());

        return view('admin.leave_records.index', compact('records', 'departments', 'dept', 'status'));
    }

    public function export(Request $request)
    {
        $status = $request->input('status', 'all');
        $dept = $request->input('department_id', '');

        $query = DB::table('leave_application as la')
            ->leftJoin('tab1 as t', 'la.employee_id', '=', 't.employee_id')
            ->leftJoin('leave_type as lt', 'la.leave_type_id', '=', 'lt.leave_type_id')
            ->leftJoin('department as d', 't.department_id', '=', 'd.department_id')
            ->select(
                DB::raw("COALESCE(t.employee_name, '-') as employee_name"),
                DB::raw("COALESCE(lt.leave_name, '-') as type"),
                'la.from_date',
                'la.to_date',
                'la.total_days as days',
                'la.reason',
                'la.HoD_status',
                'la.medical_superintendent_status',
                DB::raw("COALESCE(d.department_name, '-') as department_name"),
                'la.applied_at'
            )
            ->orderByDesc('la.applied_at');

        if ($dept) {
            $query->where('t.department_id', $dept);
        }

        if ($status !== 'all') {
            if ($status === 'approved') {
                $query->where(function ($q) {
                    $q->where('la.medical_superintendent_status', 'approved')
                      ->orWhere('la.HoD_status', 'approved');
                });
            } elseif ($status === 'rejected') {
                $query->where(function ($q) {
                    $q->where('la.medical_superintendent_status', 'rejected')
                      ->orWhere('la.HoD_status', 'rejected');
                });
            } elseif ($status === 'pending') {
                $query->where(function ($q) {
                    $q->where('la.medical_superintendent_status', 'pending')
                      ->orWhere('la.HoD_status', 'pending');
                });
            }
        }

        $rows = $query->get();

        $filename = 'leave-records-' . date('Ymd-His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Employee', 'Type', 'Start', 'End', 'Days', 'Reason', 'HoD Status', 'MS Status', 'Department', 'Applied At']);
            foreach ($rows as $r) {
                fputcsv($out, [
                    $r->employee_name,
                    $r->type,
                    !empty($r->from_date) ? \Illuminate\Support\Carbon::parse($r->from_date)->format('d/m/Y') : '',
                    !empty($r->to_date) ? \Illuminate\Support\Carbon::parse($r->to_date)->format('d/m/Y') : '',
                    $r->days,
                    $r->reason,
                    $r->HoD_status,
                    $r->medical_superintendent_status,
                    $r->department_name,
                    !empty($r->applied_at) ? \Illuminate\Support\Carbon::parse($r->applied_at)->format('d/m/Y') : '',
                ]);
            }
            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }
}
