<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>HoD - Pending Leave Requests</title>
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
            <h2>Pending Leave Requests</h2>
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
    </main>
</div>
</body>
</html>
