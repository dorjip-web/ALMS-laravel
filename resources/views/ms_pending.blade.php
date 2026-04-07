<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Pending Approvals</title>
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
            <header><h1>Pending Approvals</h1></header>

            <section class="panel">
                <h2>Leaves Forwarded by HoD</h2>
                <div class="leave-history"><div class="table-wrap"><table class="users requests">
                    <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Leave Type</th>
                        <th>From</th>
                        <th>To</th>
                        <th>Days</th>
                        <th>Reason</th>
                        <th>Action</th>
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
                                <td>{{ $req['reason'] ?? '-' }}</td>
                                <td class="actions">
                                    <form method="POST" action="{{ route('ms.dashboard.action') }}" class="inline-form">@csrf
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
                <div class="leave-history"><div class="table-wrap"><table class="users requests">
                    <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Leave Type</th>
                        <th>From</th>
                        <th>To</th>
                        <th>Days</th>
                        <th>Reason</th>
                        <th>Action</th>
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
                                    <form method="POST" action="{{ route('ms.dashboard.action') }}" class="inline-form">@csrf
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
        </div>
    </main>
</div>
</body>
</html>
