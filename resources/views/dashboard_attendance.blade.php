@php
    $fullName = $employee['employee_name'] ?? auth()->user()->name;
    $parts = preg_split('/\s+/', trim($fullName));
    $initials = count($parts) > 1
        ? strtoupper(substr($parts[0], 0, 1) . substr($parts[1], 0, 1))
        : strtoupper(substr($parts[0] ?? 'U', 0, 2));
    $roleName = strtolower(trim((string) ($employee['role_name'] ?? '')));
    $isMs = $roleName === 'ms' || str_contains($roleName, 'medical') || str_contains($roleName, 'superintendent');
    $showShift = isset($employee['department_id']) && (int) ($employee['department_id'] ?? 0) === 3;
    $leaveBalances = $leaveBalances ?? [];
@endphp

@if (request()->ajax())
    <section class="grid">
        <div style="grid-column: 1 / -1;">
            <h2 style="margin:0 0 12px 0;">Attendance Summary</h2>

            @if (empty($rows))
                <div class="summary-empty">No attendance records found.</div>
            @else
                <div class="leave-history" style="margin-top:0;">
                    <div class="table-wrap">
                        <table class="attendance users" style="width:100%;">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    @if($showShift)
                                        <th>Shift Type</th>
                                    @endif
                                    <th>Check-in</th>
                                    <th>Check-in Status</th>
                                    <th>Late Reason</th>
                                    <th>Check-out</th>
                                    <th>Check-out Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($rows as $r)
                                    <tr>
                                        <td>{{ $r['attendance_date'] ?? '-' }}</td>
                                        @if($showShift)
                                            <td>{{ $r['shift_type'] ?? '-' }}</td>
                                        @endif
                                        <td>{{ !empty($r['checkin']) ? \Illuminate\Support\Carbon::parse($r['checkin'], 'Asia/Thimphu')->format('g:i A') : '-' }}</td>
                                        <td>{{ $r['checkin_status'] ?? '-' }}</td>
                                        <td>{{ $r['late_reason'] ?? '-' }}</td>
                                        <td>{{ !empty($r['checkout']) ? \Illuminate\Support\Carbon::parse($r['checkout'], 'Asia/Thimphu')->format('g:i A') : '-' }}</td>
                                        <td>{{ $r['checkout_status'] ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </section>

    <script id="leave-balances-json" type="application/json">@json($leaveBalances)</script>

@else
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Attendance Summary</title>
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        table.attendance{width:100%;border-collapse:collapse}
        table.attendance th,table.attendance td{padding:8px;border:1px solid #e6eef4;text-align:left;font-size:14px}
        .summary-empty{margin-top:18px;padding:14px;border-radius:8px;background:#fff8e6;color:#8a3e14}
        /* Style logout button in topbar to match other pages */
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
    @include('partials.sidebar')

    <main class="main">
        <header class="topbar">
            <div class="search"><input placeholder="Search..."></div>
                <div class="notifications">
                    <a href="#" class="notif-btn" title="Notifications" aria-label="Notifications">✉️<span class="badge">0</span></a>
                </div>
            <div class="logout">
                <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                    @csrf
                    <button type="submit" style="background:#fff;padding:8px 12px;border-radius:6px;border:none;color:var(--orange);font-weight:600;cursor:pointer;">Logout</button>
                </form>
            </div>
        </header>

        <section class="grid">
            <div style="grid-column: 1 / -1;">
                <h2 style="margin:0 0 12px 0;">Attendance Summary</h2>

                @if (empty($rows))
                    <div class="summary-empty">No attendance records found.</div>
                @else
                    <div class="leave-history" style="margin-top:0;">
                        <div class="table-wrap">
                            <table class="attendance users" style="width:100%;">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        @if($showShift)
                                            <th>Shift Type</th>
                                        @endif
                                        <th>Check-in</th>
                                        <th>Check-in Status</th>
                                        <th>Late Reason</th>
                                        <th>Check-out</th>
                                        <th>Check-out Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($rows as $r)
                                        <tr>
                                            <td>{{ $r['attendance_date'] ?? '-' }}</td>
                                            @if($showShift)
                                                <td>{{ $r['shift_type'] ?? '-' }}</td>
                                            @endif
                                            <td>{{ !empty($r['checkin']) ? \Illuminate\Support\Carbon::parse($r['checkin'], 'Asia/Thimphu')->format('g:i A') : '-' }}</td>
                                            <td>{{ $r['checkin_status'] ?? '-' }}</td>
                                            <td>{{ $r['late_reason'] ?? '-' }}</td>
                                            <td>{{ !empty($r['checkout']) ? \Illuminate\Support\Carbon::parse($r['checkout'], 'Asia/Thimphu')->format('g:i A') : '-' }}</td>
                                            <td>{{ $r['checkout_status'] ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </div>
        </section>

        <script>
            (function () {
                const links = document.querySelectorAll('.menu a');
                function setActiveByHref() {
                    links.forEach(a => a.classList.toggle('active', a.href === window.location.href || a.getAttribute('href') === window.location.pathname));
                }
                links.forEach(a => a.addEventListener('click', () => {
                    links.forEach(x => x.classList.remove('active'));
                    a.classList.add('active');
                }));
                window.addEventListener('load', setActiveByHref);
            })();

            document.getElementById('profilePicInput')?.addEventListener('change', function () {
                if (this.files && this.files[0]) {
                    document.getElementById('profilePicForm').submit();
                }
            });
        </script>

    </main>
</div>

<script id="leave-balances-json" type="application/json">@json($leaveBalances)</script>

</body>
</html>
@endif
