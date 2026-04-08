<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Staff On Tour</title>
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
</head>
<body>
<div class="app">
    @include('partials.ms_sidebar')

    <main class="main">
        <header class="topbar">
            <div class="search"><input placeholder="Search..." disabled></div>
            <div class="logout">
                <form method="POST" action="{{ route('logout') }}" style="display:inline;">@csrf<button type="submit">Logout</button></form>
            </div>
        </header>

        <section class="grid" style="grid-template-columns:1fr; padding: 18px 0;">
            <div class="card leave-card">
                <h3>Staff Currently On Tour</h3>

                @if(session('flash_error'))
                    <div class="flash flash-error">{{ session('flash_error') }}</div>
                @endif
                @if(session('flash_success'))
                    <div class="flash flash-success">{{ session('flash_success') }}</div>
                @endif
                @if ($errors->any())
                    <div class="flash flash-error">{{ $errors->first() }}</div>
                @endif

                <!-- Add Tour Record form removed per user request -->

                <h4 style="margin-top:18px">Recent Staff On Tour</h4>
                <div class="leave-history" style="margin-top:8px;">
                    <div class="table-wrap">
                        <table class="users">
                            <thead>
                                <tr>
                                    <th>Employee</th>
                                    <th>Designation</th>
                                    <th>Department</th>
                                    <th>Place</th>
                                    <th>From</th>
                                    <th>To</th>
                                    <th>Total Days</th>
                                    <th>Purpose</th>
                                    <th>Office Order</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (empty($onTourStaff))
                                    <tr><td colspan="10" class="summary-empty">No staff on tour right now</td></tr>
                                @else
                                    @foreach($onTourStaff as $tour)
                                        <tr>
                                            <td>{{ $tour['employee_name'] ?? ($tour['employee'] ?? '-') }}</td>
                                            <td>{{ $tour['designation'] ?? '-' }}</td>
                                            <td>{{ $tour['department_name'] ?? '-' }}</td>
                                            <td>{{ $tour['place'] ?? '-' }}</td>
                                            <td>{{ ! empty($tour['start_date']) ? \Illuminate\Support\Carbon::parse($tour['start_date'])->format('d M Y') : '-' }}</td>
                                            <td>{{ ! empty($tour['end_date']) ? \Illuminate\Support\Carbon::parse($tour['end_date'])->format('d M Y') : '-' }}</td>
                                            <td>{{ (!empty($tour['start_date']) && !empty($tour['end_date'])) ? (\Illuminate\Support\Carbon::parse($tour['start_date'])->diffInDays(\Illuminate\Support\Carbon::parse($tour['end_date'])) + 1) : '-' }}</td>
                                            <td>{{ $tour['purpose'] ?? '-' }}</td>
                                            <td>@if(!empty($tour['office_order_pdf'])) <a href="{{ asset('storage/' . $tour['office_order_pdf']) }}" target="_blank" class="btn btn-pdf">View PDF</a> @else - @endif</td>
                                            <td style="white-space:nowrap">
                                                <a class="action-link" href="{{ route('ms.on_tour.edit', $tour['id'] ?? $tour['employee_id'] ?? '') }}">Edit</a>
                                                |
                                                <form method="POST" action="{{ route('ms.on_tour.delete', $tour['id'] ?? $tour['employee_id'] ?? '') }}" style="display:inline" onsubmit="return confirm('Delete this tour record?');">
                                                    @csrf
                                                    <button type="submit" style="background:none;border:none;color:var(--accent);font-weight:700;cursor:pointer;padding:0;margin-left:6px">Delete</button>
                                                </form>
                                            </td>
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
