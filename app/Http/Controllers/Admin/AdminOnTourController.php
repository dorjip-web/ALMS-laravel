<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AdminOnTourController extends Controller
{
    public function index(Request $request)
    {
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

            // Choose a stable identifier column for actions. Prefer `tour_id`, then `id`, then `employee_id`.
            if (Schema::hasColumn('tour_records', 'tour_id')) {
                $idExpr = 'tr.tour_id as record_id';
            } elseif (Schema::hasColumn('tour_records', 'id')) {
                $idExpr = 'tr.id as record_id';
            } else {
                $idExpr = 'tr.employee_id as record_id';
            }

            $tourSelect = array_merge([
                DB::raw($idExpr),
                'tr.employee_id',
                'e.employee_name',
                'e.designation',
                'tr.place',
                'tr.start_date',
                'tr.end_date',
                'tr.purpose',
                'tr.office_order_pdf',
            ]);
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

        $departments = [];
        if (Schema::hasTable('department')) {
            $departments = DB::table('department')->select('department_id','department_name')->orderBy('department_name')->get();
        }

        return view('admin_on_tour', [
            'onTourStaff' => $onTourStaff,
            'departments' => $departments,
            'dept' => $dept,
        ]);
    }

    public function edit(Request $request, $id)
    {
        if (! Schema::hasTable('tour_records')) {
            return redirect()->route('admin.on_tour')->with('flash_error', 'Tour records not available.');
        }

        // Prefer `tour_id`, then `id`, then `employee_id` when locating the record.
        $record = DB::table('tour_records')
            ->when(Schema::hasColumn('tour_records', 'tour_id'), fn($q) => $q->where('tour_id', $id), fn($q) => $q->when(Schema::hasColumn('tour_records', 'id'), fn($qq) => $qq->where('id', $id), fn($qq) => $qq->where('employee_id', $id)))
            ->first();
        if (! $record) {
            return redirect()->route('admin.on_tour')->with('flash_error', 'Record not found');
        }

        $departments = Schema::hasTable('department') ? DB::table('department')->select('department_id','department_name')->orderBy('department_name')->get() : [];
        $employees = Schema::hasTable('tab1') ? DB::table('tab1')->select('employee_id','employee_name')->orderBy('employee_name')->get() : [];

        return view('admin_on_tour_edit', [
            'record' => (array) $record,
            'departments' => $departments,
            'employees' => $employees,
        ]);
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'employee_id' => 'required|integer',
            'place' => 'nullable|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'purpose' => 'nullable|string',
        ]);

        DB::table('tour_records')
            ->when(Schema::hasColumn('tour_records', 'tour_id'), fn($q) => $q->where('tour_id', $id), fn($q) => $q->when(Schema::hasColumn('tour_records', 'id'), fn($qq) => $qq->where('id', $id), fn($qq) => $qq->where('employee_id', $id)))
            ->update(array_merge($data, ['updated_at' => now()]));

        return redirect()->route('admin.on_tour')->with('flash_success', 'Tour record updated');
    }

    public function delete(Request $request, $id)
    {
        DB::table('tour_records')
            ->when(Schema::hasColumn('tour_records', 'tour_id'), fn($q) => $q->where('tour_id', $id), fn($q) => $q->when(Schema::hasColumn('tour_records', 'id'), fn($qq) => $qq->where('id', $id), fn($qq) => $qq->where('employee_id', $id)))
            ->delete();
        return redirect()->route('admin.on_tour')->with('flash_success', 'Tour record deleted');
    }

    /**
     * Export current on-tour list as CSV (honours department filter).
     */
    public function export(Request $request)
    {
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

            if (Schema::hasColumn('tour_records', 'tour_id')) {
                $idExpr = 'tr.tour_id as record_id';
            } elseif (Schema::hasColumn('tour_records', 'id')) {
                $idExpr = 'tr.id as record_id';
            } else {
                $idExpr = 'tr.employee_id as record_id';
            }

            $tourSelect = [
                DB::raw($idExpr),
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
                ->toArray();
        }

        $filename = 'on_tour_' . date('Ymd_His') . '.csv';
        $columns = ['Employee ID','Name','Designation','Department','Place','From','To','Total Days','Purpose','Office Order PDF'];

        $callback = function() use ($onTourStaff, $columns) {
            $out = fopen('php://output', 'w');
            fputcsv($out, $columns);
            foreach ($onTourStaff as $t) {
                $from = $t->start_date ?? '';
                $to = $t->end_date ?? '';
                $total = '';
                if (!empty($from) && !empty($to)) {
                    try {
                        $fromC = \Illuminate\Support\Carbon::parse($from);
                        $toC = \Illuminate\Support\Carbon::parse($to);
                        $total = $fromC->diffInDays($toC) + 1;
                    } catch (\Throwable $e) {
                        $total = '';
                    }
                }

                $line = [
                    $t->employee_id ?? '',
                    $t->employee_name ?? '',
                    $t->designation ?? '',
                    $t->department_name ?? '',
                    $t->place ?? '',
                    $from,
                    $to,
                    $total,
                    $t->purpose ?? '',
                    $t->office_order_pdf ?? '',
                ];
                fputcsv($out, $line);
            }
            fclose($out);
        };

        return response()->streamDownload($callback, $filename, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }
}
