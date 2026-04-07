@extends('admin_layout')

@section('pageTitle', 'Admin Dashboard')

@section('content')
<!-- Admin dashboard quick links -->
<section class="grid">
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;">
        <a href="{{ route('admin.users.index') }}" class="card" style="background:#5b2a86;color:#fff;text-decoration:none;">
            <div style="display:flex;align-items:center;gap:16px;padding:18px;">
                <div style="width:72px;height:72px;background:rgba(255,255,255,0.08);border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:28px;">👥</div>
                <div style="flex:1">
                    <div style="font-size:20px;font-weight:700">Total Staff</div>
                    <div style="opacity:0.9">View your department staff</div>
                </div>
                <div style="font-size:28px;font-weight:700">{{ $totalStaff ?? 0 }}</div>
            </div>
        </a>

        <a href="{{ route('admin.adhoc') }}" class="card" style="background:#0097d1;color:#fff;text-decoration:none;">
            <div style="display:flex;align-items:center;gap:16px;padding:18px;">
                <div style="width:72px;height:72px;background:rgba(255,255,255,0.08);border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:28px;">⏳</div>
                <div style="flex:1">
                    <div style="font-size:20px;font-weight:700">Pending</div>
                    <div style="opacity:0.9">Requests awaiting your action</div>
                </div>
                <div style="font-size:28px;font-weight:700">{{ $pendingRequests ?? 0 }}</div>
            </div>
        </a>

        <a href="{{ url('/admin/device-bindings') }}" class="card" style="background:#6e6e6e;color:#fff;text-decoration:none;">
            <div style="display:flex;align-items:center;gap:16px;padding:18px;">
                <div style="width:72px;height:72px;background:rgba(255,255,255,0.06);border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:28px;">✉️</div>
                <div style="flex:1">
                    <div style="font-size:20px;font-weight:700">Forwarded to MS</div>
                    <div style="opacity:0.9">Recently forwarded</div>
                </div>
                <div style="font-size:28px;font-weight:700">{{ $forwardedToMs ?? 0 }}</div>
            </div>
        </a>

        <a href="{{ route('admin.adhoc') }}" class="card" style="background:#f6c235;color:#000;text-decoration:none;">
            <div style="display:flex;align-items:center;gap:16px;padding:18px;">
                <div style="width:72px;height:72px;background:rgba(0,0,0,0.04);border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:28px;">📌</div>
                <div style="flex:1">
                    <div style="font-size:20px;font-weight:700">Adhoc Requests</div>
                    <div style="opacity:0.9">Manage adhoc duty requests</div>
                </div>
                <div style="font-size:28px;font-weight:700">{{ $adhocCount ?? 0 }}</div>
            </div>
        </a>

        <a href="{{ route('admin.adhoc') }}" class="card" style="background:#c92b2b;color:#fff;text-decoration:none;">
            <div style="display:flex;align-items:center;gap:16px;padding:18px;">
                <div style="width:72px;height:72px;background:rgba(255,255,255,0.06);border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:28px;">❌</div>
                <div style="flex:1">
                    <div style="font-size:20px;font-weight:700">Rejected</div>
                    <div style="opacity:0.9">Requests you rejected</div>
                </div>
                <div style="font-size:28px;font-weight:700">{{ $rejectedCount ?? 0 }}</div>
            </div>
        </a>

        <a href="{{ route('admin.on_tour') }}" class="card" style="background:#ff7a00;color:#fff;text-decoration:none;">
            <div style="display:flex;align-items:center;gap:16px;padding:18px;">
                <div style="width:72px;height:72px;background:rgba(255,255,255,0.06);border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:28px;">🧭</div>
                <div style="flex:1">
                    <div style="font-size:20px;font-weight:700">Staff On Tour</div>
                    <div style="opacity:0.9">Current tours in your depts</div>
                </div>
                <div style="font-size:28px;font-weight:700">{{ $staffOnTour ?? 0 }}</div>
            </div>
        </a>
    </div>
</section>
@endsection
