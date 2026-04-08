@php
    // Determine the display name for the sidebar (prefer passed value, then username, then authenticated user)
    $sidebarName = $sidebarName ?? $username ?? (auth()->check() ? auth()->user()->name : null) ?? 'Admin';
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
