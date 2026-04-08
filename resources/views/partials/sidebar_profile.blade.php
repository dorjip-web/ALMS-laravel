@php
    // Show organisation short name on admin pages, show "HoD" only on HoD dashboard/pages,
    // otherwise show the employee name
    $isAdminArea = (
        request()->routeIs('admin.*') ||
        request()->routeIs('admin.dashboard') ||
        request()->is('admin-dashboard') ||
        ($isAdmin ?? false)
    );

    $isHodArea = (
        request()->routeIs('hod.*') ||
        request()->routeIs('hod.dashboard') ||
        request()->is('hod-dashboard') ||
        ($isHod ?? false)
    );

    if ($isHodArea) {
        $sidebarName = 'HoD';
    } elseif ($isAdminArea) {
        $sidebarName = 'NTMH';
    } else {
        $sidebarName = $sidebarName ?? $username ?? (auth()->check() ? auth()->user()->name : null) ?? 'Admin';
    }

    $parts = preg_split('/\s+/', trim($sidebarName));
    $initials = count($parts) > 1
        ? strtoupper(substr($parts[0], 0, 1) . substr($parts[1], 0, 1))
        : strtoupper(substr($parts[0] ?? 'A', 0, 2));
@endphp

<div class="profile">
    <div class="avatar">
        @if(auth()->check() && auth()->user()->profile_picture && file_exists(public_path('profile_pictures/' . auth()->user()->profile_picture)))
            <img src="{{ asset('profile_pictures/' . auth()->user()->profile_picture) }}" alt="Profile">
        @else
            {{ $initials }}
        @endif
    </div>
    <div class="username">{{ $sidebarName }}</div>
</div>
