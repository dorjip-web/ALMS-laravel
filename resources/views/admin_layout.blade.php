<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>@yield('pageTitle', 'Admin') — NTMH</title>
    <link rel="stylesheet" href="{{ asset('css/admin_dashboard.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
</head>
<body>
@php
    $adminLogo = null;
    foreach (['images/ntmh-logo.png', 'images/ntmh-logo.png.jpg', 'images/ntmh-logo.jpg'] as $candidate) {
        if (file_exists(public_path($candidate))) {
            $adminLogo = asset($candidate);
            break;
        }
    }
@endphp
<div class="app">
    <aside class="sidebar">
        <div class="profile">
            <div class="avatar">
                @if ($adminLogo)
                    <img src="{{ $adminLogo }}" alt="NTMH logo">
                @else
                    {{ $avatar ?? 'AD' }}
                @endif
            </div>
            <div class="username">{{ $username ?? 'Admin' }}</div>
        </div>
        <nav class="menu">
            <a href="{{ route('admin.dashboard') }}" @class(['active' => ($activeNav ?? '') === 'dashboard'])>Admin Dashboard</a>
            <a href="{{ route('admin.users.index') }}" @class(['active' => ($activeNav ?? '') === 'users'])>User Management</a>
            <a href="{{ route('admin.roles_permissions') }}" @class(['active' => ($activeNav ?? '') === 'roles_permissions'])>Roles &amp; Permissions</a>
            <a href="{{ route('admin.departments_hods.index') }}" @class(['active' => ($activeNav ?? '') === 'departments_hods'])>Department &amp; HoD Management</a>
            <a href="{{ route('admin.leave_balances.index') }}" @class(['active' => ($activeNav ?? '') === 'leave_balances'])>Leave Balance</a>
            <a href="{{ route('admin.on_tour') }}" @class(['active' => ($activeNav ?? '') === 'staff_on_tour'])>Staff on Tour</a>
            <a href="{{ route('admin.adhoc') }}" @class(['active' => ($activeNav ?? '') === 'adhoc_requests'])>Adhoc Requests</a>
            <a href="{{ route('admin.leave_types.index') }}" @class(['active' => ($activeNav ?? '') === 'leave_types'])>Leave Management</a>
            <a href="{{ route('admin.attendance_logs.index') }}" @class(['active' => ($activeNav ?? '') === 'attendance_logs'])>Attendance Logs</a>
            <a href="{{ route('admin.leave_records.index') }}" @class(['active' => ($activeNav ?? '') === 'leave_records'])>Leave Records</a>
            <a href="{{ url('reports.php') }}" @class(['active' => ($activeNav ?? '') === 'reports'])>Reports</a>
            <a href="{{ url('settings.php') }}" @class(['active' => ($activeNav ?? '') === 'settings'])>Settings</a>
        </nav>
    </aside>

    <main class="main">
        <header class="topbar">
            <div class="search"><input placeholder="Search..."></div>
            <div class="logout">
                <form method="POST" action="{{ route('admin.logout') }}" style="display:inline;">
                    @csrf
                    <button type="submit">Logout</button>
                </form>
            </div>
        </header>

        <section>
            @yield('content')
        </section>
    </main>
</div>
</div>

@yield('scripts')

</body>
</html>
