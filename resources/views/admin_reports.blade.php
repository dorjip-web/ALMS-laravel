@extends('admin_layout')

@section('pageTitle', 'Reports')

@section('content')
<div style="padding:18px">
    <h1>Core Reporting</h1>

    <div class="card" style="margin-top:12px;">
        <h3>Dashboard Export</h3>
        <p>Export attendance, leave, staff on tour and adhoc requests.</p>
        <form method="GET" action="{{ route('admin.reports.export') }}">
            <input type="hidden" name="report_type" value="dashboard_export">
            <label>Period: <select name="period"><option value="daily">Daily</option><option value="weekly">Weekly</option><option value="monthly">Monthly</option><option value="yearly">Yearly</option></select></label>
            <label style="margin-left:12px">Format: <select name="format"><option value="csv">CSV</option><option value="xlsx">Excel</option><option value="pdf">PDF</option></select></label>
            <button class="btn" style="margin-left:12px">Export</button>
        </form>
    </div>

    <div class="card" style="margin-top:12px;">
        <h3>Performance Metrics</h3>
        <p>Staff punctuality scores and related reports.</p>
        <form method="GET" action="{{ route('admin.reports.export') }}">
            <input type="hidden" name="report_type" value="punctuality">
            <label>Period: <select name="period"><option value="monthly">Monthly</option><option value="yearly">Yearly</option></select></label>
            <label style="margin-left:12px">Format: <select name="format"><option value="csv">CSV</option><option value="xlsx">Excel</option></select></label>
            <button class="btn" style="margin-left:12px">Export</button>
        </form>
    </div>

    <div class="card" style="margin-top:12px;">
        <h3>Geo-location & Audit / Alerts</h3>
        <p>Configure and download geo-verification, audit trail and alert summaries.</p>
        <p>This page contains scaffolding for:<br>
        - Geo-location tracking exports<br>
        - Audit trail (admin actions)
        - Real-time alerts summary</p>
    </div>
</div>
@endsection
