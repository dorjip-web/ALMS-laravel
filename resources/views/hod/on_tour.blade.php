<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>HoD - Staff On Tour</title>
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
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
        /* Keep office order PDF button from overflowing table cells */
        .leave-history td{vertical-align:middle}
        .leave-history td .btn.btn-pdf{padding:6px 10px;border-radius:6px;display:inline-block;white-space:nowrap;box-shadow:none}
    </style>
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
            <h2>Staff Currently On Tour</h2>
            <div class="leave-history"><div class="table-wrap"><table class="users requests">
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
                        @foreach ($onTourStaff as $tour)
                            <tr>
                                <td>{{ $tour['employee_name'] ?? '-' }}</td>
                                <td>{{ $tour['department_name'] ?? '-' }}</td>
                                <td>{{ $tour['place'] ?? '-' }}</td>
                                <td>{{ ! empty($tour['start_date']) ? \Illuminate\Support\Carbon::parse($tour['start_date'])->format('d/m/Y') : '-' }}</td>
                                <td>{{ ! empty($tour['end_date']) ? \Illuminate\Support\Carbon::parse($tour['end_date'])->format('d/m/Y') : '-' }}</td>
                                <td>
                                    @php
                                        $totalDays = '-';
                                        if (!empty($tour['start_date'])) {
                                            $start = \Illuminate\Support\Carbon::parse($tour['start_date']);
                                            if (!empty($tour['end_date'])) {
                                                $end = \Illuminate\Support\Carbon::parse($tour['end_date']);
                                                $totalDays = $start->diffInDays($end) + 1;
                                            } else {
                                                // ongoing or open-ended: count up to today
                                                $totalDays = $start->diffInDays(now('Asia/Thimphu')) + 1;
                                            }
                                        }
                                    @endphp
                                    {{ $totalDays }}</td>
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
    </main>
</div>
</body>
</html>
