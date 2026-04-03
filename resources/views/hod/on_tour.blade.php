<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>HoD - Staff On Tour</title>
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
            <h2>Staff Currently On Tour</h2>
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
                        <tr><td colspan="7" class="empty">No staff on tour right now</td></tr>
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
    </main>
</div>
</body>
</html>
