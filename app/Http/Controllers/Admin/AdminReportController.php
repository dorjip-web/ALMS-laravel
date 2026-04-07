<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class AdminReportController extends Controller
{
    /**
     * Show reports dashboard with available report actions.
     */
    public function index(Request $request)
    {
        return view('admin_reports');
    }

    /**
     * Export a selected report. Accepts: report_type, period, format
     */
    public function export(Request $request)
    {
        $data = $request->validate([
            'report_type' => 'required|string',
            'period' => 'nullable|string',
            'format' => 'nullable|string|in:csv,xlsx,pdf',
        ]);

        $format = $data['format'] ?? 'csv';

        // Placeholder implementation: return a minimal CSV demonstrating export pipeline.
        $filename = 'report_' . preg_replace('/[^a-z0-9_\-]/i', '_', $data['report_type']) . '_' . date('Ymd_His') . '.' . $format;

        $rows = [
            ['Report', 'Period', 'Format', 'Generated At'],
            [$data['report_type'], $data['period'] ?? 'all', $format, now()->toDateTimeString()],
        ];

        $callback = function () use ($rows) {
            $out = fopen('php://output', 'w');
            foreach ($rows as $r) {
                fputcsv($out, $r);
            }
            fclose($out);
        };

        return Response::streamDownload($callback, $filename, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
