<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>HoD - Recent Leave Actions</title>
    <link rel="stylesheet" href="/css/dashboard.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        /* Match logout button style to other dashboard pages */
        .topbar .logout form button{
            background:#fff;
            padding:8px 12px;
            border-radius:6px;
            border:none;
            color:var(--orange);
            font-weight:600;
            cursor:pointer;
        }
        .topbar .logout form button:hover{filter:brightness(0.97)}
    </style>
</head>
<body>
<div class="app">
    @include('partials.hod_sidebar')
    <main class="main">
        <header class="topbar">
            <div class="search"><input placeholder="Search..."></div>
            <div class="logout"><form method="POST" action="/logout">@csrf<button type="submit">Logout</button></form></div>
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
                                        @if (! empty($r['reject_note']))
                                            <span class="status-rejected js-reject-note" data-note="{{ e($r['reject_note']) }}" style="cursor:pointer;" title="Click to view rejection reason">Rejected</span>
                                        @else
                                            <span class="status-rejected">Rejected</span>
                                        @endif
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
<script>
    document.querySelectorAll('.js-reject-note').forEach(function (el) {
        el.addEventListener('click', function () {
            const note = (el.getAttribute('data-note') || '').trim();
            if (!note) return;
            alert('Rejection reason:\n\n' + note);
        });
    });
</script>
</body>
</html>
