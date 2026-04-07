<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Recent Decisions</title>
    <link rel="stylesheet" href="{{ asset('css/ms_dashboard.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
</head>
<body>
<div class="app">
    @include('partials.ms_sidebar')

    <main class="main">
        <header class="topbar">
            <div class="search"><input placeholder="Search..."></div>
            <div class="logout">
                <form method="POST" action="{{ route('logout') }}" style="display:inline;">@csrf<button type="submit">Logout</button></form>
            </div>
        </header>

        <div class="container">
            <header><h1>Recent Decisions</h1></header>

            <section class="panel">
                <h2>Recent MS Decisions</h2>
                <div class="leave-history recent"><div class="table-wrap recent"><table class="users recent">
                    <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Leave</th>
                        <th>MS Action</th>
                        <th>Date</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if (empty($recentDecisions))
                        <tr><td colspan="4" class="empty">No recent decisions</td></tr>
                    @else
                        @foreach ($recentDecisions as $r)
                            @php $s = strtolower(trim((string) ($r['medical_superintendent_status'] ?? ''))); @endphp
                            <tr>
                                <td>{{ $r['employee_name'] }}</td>
                                <td>{{ $r['leave_name'] }}</td>
                                <td>
                                    @if ($s === 'approved')
                                        <span class="status-approved">Approved</span>
                                    @elseif ($s === 'rejected')
                                        <span class="status-rejected">Rejected</span>
                                    @else
                                        {{ $r['medical_superintendent_status'] }}
                                    @endif
                                </td>
                                <td>{{ ! empty($r['medical_superintendent_action_at']) ? \Illuminate\Support\Carbon::parse($r['medical_superintendent_action_at'])->format('d/m/Y') : '-' }}</td>
                            </tr>
                        @endforeach
                    @endif
                    </tbody>
                </table></div></div>
            </section>
        </div>
    </main>
</div>
</body>
</html>
