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
        @include('partials.sidebar_profile')
        <nav class="menu">
            <a href="{{ route('admin.dashboard') }}" @class(['active' => ($activeNav ?? '') === 'dashboard'])>Admin Dashboard</a>
            <a href="{{ route('admin.users.index') }}" @class(['active' => ($activeNav ?? '') === 'users'])>User Management</a>
            <a href="{{ route('admin.roles_permissions') }}" @class(['active' => ($activeNav ?? '') === 'roles_permissions'])>Roles &amp; Permissions</a>
            <a href="{{ route('admin.departments_hods.index') }}" @class(['active' => ($activeNav ?? '') === 'departments_hods'])>Department &amp; HoD Management</a>
            <a href="{{ route('admin.leave_balances.index') }}" @class(['active' => ($activeNav ?? '') === 'leave_balances'])>Leave Balance</a>
            <a href="{{ route('admin.on_tour') }}" @class(['active' => ($activeNav ?? '') === 'staff_on_tour'])>Staff on Tour</a>
            <a href="{{ route('admin.adhoc') }}" @class(['active' => ($activeNav ?? '') === 'adhoc_requests'])>Adhoc Requests</a>
            <a href="{{ url('/admin/device-bindings') }}" @class(['active' => ($activeNav ?? '') === 'device_bindings'])>Device Binding</a>
            <a href="{{ route('admin.leave_types.index') }}" @class(['active' => ($activeNav ?? '') === 'leave_types'])>Leave Management</a>
            <a href="{{ route('admin.attendance_logs.index') }}" @class(['active' => ($activeNav ?? '') === 'attendance_logs'])>Attendance Logs</a>
            <a href="{{ route('admin.leave_records.index') }}" @class(['active' => ($activeNav ?? '') === 'leave_records'])>Leave Records</a>
            {{-- Reports link removed per request --}}
            <div class="menu-item">
                <a href="{{ url('settings.php') }}" @class(['active' => ($activeNav ?? '') === 'settings'])>Settings</a>
                <div class="submenu">
                    <a href="{{ url('/admin/settings/add-admin') }}">Add New Admin</a>
                    <a href="{{ url('/admin/settings/change-admin-password') }}">Change Admin Password</a>
                    <a href="{{ url('/admin/settings/edit-admin') }}">Edit Admin Details</a>
                    <a href="{{ url('/admin/settings/toggle-admin') }}">Toggle</a>
                </div>
            </div>
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

@yield('scripts')

<script>
(() => {
    const links = document.querySelectorAll('.sidebar .menu a');
    function normalize(path) { return path.replace(/\/+$/,''); }
    const loc = normalize(location.pathname || '/');
    links.forEach(a => {
        const href = a.getAttribute('href') || '';
        try {
            const p = new URL(href, location.origin).pathname;
            if (normalize(p) === loc || (loc.startsWith(normalize(p)) && normalize(p) !== '')) {
                a.classList.add('active');
            } else {
                a.classList.remove('active');
            }
        } catch (e) {
            // ignore invalid hrefs
        }
        a.addEventListener('click', function () {
            links.forEach(x => x.classList.remove('active'));
            this.classList.add('active');
        });
    });
})();
</script>

</body>
</html>
