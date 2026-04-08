@php
    // Determine a consistent sidebar display name for all sidebars
    $sidebarName = $sidebarName ?? $username ?? (auth()->check() ? auth()->user()->name : null) ?? 'Admin';
    $parts = preg_split('/\s+/', trim($sidebarName));
    $initials = count($parts) > 1
        ? strtoupper(substr($parts[0], 0, 1) . substr($parts[1], 0, 1))
        : strtoupper(substr($parts[0] ?? 'A', 0, 2));

    // Only show upload control for authenticated non-admin users
    $isAdminArea = request()->is('admin/*') || ($isAdmin ?? false);
    $showUpload = auth()->check() && ! $isAdminArea;
@endphp

<style>
    .profile .avatar { position: relative; }
    .profile .avatar label { display: block; }
    .profile .camera-badge {
        position: absolute;
        right: 6px;
        bottom: 6px;
        width: 22px;
        height: 18px;
        background: transparent;
        color: inherit;
        border-radius: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        box-shadow: none;
        opacity: 0;
        transform: translateY(6px) scale(0.95);
        transition: opacity .14s ease, transform .14s ease;
        pointer-events: none;
    }
    .profile .avatar:hover .camera-badge { opacity: 1; transform: translateY(0) scale(1); pointer-events: auto; }
</style>

<div class="profile">
    <div class="avatar">
        @if($showUpload)
            <form id="profilePicForm" method="POST" action="{{ route('dashboard.profile.picture') }}" enctype="multipart/form-data" style="display:inline-block;">
                @csrf
                <label for="profilePicInput" style="cursor:pointer;display:block;position:relative;">
                    @if(auth()->user()->profile_picture && file_exists(public_path('profile_pictures/' . auth()->user()->profile_picture)))
                        <img src="{{ asset('profile_pictures/' . auth()->user()->profile_picture) }}" alt="Profile">
                    @else
                        {{ $initials }}
                    @endif
                    <span class="camera-badge" aria-hidden="true">
                        <svg width="18" height="14" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
                            <path d="M21 7h-3.2l-1.7-2.4A1 1 0 0015.6 4H8.4a1 1 0 00-.5.6L6.2 7H3a1 1 0 00-1 1v10a1 1 0 001 1h18a1 1 0 001-1V8a1 1 0 00-1-1zM12 17a4 4 0 110-8 4 4 0 010 8z" fill="#6b6b6b"/>
                        </svg>
                    </span>
                </label>
                <input id="profilePicInput" name="profile_picture" type="file" accept="image/*" style="display:none">
            </form>
        @else
            @if(auth()->check() && auth()->user()->profile_picture && file_exists(public_path('profile_pictures/' . auth()->user()->profile_picture)))
                <img src="{{ asset('profile_pictures/' . auth()->user()->profile_picture) }}" alt="Profile">
            @else
                {{ $initials }}
            @endif
        @endif
    </div>
    <div class="username">{{ $sidebarName }}</div>
</div>
