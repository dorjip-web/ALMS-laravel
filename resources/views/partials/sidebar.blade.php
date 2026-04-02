@php
    $fullName = $employee['employee_name'] ?? auth()->user()->name;
    $parts = preg_split('/\s+/', trim($fullName));
    $initials = count($parts) > 1
        ? strtoupper(substr($parts[0], 0, 1) . substr($parts[1], 0, 1))
        : strtoupper(substr($parts[0] ?? 'U', 0, 2));
    $roleName = strtolower(trim((string) ($employee['role_name'] ?? '')));
    $isMs = $roleName === 'ms' || str_contains($roleName, 'medical') || str_contains($roleName, 'superintendent');
    $showShift = isset($employee['department_id']) && (int) ($employee['department_id'] ?? 0) === 3;
@endphp

<aside class="sidebar">
    <div class="profile">
        <div class="avatar-wrapper">
            <div class="avatar">
                @if(auth()->user()->profile_picture && file_exists(public_path('profile_pictures/' . auth()->user()->profile_picture)))
                    <img src="{{ asset('profile_pictures/' . auth()->user()->profile_picture) }}" alt="Profile">
                @else
                    {{ strtoupper(substr(trim(explode(' ', $fullName)[0] ?? 'U'), 0, 1) ?? 'U') }}
                @endif
            </div>
            <label class="avatar-upload-btn" for="profilePicInput" title="Change photo">
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path><circle cx="12" cy="13" r="4"></circle></svg>
            </label>
            <form id="profilePicForm" method="POST" action="{{ route('dashboard.profile.picture') }}" enctype="multipart/form-data" style="display:none;">
                @csrf
                <input type="file" id="profilePicInput" name="profile_picture" accept="image/jpeg,image/png,image/gif,image/webp">
            </form>
        </div>
        <div class="username">{{ $fullName }}</div>
    </div>

    <nav class="menu">
        <a href="{{ route('dashboard') }}#employee" @class(['active' => request()->routeIs('dashboard') && ! request()->routeIs('dashboard.attendance_summary')])>My Dashboard</a>
        @if (strtolower((string) ($employee['role_name'] ?? '')) === 'hod')
            <a href="{{ route('hod.dashboard') }}">HoD Dashboard</a>
        @endif
        @if ($isMs)
            <a href="{{ route('ms.dashboard') }}">MS Dashboard</a>
        @endif
        <a href="{{ route('dashboard') }}#attendance">Attendance</a>
        <a href="{{ route('dashboard.attendance_summary') }}" class="ajax-link" @class(['active' => request()->routeIs('dashboard.attendance_summary')])>Attendance Summary</a>
        <a href="{{ route('dashboard.leave_form') }}" @class(['active' => request()->routeIs('dashboard.leave_form')])>Leave</a>
        <a href="{{ route('dashboard.leave_form') }}#leave-history" @class(['active' => request()->routeIs('dashboard.leave_form')])>Leave History</a>
        <a href="{{ route('dashboard.tour') }}" @class(['active' => request()->routeIs('dashboard.tour')])>Tour</a>
        <a href="{{ route('dashboard.adhoc_requests') }}" @class(['active' => request()->routeIs('dashboard.adhoc_requests')])>Adhoc Requests</a>
    </nav>
</aside>
