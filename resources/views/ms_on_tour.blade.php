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
            <section class="panel">
                <h2>Staff Currently On Tour</h2>
                <div style="margin:12px 0 18px 0;display:flex;gap:18px;align-items:flex-start;flex-wrap:wrap">
                    <div style="flex:1;min-width:320px">
                        <form method="POST" action="{{ route('ms.on_tour.store') }}" style="display:block;padding:12px;border:1px solid #e6eef8;border-radius:8px;background:#fff">
                            @csrf
                            <div style="margin-bottom:8px;font-weight:700">Add Tour Record</div>
                            <div style="margin-bottom:8px">
                                <label style="display:block;margin-bottom:6px">Employee</label>
                                <select name="employee_id" required style="padding:8px;width:100%;border:1px solid #e6eef8;border-radius:6px">
                                    <option value="">-- Select Employee --</option>
                                    @foreach($employees as $emp)
                                        <option value="{{ $emp->employee_id }}">{{ $emp->employee_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div style="display:flex;gap:8px;margin-bottom:8px">
                                <div style="flex:1">
                                    <label style="display:block;margin-bottom:6px">Start Date</label>
                                    <input type="date" name="start_date" class="form-input" style="padding:8px;width:100%;border:1px solid #e6eef8;border-radius:6px">
                                </div>
                                <div style="flex:1">
                                    <label style="display:block;margin-bottom:6px">End Date</label>
                                    <input type="date" name="end_date" class="form-input" style="padding:8px;width:100%;border:1px solid #e6eef8;border-radius:6px">
                                </div>
                            </div>
                            <div style="margin-bottom:8px">
                                <label style="display:block;margin-bottom:6px">Place</label>
                                <input type="text" name="place" style="padding:8px;width:100%;border:1px solid #e6eef8;border-radius:6px">
                            </div>
                            <div style="margin-bottom:8px">
                                <label style="display:block;margin-bottom:6px">Purpose</label>
                                <textarea name="purpose" rows="2" style="padding:8px;width:100%;border:1px solid #e6eef8;border-radius:6px"></textarea>
                            </div>
                            <div style="margin-bottom:8px">
                                <label style="display:block;margin-bottom:6px">Department</label>
                                <select name="department" style="padding:8px;width:100%;border:1px solid #e6eef8;border-radius:6px">
                                    <option value="">-- All Departments --</option>
                                    @foreach($departments as $d)
                                        <option value="{{ $d->department_id }}" @if((string)($dept ?? '') === (string)$d->department_id) selected @endif>{{ $d->department_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button class="btn" type="submit">Filter</button>
                        </form>
                    </div>
                </div>
                <div class="leave-history"><div class="table-wrap"><table class="users requests">
                    <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Department</th>
                        <th>Department</th>
                        <th>Place</th>
                        <th>From</th>
                        <th>To</th>
                        <th>Total Days</th>
                        <th>Office Order</th>
                        <th>Actions</th>
                        <th>Office Order</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if(empty($onTourStaff) || count($onTourStaff) === 0)
                        <tr><td colspan="9" class="empty">No staff on tour right now</td></tr>
                    @else
                        @foreach($onTourStaff as $tour)
                            <tr>
                                <td>{{ $tour['department_name'] ?? '-' }}</td>
                                <td>{{ $tour['designation'] ?? '-' }}</td>
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
                                <td style="white-space:nowrap">
                                    <a class="action-link" href="{{ route('ms.on_tour.edit', $tour['employee_id'] ?? '') }}">Edit</a>
                                    |
                                    <form method="POST" action="{{ route('ms.on_tour.delete', $tour['employee_id'] ?? '') }}" style="display:inline" onsubmit="return confirm('Delete this tour record?');">
                                        @csrf
                                        <button type="submit" style="background:none;border:none;color:var(--accent);font-weight:700;cursor:pointer;padding:0;margin-left:6px">Delete</button>
                                    </form>
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
