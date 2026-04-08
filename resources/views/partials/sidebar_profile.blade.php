@php
    // Show organisation short name on admin pages, otherwise show the employee name
    $isAdminArea = request()->is('admin/*') || ($isAdmin ?? false);
    if ($isAdminArea) {
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
    @php $showUpload = !($isAdminArea ?? false) && auth()->check(); @endphp
    <div class="avatar">
        @if($showUpload)
            <form id="profilePicForm" method="POST" action="{{ route('dashboard.profile.picture') }}" enctype="multipart/form-data" style="display:inline;">
                @csrf
                <label for="profilePicInput" style="cursor:pointer;display:block;position:relative;">
                    @if(auth()->user()->profile_picture && file_exists(public_path('profile_pictures/' . auth()->user()->profile_picture)))
                        <img src="{{ asset('profile_pictures/' . auth()->user()->profile_picture) }}" alt="Profile">
                    @else
                        {{ $initials }}
                    @endif
                    <span aria-hidden="true" style="position:absolute;right:6px;bottom:6px;background:#fff;color:var(--orange,#ff7a00);border-radius:50%;width:28px;height:28px;display:flex;align-items:center;justify-content:center;font-size:14px;box-shadow:0 2px 6px rgba(0,0,0,0.12);">📷</span>
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
