<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>HoD Adhoc Requests</title>
    <link rel="stylesheet" href="/css/dashboard.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        /* Match the adhoc/staff-on-tour dashboard look */
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
            <div class="logout">
                <form method="POST" action="/logout">
                    @csrf
                    <button type="submit">Logout</button>
                </form>
            </div>
        </header>

        <section>
            <div>
                <h3 style="text-align:left;">Adhoc Requests</h3>

                <div class="leave-history">
                    <div class="table-wrap">
                        <table class="users requests">
                            <thead>
                                <tr>
                                    <th><strong>Employee</strong></th>
                                    <th><strong>Department</strong></th>
                                    <th><strong>Date</strong></th>
                                    <th><strong>Purpose</strong></th>
                                    <th><strong>Remarks</strong></th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($requests->isEmpty())
                                    <tr><td colspan="5" class="empty">No adhoc requests found.</td></tr>
                                @else
                                    @foreach($requests as $req)
                                        <tr>
                                            <td>{{ $req->name }}</td>
                                            <td>{{ $req->department ?? $req->department_id ?? '' }}</td>
                                            <td>{{ $req->date }}</td>
                                            <td>{{ ucfirst($req->purpose) }}</td>
                                            <td>{{ $req->remarks }}</td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </main>
</div>
</body>
</html>
