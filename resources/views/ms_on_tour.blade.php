<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Staff On Tour</title>
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
            <header><h1>Staff On Tour</h1></header>

            <section class="panel">
                <h2>Staff Currently On Tour</h2>
                <div class="leave-history"><div class="table-wrap"><table class="users requests">
                    <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Department</th>
                        <th>Place</th>
                        <th>From</th>
                        <th>To</th>
                        <th>Total Days</th>
                        <th>Purpose</th>
                        <th>Office Order</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if (empty($onTourStaff))
                        <tr><td colspan="8" class="empty">No staff on tour right now</td></tr>
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
        </div>
    </main>
</div>
</body>
</html>
