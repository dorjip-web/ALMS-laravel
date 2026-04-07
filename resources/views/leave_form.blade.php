<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Leave Request</title>
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        .leave-form .row-grid-3 .col input, .leave-form .row-grid-3 .col select{min-height:44px}
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
@php $fullName = $employee['employee_name'] ?? auth()->user()->name; @endphp
<div class="app">
    @include('partials.sidebar')
    <main class="main">
        <header class="topbar">
            <div class="search"><input placeholder="Search..." disabled></div>
            <div class="logout">
                <form method="POST" action="{{ route('logout') }}" style="display:inline;">@csrf<button type="submit">Logout</button></form>
            </div>
        </header>

        <section class="grid" style="grid-template-columns:1fr;">
            <div class="card leave-card">
                <h3>Leave</h3>
                @if(session('flash_success'))<div class="flash flash-success">{{ session('flash_success') }}</div>@endif
                @if(session('flash_error'))<div class="flash flash-error">{{ session('flash_error') }}</div>@endif
                @if ($errors->any())<div class="flash flash-error">{{ $errors->first() }}</div>@endif

                <form method="post" action="{{ route('dashboard.leave') }}" class="leave-form">
                    @csrf
                    <div class="row-grid-3">
                        <div class="col">
                            <label>Type</label>
                            <select name="leave_type_id" id="leave-type-select" class="form-control">
                                @forelse ($leaveTypes as $lt)
                                    <option value="{{ $lt['leave_type_id'] }}" {{ ((int) old('leave_type_id') === (int) $lt['leave_type_id']) ? 'selected' : '' }}>{{ str_replace('_', ' ', $lt['leave_name']) }}</option>
                                @empty
                                    <option value="">No leave type found</option>
                                @endforelse
                            </select>
                        </div>
                        <div class="col">
                            <label>Submit To</label>
                            <select name="submit_to" class="form-control">
                                <option value="hod" {{ old('submit_to') === 'hod' ? 'selected' : '' }}>HoD</option>
                                <option value="ms" {{ old('submit_to') === 'ms' ? 'selected' : '' }}>Medical Superintendent</option>
                            </select>
                        </div>
                        <div class="col">
                            <label>Balance</label>
                            <input type="text" id="leave-balance" readonly class="form-control">
                        </div>

                        <div class="col">
                            <label>Start</label>
                            <input type="text" name="from_date" required min="{{ now('Asia/Thimphu')->toDateString() }}" class="form-control" value="{{ old('from_date') }}">
                        </div>
                        <div class="col">
                            <label>End</label>
                            <input type="text" name="to_date" required min="{{ now('Asia/Thimphu')->toDateString() }}" class="form-control" value="{{ old('to_date') }}">
                        </div>
                        <div class="col">
                            <label>Total Days</label>
                            <input name="total_days" type="number" step="0.5" min="0.5" placeholder="e.g. 1 or 0.5" class="form-control" value="{{ old('total_days') }}">
                        </div>
                    </div>

                    <div class="row">
                        <label>Reason</label>
                        <input name="reason" placeholder="Reason" required class="form-control" value="{{ old('reason') }}">
                    </div>

                    <div class="row">
                        <button class="btn" type="submit">Submit</button>
                    </div>
                </form>

                <div class="leave-history" style="margin-top:12px">
                    <h4>Leave History</h4>
                    <div class="table-wrap">
                        <table class="users">
                            <thead>
                                <tr>
                                        <th>Type</th><th>S.date</th><th>E.date</th><th>Reason</th><th>Days</th><th>HoD</th><th>HoD Status</th><th>MS</th><th>MS Status</th>
                                    </tr>
                            </thead>
                            <tbody>
                                @forelse ($leaveApplications as $lv)
                                    <tr>
                                        <td>{{ $lv['type'] ?? '-' }}</td>
                                        <td>{{ !empty($lv['start_date']) ? \Illuminate\Support\Carbon::parse($lv['start_date'])->format('d/m/Y') : '-' }}</td>
                                        <td>{{ !empty($lv['end_date']) ? \Illuminate\Support\Carbon::parse($lv['end_date'])->format('d/m/Y') : '-' }}</td>
                                        <td>{{ $lv['reason'] ?? '-' }}</td>
                                        <td>{{ $lv['days'] ?? '-' }}</td>
                                        <td>{{ empty($lv['hod_status']) ? '' : 'HoD' }}</td>
                                        <td>@if (! empty($lv['hod_status'])) <span class="leave-status-badge status-{{ strtolower($lv['hod_status']) }}">{{ ucfirst($lv['hod_status']) }}</span>@endif</td>
                                        <td>{{ empty($lv['ms_status']) ? '' : 'MS' }}</td>
                                        <td>@if (! empty($lv['ms_status'])) <span class="leave-status-badge status-{{ strtolower($lv['ms_status']) }}">{{ ucfirst($lv['ms_status']) }}</span>@endif</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="9">No leave records</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </main>
</div>
<script id="leave-balances-json" type="application/json">@json($leaveBalances)</script>
<script>
    (function () {
        try {
            const el = document.getElementById('leave-balances-json');
            const leaveBalances = el ? JSON.parse(el.textContent || '{}') : {};
            const sel = document.getElementById('leave-type-select');
            const bal = document.getElementById('leave-balance');
            if (sel && bal) {
                const update = () => {
                    const id = parseInt(sel.value, 10);
                    bal.value = typeof leaveBalances[id] !== 'undefined' ? leaveBalances[id] : '-';
                };
                sel.addEventListener('change', update);
                update();
            }
        } catch (e) { console.warn('leaveBalances init failed', e); }
    })();
</script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    (function(){
        try {
            flatpickr("input[name='from_date'], input[name='to_date']", {
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
