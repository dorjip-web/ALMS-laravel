<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Adhoc Requests</title>
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
</head>
<body>
@php
    $fullName = $employee['employee_name'] ?? auth()->user()->name;
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

        <section class="grid" style="grid-template-columns:1fr;">
            <div class="card leave-card">
                <h3>Adhoc Request</h3>

                @if(session('flash_error'))
                    <div class="flash flash-error">{{ session('flash_error') }}</div>
                @endif
                @if(session('flash_success'))
                    <div class="flash flash-success">{{ session('flash_success') }}</div>
                @endif
                @if ($errors->any())
                    <div class="flash flash-error">{{ $errors->first() }}</div>
                @endif

                @if (! $tableExists)
                    <div class="summary-empty">Adhoc requests table not found.</div>
                @else
                    <form method="POST" action="{{ route('dashboard.adhoc_requests.store') }}" class="leave-form" style="margin-bottom:18px;">
                        @csrf
                        <div class="row-grid">
                            <div class="col">
                                <label>Date</label>
                                <input type="date" name="date" required class="form-control" value="{{ old('date', now()->toDateString()) }}">
                            </div>
                            <div class="col">
                                <label>Purpose</label>
                                <select name="purpose" required class="form-control">
                                    <option value="meeting" {{ old('purpose') === 'meeting' ? 'selected' : '' }}>Meeting</option>
                                    <option value="emergency" {{ old('purpose') === 'emergency' ? 'selected' : '' }}>Emergency</option>
                                </select>
                            </div>
                        </div>

                        <div class="row" style="margin-top:12px">
                            <label>Remarks</label>
                            <input type="text" name="remarks" maxlength="255" class="form-control" value="{{ old('remarks') }}">
                        </div>

                        <div style="margin-top:12px">
                            <button type="submit" class="btn">Submit Adhoc Request</button>
                        </div>
                    </form>

                    <h4>Your recent adhoc requests</h4>
                    @if (empty($rows))
                        <div class="summary-empty">No adhoc requests found.</div>
                    @else
                        <div class="leave-history" style="margin-top:8px;">
                            <div class="table-wrap">
                                <table class="users">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Purpose</th>
                                            <th>Remarks</th>
                                            <th>Created</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($rows as $r)
                                            <tr>
                                                <td>{{ $r['date'] ?? '-' }}</td>
                                                <td>{{ ucfirst($r['purpose'] ?? '-') }}</td>
                                                <td>{{ $r['remarks'] ?? '-' }}</td>
                                                <td>{{ $r['created_at'] ?? '-' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                @endif
            </div>
        </section>
    </main>
</div>
</body>
</html>
