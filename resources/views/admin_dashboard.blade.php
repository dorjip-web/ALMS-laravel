@extends('admin_layout')

@section('pageTitle', 'Admin Dashboard')

@section('content')
<!-- Admin dashboard quick links -->
<section class="grid">
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:16px;">
        @php
            $tiles = [
                ['label' => 'User Management', 'href' => route('admin.users.index'), 'color' => '#5b2a86', 'icon' => '👥'],
                ['label' => 'Roles & Permissions', 'href' => route('admin.roles_permissions'), 'color' => '#0b7a75', 'icon' => '🔐'],
                ['label' => 'Department & HoD Management', 'href' => route('admin.departments_hods.index'), 'color' => '#2b7aab', 'icon' => '🏛️'],
                ['label' => 'Leave Balance', 'href' => route('admin.leave_balances.index'), 'color' => '#f6c235', 'icon' => '📊'],
                ['label' => 'Staff on Tour', 'href' => route('admin.on_tour'), 'color' => '#ff7a00', 'icon' => '🧭'],
                ['label' => 'Adhoc Requests', 'href' => route('admin.adhoc'), 'color' => '#c92b2b', 'icon' => '📌'],
                ['label' => 'Device Binding', 'href' => url('/admin/device-bindings'), 'color' => '#6e6e6e', 'icon' => '📱'],
                ['label' => 'Leave Management', 'href' => route('admin.leave_types.index'), 'color' => '#8a4baf', 'icon' => '🗂️'],
                ['label' => 'Attendance Logs', 'href' => route('admin.attendance_logs.index'), 'color' => '#0d6efd', 'icon' => '🕒'],
                ['label' => 'Leave Records', 'href' => route('admin.leave_records.index'), 'color' => '#20c997', 'icon' => '📁'],
                ['label' => 'Settings', 'href' => url('settings.php'), 'color' => '#6c757d', 'icon' => '⚙️'],
            ];
        @endphp

        @foreach ($tiles as $t)
            <a href="{{ $t['href'] }}" class="card" style="background:{{ $t['color'] }};color:#fff;text-decoration:none;">
                <div style="display:flex;align-items:center;gap:16px;padding:18px;">
                    <div style="width:64px;height:64px;background:rgba(255,255,255,0.06);border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:26px;">{{ $t['icon'] }}</div>
                    <div style="flex:1">
                        <div style="font-size:18px;font-weight:700">{{ $t['label'] }}</div>
                        <div style="opacity:0.9">Quick access</div>
                    </div>
                </div>
            </a>
        @endforeach
    </div>
</section>
@endsection
