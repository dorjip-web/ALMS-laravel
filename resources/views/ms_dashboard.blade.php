<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>MS Dashboard</title>
    <link rel="stylesheet" href="{{ asset('css/ms_dashboard.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
</head>
<body>
<div class="app">
    <aside class="sidebar">
        <div class="profile">
            <div class="avatar">M</div>
            <div class="username">{{ $username }}</div>
        </div>

        <nav class="menu">
            <a href="{{ route('dashboard') }}">Back to My Dashboard</a>
            <a href="{{ route('ms.dashboard') }}" class="active">MS Dashboard</a>
            <a href="#pending-approvals">Pending Approvals</a>
            <a href="#on-tour">Staff On Tour</a>
            <a href="#recent-decisions">Recent Decisions</a>
        </nav>
    </aside>

    <main class="main">
        <header class="topbar">
            <div class="search"><input placeholder="Search..."></div>
            <div class="logout">
                <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                    @csrf
                    <button type="submit">Logout</button>
                </form>
            </div>
        </header>

        <div class="container">
            <header>
                <h1>MS Dashboard</h1>
            </header>

            @if (! $authorized)
                <div class="access-denied">
                    <h2>Access Denied</h2>
                    <p>You are not assigned as MS.</p>
                    <a href="{{ route('dashboard') }}">Return to Dashboard</a>
                </div>
            @else
                @if (session('message'))
                    <div class="notice">{{ session('message') }}</div>
                @endif

                <section class="cards">
                    <div class="card">
                        <div class="card-title">Pending Approvals</div>
                        <div class="card-value">{{ $summary['pending'] }}</div>
                    </div>
                    <div class="card">
                        <div class="card-title">Approved Leaves</div>
                        <div class="card-value">{{ $summary['approved'] }}</div>
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
                    <h2 id="on-tour"><span style="color:#2563eb;">Staff Currently On Tour</span></h2>
                    <div class="leave-history"><div class="table-wrap"><table class="users">
                        <thead>
                        <tr>
                            <th><strong>Employee</strong></th>
                            <th><strong>Department</strong></th>
                            <th><strong>Place</strong></th>
                            <th><strong>From</strong></th>
                            <th><strong>To</strong></th>
                            <th><strong>Total Days</strong></th>
                            <th><strong>Purpose</strong></th>
                            <th><strong>Office Order</strong></th>
                        </tr>
                        </thead>
                        <tbody>
                        @if (empty($onTourStaff))
                            <tr><td colspan="7" class="empty">No staff on tour right now</td></tr>
                        @else
                            @foreach($onTourStaff as $tour)
                                <tr>
                                    <td>{{ $tour['employee_name'] ?? '-' }}</td>
                                    <td>{{ $tour['department_name'] ?? '-' }}</td>
                                    <td>{{ $tour['place'] ?? '-' }}</td>
                                    <td>{{ ! empty($tour['start_date']) ? \Illuminate\Support\Carbon::parse($tour['start_date'])->format('d M Y') : '-' }}</td>
                                    <td>{{ ! empty($tour['end_date']) ? \Illuminate\Support\Carbon::parse($tour['end_date'])->format('d M Y') : '-' }}</td>
                                    <td>
                                        @php
                                            $from = !empty($tour['start_date']) ? \Illuminate\Support\Carbon::parse($tour['start_date']) : null;
                                            $to = !empty($tour['end_date']) ? \Illuminate\Support\Carbon::parse($tour['end_date']) : null;
                                        @endphp
                                        {{ ($from && $to) ? $from->diffInDays($to) + 1 : '-' }}
                                    </td>
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
                    <h2 id="pending-approvals">Leaves Forwarded by HoD</h2>
                    <div class="leave-history"><div class="table-wrap"><table class="users">
                        <thead>
                        <tr>
                            <th><strong>Employee</strong></th>
                            <th><strong>Leave Type</strong></th>
                            <th><strong>From</strong></th>
                            <th><strong>To</strong></th>
                            <th><strong>Days</strong></th>
                            <th><strong>HoD Note</strong></th>
                            <th><strong>Action</strong></th>
                        </tr>
                        </thead>
                        <tbody>
                        @if (empty($forwardedRequests))
                            <tr><td colspan="7" class="empty">No pending approvals</td></tr>
                        @else
                            @foreach($forwardedRequests as $req)
                                <tr>
                                    <td>{{ $req['employee_name'] }}</td>
                                    <td>{{ $req['leave_type'] }}</td>
                                    <td>{{ \Illuminate\Support\Carbon::parse($req['from_date'])->format('d M') }}</td>
                                    <td>{{ \Illuminate\Support\Carbon::parse($req['to_date'])->format('d M') }}</td>
                                    <td>{{ $req['total_days'] }}</td>
                                    <td>{{ $req['hod_note'] ?? '-' }}</td>
                                    <td class="actions">
                                        <form method="POST" action="{{ route('ms.dashboard.action') }}" class="inline-form">
                                            @csrf
                                            <input type="hidden" name="request_id" value="{{ $req['application_id'] }}">
                                            <button type="submit" name="action" value="approve" class="btn btn-approve">Approve</button>
                                            <button type="submit" name="action" value="reject" class="btn btn-reject">Reject</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                        </tbody>
                    </table></div></div>
                </section>

                <section class="panel">
                    <h2>Direct Leave Requests</h2>
                    <div class="leave-history"><div class="table-wrap"><table class="users">
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
                        @if (empty($directRequests))
                            <tr><td colspan="7" class="empty">No direct requests</td></tr>
                        @else
                            @foreach($directRequests as $req)
                                <tr>
                                    <td>{{ $req['employee_name'] }}</td>
                                    <td>{{ $req['leave_type'] }}</td>
                                    <td>{{ \Illuminate\Support\Carbon::parse($req['from_date'])->format('d M') }}</td>
                                    <td>{{ \Illuminate\Support\Carbon::parse($req['to_date'])->format('d M') }}</td>
                                    <td>{{ $req['total_days'] }}</td>
                                    <td>{{ $req['reason'] }}</td>
                                    <td class="actions">
                                        <form method="POST" action="{{ route('ms.dashboard.action') }}" class="inline-form">
                                            @csrf
                                            <input type="hidden" name="application_id" value="{{ $req['application_id'] }}">
                                            <button type="submit" name="action" value="approve" class="btn btn-approve">Approve</button>
                                            <button type="submit" name="action" value="reject" class="btn btn-reject">Reject</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                        </tbody>
                    </table></div></div>
                </section>

                <section class="panel">
                    <h2 id="recent-decisions">Recent Decisions</h2>
                    <div class="leave-history"><div class="table-wrap"><table class="users">
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
                                    <td>{{ ! empty($r['medical_superintendent_action_at']) ? \Illuminate\Support\Carbon::parse($r['medical_superintendent_action_at'])->format('d M') : '-' }}</td>
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
        document.querySelectorAll('.sidebar .menu a').forEach(function (x) { x.classList.remove('active'); });
        this.classList.add('active');
    });
});
</script>
</body>
</html>
