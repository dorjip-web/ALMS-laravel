<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>HoD Dashboard</title>
    <link rel="stylesheet" href="{{ asset('css/hod_dashboard.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body{font-family: 'Inter', system-ui, -apple-system, 'Segoe UI', Roboto, Arial, sans-serif;}
        .topbar .logout form button{background:transparent;border:0;padding:6px 10px;border-radius:6px;font-weight:600}
        .topbar .logout form button:hover{background:#f8f9fa}
    </style>
</head>
<body>
<div class="app">
    <aside class="sidebar">
        <div class="profile">
            <div class="avatar">H</div>
            <div class="username">{{ $username }}</div>
        </div>

        <nav class="menu">
            <a href="{{ route('dashboard') }}">Back to Dashboard</a>
            <a href="{{ route('hod.dashboard') }}" class="active">HoD Dashboard</a>
            <a href="{{ route('hod.staff_list') }}">View Staff List</a>
            <a href="#pending-requests">Pending Leave Requests</a>
            <a href="#on-tour">Staff On Tour</a>
            <a href="#recent-actions">Recent Leave Actions</a>
        </nav>
    </aside>

    <main class="main">
        <header class="topbar">
            <div class="search"><input placeholder="Search..."></div>
            <div class="logout">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit">Logout</button>
                </form>
            </div>
        </header>

        <div class="container">
            <header>
                <h1>HoD Dashboard</h1>
            </header>

            @if (! $authorized)
                <div class="access-denied">
                    <h2>Access Denied</h2>
                    <p>You are not assigned as HoD.</p>
                    <a href="{{ route('dashboard') }}">Return to Dashboard</a>
                </div>
            @else
                @if (session('message'))
                    <div class="notice">{{ session('message') }}</div>
                @endif

                <section class="cards">
                    <div class="card">
                        <div class="card-title">Total Staff</div>
                        <div class="card-value">{{ $totalStaff ?? 0 }}</div>
                    </div>
                    <div class="card">
                        <div class="card-title">Pending</div>
                        <div class="card-value">{{ $summary['pending'] }}</div>
                    </div>
                    <div class="card">
                        <div class="card-title">Forwarded to MS</div>
                        <div class="card-value">{{ $summary['forwarded'] }}</div>
                    </div>
                    <div class="card">
                        <div class="card-title">Rejected</div>
                        <div class="card-value">{{ $summary['rejected'] }}</div>
                    </div>
                    <div class="card">
                        <div class="card-title">Staff On Tour</div>
                        <div class="card-value">{{ $onTourCount ?? 0 }}</div>
                    </div>
                </section>

                <section class="panel">
                    <h2 id="on-tour">Staff Currently On Tour</h2>
                    <div class="leave-history"><div class="table-wrap"><table class="users requests">
                        <thead>
                            <tr>
                                <th><strong>Employee</strong></th>
                                <th><strong>Department</strong></th>
                                <th><strong>Place</strong></th>
                                <th><strong>From</strong></th>
                                <th><strong>To</strong></th>
                                <th><strong>Purpose</strong></th>
                                <th><strong>Office Order</strong></th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (empty($onTourStaff))
                                <tr><td colspan="6" class="empty">No staff on tour right now</td></tr>
                            @else
                                @foreach ($onTourStaff as $tour)
                                    <tr>
                                        <td>{{ $tour['employee_name'] ?? '-' }}</td>
                                        <td>{{ $tour['department_name'] ?? '-' }}</td>
                                        <td>{{ $tour['place'] ?? '-' }}</td>
                                        <td>{{ ! empty($tour['start_date']) ? \Illuminate\Support\Carbon::parse($tour['start_date'])->format('d M Y') : '-' }}</td>
                                        <td>{{ ! empty($tour['end_date']) ? \Illuminate\Support\Carbon::parse($tour['end_date'])->format('d M Y') : '-' }}</td>
                                        <td>{{ $tour['purpose'] ?? '-' }}</td>
                                        <td>
                                            @if (!empty($tour['office_order_pdf']))
                                                <a href="{{ asset('storage/' . $tour['office_order_pdf']) }}" target="_blank" class="btn btn-pdf">View PDF</a>
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table></div></div>
                </section>

                <section class="panel">
                    <h2 id="pending-requests">Pending Leave Requests</h2>
                    <div class="leave-history"><div class="table-wrap"><table class="users requests">
                        <thead>
                            <tr>
                                <th><strong>Employee</strong></th>
                                <th><strong>Leave</strong></th>
                                <th><strong>From</strong></th>
                                <th><strong>To</strong></th>
                                <th><strong>Days</strong></th>
                                <th><strong>Reason</strong></th>
                                <th><strong>Action</strong></th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (empty($pending))
                                <tr><td colspan="7" class="empty">No pending requests</td></tr>
                            @else
                                @foreach ($pending as $req)
                                    <tr>
                                        <td>{{ $req['employee_name'] }}</td>
                                        <td>{{ $req['leave_name'] }}</td>
                                        <td>{{ \Illuminate\Support\Carbon::parse($req['from_date'])->format('d M') }}</td>
                                        <td>{{ \Illuminate\Support\Carbon::parse($req['to_date'])->format('d M') }}</td>
                                        <td>{{ $req['total_days'] }}</td>
                                        <td>{{ $req['reason'] }}</td>
                                        <td class="actions">
                                            <form method="POST" action="{{ route('hod.dashboard.action') }}" class="inline-form">
                                                @csrf
                                                <input type="hidden" name="request_id" value="{{ $req['application_id'] }}">
                                                <button type="submit" name="action" value="Forward" class="btn btn-forward">Forward</button>
                                                <button type="submit" name="action" value="Reject" class="btn btn-reject">Reject</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table></div></div>
                </section>

                <section class="panel">
                    <h2 id="recent-actions">Recent Leave Actions</h2>
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
            @endif
        </div>
    </main>
</div>

<script>
    document.querySelectorAll('.sidebar .menu a').forEach(function (a) {
        a.addEventListener('click', function () {
            document.querySelectorAll('.sidebar .menu a').forEach(function (x) {
                x.classList.remove('active');
            });
            this.classList.add('active');
        });
    });
</script>
</body>
</html>
