@extends('admin_layout')

@section('pageTitle', 'Admin Dashboard')

@section('content')
<!-- Admin dashboard quick links -->
<section class="grid">
    <style>
        .quick-tiles { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; }
        /* Ensure quick-tiles span the full .grid container (prevent empty second column) */
        .grid > .quick-tiles { grid-column: 1 / -1; }
        @media (max-width: 900px) { .quick-tiles { grid-template-columns: repeat(2, 1fr); } }
        @media (max-width: 480px) { .quick-tiles { grid-template-columns: 1fr; } }

        /* Card: book-fold (folded corner) style */
        .quick-tiles .card {
            display: block;
            width: 100%;
            border-radius: 10px;
            overflow: hidden;
            background: var(--tile-bg);
            color: #fff;
            text-decoration: none;
            position: relative;
            box-shadow: 0 8px 18px rgba(14,30,37,0.08);
            transition: transform .12s ease, box-shadow .12s ease;
        }
        .quick-tiles .card:hover { transform: translateY(-4px); box-shadow: 0 14px 30px rgba(14,30,37,0.12); }

        /* folded-corner using pseudo-element */
        .quick-tiles .card::after {
            content: '';
            position: absolute;
            right: 0;
            top: 0;
            width: 56px;
            height: 56px;
            background: linear-gradient(135deg, rgba(255,255,255,0.08) 0%, rgba(255,255,255,0.02) 100%);
            clip-path: polygon(100% 0, 100% 100%, 0 100%);
            transform-origin: top right;
        }
        .quick-tiles .card .fold-label{
            position:absolute;right:8px;top:8px;font-size:11px;opacity:0.95;padding:2px 6px;border-radius:4px;background:rgba(255,255,255,0.06)
        }

        .quick-tiles .card .icon-wrap{
            width:64px;height:64px;background:rgba(255,255,255,0.08);border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:26px
        }
    </style>
    <div class="quick-tiles">
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
            <a href="{{ $t['href'] }}" class="card" style="--tile-bg: {{ $t['color'] }};">
                <div style="display:flex;align-items:center;gap:16px;padding:18px;">
                    <div class="icon-wrap">{{ $t['icon'] }}</div>
                    <div style="flex:1">
                        <div style="font-size:18px;font-weight:700">{{ $t['label'] }}</div>
                        <div style="opacity:0.9">Quick link</div>
                        <div class="fold-label">Admin</div>
                    </div>
                </div>
            </a>
        @endforeach
    </div>
</section>
@endsection
