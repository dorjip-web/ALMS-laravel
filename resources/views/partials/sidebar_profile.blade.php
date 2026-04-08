@php
    // Force a consistent short organization name across all sidebar links
    $sidebarName = 'NTMH';
    $parts = preg_split('/\s+/', trim($sidebarName));
    $initials = count($parts) > 1
        ? strtoupper(substr($parts[0], 0, 1) . substr($parts[1], 0, 1))
        : strtoupper(substr($parts[0] ?? 'N', 0, 2));
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
