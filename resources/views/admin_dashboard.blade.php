@extends('admin_layout')

@section('pageTitle', 'Admin Dashboard')

@section('content')
<section class="grid">
            <div class="card">
                <h2>Welcome, {{ $username }}</h2>
                <p>Use the links on the left to manage the system.</p>
            </div>

            <div class="card">
                <h3>Main Features</h3>
                <ul>
                    <li><strong>User & Role Management</strong> — add/edit/delete employees; assign roles (employee, HOD, MS, Admin).</li>
                    <li><strong>Department Management</strong> — add/edit/delete departments; map HODs to departments.</li>
                    <li><strong>Leave Type Management</strong> — add/edit/delete leave types.</li>
                    <li><strong>System-wide Leave Reports</strong> — generate by employee, department, role, month, year; export to PDF/Excel.</li>
                    <li><strong>Settings</strong> — application-wide configuration.</li>
                    <li><strong>Full Attendance Log</strong> — view and export attendance records.</li>
                </ul>
            </div>

            <div class="card">
                <h3>Quick Actions</h3>
                <div style="display:flex;gap:10px;flex-wrap:wrap;margin-top:10px">
                    <a class="btn" href="{{ url('admin/users/users.php') }}">Manage Users</a>
                    <a class="btn" href="{{ url('admin/manage_departments.php') }}">Manage Departments</a>
                    <a class="btn" href="{{ url('manage_leave_types.php') }}">Leave Types</a>
                    <a class="btn" href="{{ route('admin.reports') }}">Reports</a>
                    <a class="btn" href="{{ url('attendanceleave/attendance_logs.php') }}">Attendance Log</a>
                    <a class="btn" href="{{ url('settings.php') }}">Settings</a>
                </div>
            </div>

            <div class="card">
                <h3>Notes</h3>
                <p>This page is a scaffold. Implement the backend pages linked above to enable the full features listed.</p>
            </div>
        </section>
@endsection
