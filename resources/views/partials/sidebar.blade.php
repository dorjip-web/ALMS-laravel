@php
    $fullName = $employee['employee_name'] ?? auth()->user()->name;
    $parts = preg_split('/\s+/', trim($fullName));
    $initials = count($parts) > 1
        ? strtoupper(substr($parts[0], 0, 1) . substr($parts[1], 0, 1))
        : strtoupper(substr($parts[0] ?? 'U', 0, 2));
    $roleName = strtolower(trim((string) ($employee['role_name'] ?? '')));
    $isMs = $roleName === 'ms' || str_contains($roleName, 'medical') || str_contains($roleName, 'superintendent');
    $showShift = isset($employee['department_id']) && (int) ($employee['department_id'] ?? 0) === 3;

    $displayName = $fullName;
    $displayAvatar = $initials;
    $msRouteDetected = request()->routeIs('ms.*') || request()->is('ms*') || str_contains(request()->path(), 'ms-dashboard');
    if ($isMs && $msRouteDetected) {
        $displayName = 'Medical Superintendent';
        $displayAvatar = 'MS';
    }
@endphp

<aside class="sidebar">
    @include('partials.sidebar_profile', ['sidebarName' => $displayName])

    <nav class="menu">
        <a href="{{ route('dashboard') }}#employee" @class(['active' => request()->routeIs('dashboard') && ! request()->routeIs('dashboard.attendance_summary')])>My Dashboard</a>
        @if (strtolower((string) ($employee['role_name'] ?? '')) === 'hod')
            <a href="{{ route('hod.dashboard') }}">HoD Dashboard</a>
            @if(request()->routeIs('hod.dashboard'))
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('hod.adhoc.index') }}">
                        <i class="fas fa-clipboard-check"></i>
                        <span>Adhoc Requests</span>
                    </a>
                </li>
            @endif
        @endif
        @if ($isMs)
            <a href="{{ route('ms.dashboard') }}">MS Dashboard</a>
        @endif
        <a href="{{ route('dashboard') }}#attendance">Attendance</a>
        <a href="{{ route('dashboard.attendance_summary') }}" @class(['ajax-link', 'active' => request()->routeIs('dashboard.attendance_summary')])>Attendance Summary</a>
        <a href="{{ route('dashboard.leave_form') }}" @class(['active' => request()->routeIs('dashboard.leave_form')])>Leave</a>
        <!-- Leave History link removed per request -->
        <a href="{{ route('dashboard.tour') }}" @class(['active' => request()->routeIs('dashboard.tour')])>Tour</a>
        <a href="{{ route('dashboard.adhoc_requests') }}" @class(['active' => request()->routeIs('dashboard.adhoc_requests')])>Adhoc Requests</a>
    </nav>
</aside>
