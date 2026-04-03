<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>HoD - Recent Leave Actions</title>
    <link rel="stylesheet" href="{{ asset('css/hod_dashboard.css') }}">
</head>
<body>
<div class="app">
    @include('partials.hod_sidebar')
    <main class="main">
        <header class="topbar">
            <div class="search"><input placeholder="Search..."></div>
            <div class="logout"><form method="POST" action="{{ route('logout') }}">@csrf<button type="submit">Logout</button></form></div>
        </header>

        <section class="panel">
            <h2>Recent Leave Actions</h2>
            <div class="leave-history"><div class="table-wrap"><table class="users recent">
                <thead>
                    <tr>
                        <th><strong>Employee</strong></th>
                        <th><strong>Leave</strong></th>
                        <th><strong>HoD Action</strong></th>
                        <th><strong>Date</strong></th>
                    </tr>
                </thead>
                <tbody>
                    @if (empty($recent))
                        <tr><td colspan="4" class="empty">No recent actions</td></tr>
                    @else
                        @foreach ($recent as $r)
                            @php $act = strtolower(trim((string) ($r['action'] ?? ''))); @endphp
                            <tr>
                                <td>{{ $r['employee'] }}</td>
                                <td>{{ $r['leave_name'] }}</td>
                                <td>
                                    @if ($act === 'forwarded')
                                        <span class="status-forwarded">Forwarded</span>
                                    @elseif ($act === 'rejected')
                                        <span class="status-rejected">Rejected</span>
                                    @else
                                        {{ $r['action'] }}
                                    @endif
                                </td>
                                <td>{{ ! empty($r['action_at']) ? \Illuminate\Support\Carbon::parse($r['action_at'])->format('d M') : '-' }}</td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table></div></div>
        </section>
    </main>
</div>
</body>
</html>
