<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Tour Records</title>
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
</head>
<body>
@php
    $fullName = $employee['employee_name'] ?? auth()->user()->name;
    $parts = preg_split('/\s+/', trim($fullName));
    $initials = count($parts) > 1
        ? strtoupper(substr($parts[0], 0, 1) . substr($parts[1], 0, 1))
        : strtoupper(substr($parts[0] ?? 'U', 0, 2));
@endphp

<div class="app">
    @include('partials.sidebar')

    <main class="main">
        <header class="topbar">
            <div class="search"><input placeholder="Search..." disabled></div>
            <div class="logout">
                <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                    @csrf
                    <button type="submit" style="background:#fff;padding:8px 12px;border-radius:6px;border:none;color:var(--orange);font-weight:600;cursor:pointer;">Logout</button>
                </form>
            </div>
        </header>

        @if (session('flash_error'))
            <div class="flash flash-error">{{ session('flash_error') }}</div>
        @endif
        @if (session('flash_success'))
            <div class="flash flash-success">{{ session('flash_success') }}</div>
        @endif

        <section class="grid" style="grid-template-columns:1fr;">
            <div id="tour" class="card leave-card">
                <h3>Tour Records</h3>
                <form method="POST" action="{{ route('dashboard.tour.store') }}" class="leave-form" style="margin-top:10px;" enctype="multipart/form-data">
                    @csrf
                    <div class="row-grid-4">
                        <div class="col">
                            <label>Place</label>
                            <input type="text" name="place" value="{{ old('place') }}" placeholder="Tour place" required>
                        </div>
                        <div class="col">
                            <label>Start</label>
                            <input type="text" name="start_date" value="{{ old('start_date') }}" required min="{{ now('Asia/Thimphu')->toDateString() }}">
                        </div>
                        <div class="col">
                            <label>End</label>
                            <input type="text" name="end_date" value="{{ old('end_date') }}" required min="{{ now('Asia/Thimphu')->toDateString() }}">
                        </div>
                        <div class="col">
                            <label>Total Days</label>
                            <input type="text" id="tour-total-days" value="-" readonly>
                        </div>
                    </div>
                    <div class="row">
                        <label>Purpose</label>
                        <input type="text" name="purpose" value="{{ old('purpose') }}" placeholder="Purpose of tour" required>
                    </div>
                    <div class="row">
                        <label>Office Order (PDF)</label>
                        <input type="file" name="office_order_pdf" accept="application/pdf">
                    </div>
                    <div class="row">
                        <button class="btn" type="submit">Save Tour Record</button>
                    </div>
                </form>

                <div class="leave-history" style="margin-top:8px;">
                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Date</th><th>To date</th><th>Total Days</th><th>Destination</th><th>Purpose</th><th>Office Order</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($tourRecords ?? [] as $tour)
                                    <tr>
                                        <td>{{ !empty($tour['date']) ? \Illuminate\Support\Carbon::parse($tour['date'])->format('d/m/Y') : '-' }}</td>
                                        <td>{{ !empty($tour['to_date']) ? \Illuminate\Support\Carbon::parse($tour['to_date'])->format('d/m/Y') : '-' }}</td>
                                        <td>{{ $tour['total_days'] ?? '-' }}</td>
                                        <td>{{ $tour['destination'] ?? '-' }}</td>
                                        <td>{{ $tour['purpose'] ?? '-' }}</td>
                                        <td>
                                            @if (!empty($tour['office_order_pdf']))
                                                <a href="{{ asset('storage/' . $tour['office_order_pdf']) }}" target="_blank">View PDF</a>
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5">No tour records found</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </main>
</div>
<script>
    document.getElementById('profilePicInput')?.addEventListener('change', function () {
        if (this.files && this.files[0]) {
            document.getElementById('profilePicForm').submit();
        }
    });

    (function () {
        const startInput = document.querySelector('input[name="start_date"]');
        const endInput = document.querySelector('input[name="end_date"]');
        const totalInput = document.getElementById('tour-total-days');

        function updateTotalDays() {
            if (!startInput || !endInput || !totalInput) return;
            if (!startInput.value || !endInput.value) {
                totalInput.value = '-';
                return;
            }

            const start = new Date(startInput.value + 'T00:00:00');
            const end = new Date(endInput.value + 'T00:00:00');
            if (Number.isNaN(start.getTime()) || Number.isNaN(end.getTime()) || end < start) {
                totalInput.value = '-';
                return;
            }

            const diffMs = end.getTime() - start.getTime();
            const days = Math.floor(diffMs / 86400000) + 1;
            totalInput.value = days + (days === 1 ? ' day' : ' days');
        }

        startInput?.addEventListener('change', updateTotalDays);
        endInput?.addEventListener('change', updateTotalDays);
        updateTotalDays();
    })();
</script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        (function(){
            try {
                flatpickr("input[name='start_date'], input[name='end_date']", {
                    altInput: true,
                    altFormat: 'd/m/Y',
                    dateFormat: 'Y-m-d',
                    allowInput: true
                });
            } catch (e) { console.warn('flatpickr init failed', e); }
        })();
    </script>
</body>
</html>
