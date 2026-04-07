@extends('admin_layout')

@section('pageTitle', 'Admin Dashboard')

@section('content')
<!-- Admin dashboard quick links -->
<section class="grid">
    <div class="card">
        <h3>Quick Links</h3>
        <div style="display:flex;flex-wrap:wrap;gap:10px;margin-top:12px;">
            <a class="btn" href="{{ route('admin.users.index') }}">User Management</a>
            <a class="btn" href="{{ route('admin.roles_permissions') }}">Roles &amp; Permissions</a>
            <a class="btn" href="{{ route('admin.departments_hods.index') }}">Department &amp; HoD Management</a>
            <a class="btn" href="{{ route('admin.leave_balances.index') }}">Leave Balance</a>
            <a class="btn" href="{{ route('admin.on_tour') }}">Staff on Tour</a>
            <a class="btn" href="{{ route('admin.adhoc') }}">Adhoc Requests</a>
            <a class="btn" href="{{ route('admin.device_bindings') }}">Device Binding</a>
            <a class="btn" href="{{ route('admin.leave_types.index') }}">Leave Management</a>
            <a class="btn" href="{{ route('admin.attendance_logs.index') }}">Attendance Logs</a>
            <a class="btn" href="{{ route('admin.leave_records.index') }}">Leave Records</a>
            <a class="btn" href="{{ url('settings.php') }}">Settings</a>
        </div>
    </div>
</section>
@endsection
