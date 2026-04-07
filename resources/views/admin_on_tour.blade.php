@extends('admin_layout')

@section('pageTitle', 'Staff On Tour')

@section('content')
    <div class="container">
        <section class="panel">
            <h2>Staff Currently On Tour</h2>
            <div style="margin:12px 0 18px 0;display:flex;gap:18px;align-items:flex-start;flex-wrap:wrap">
                <div style="min-width:240px">
                    <form method="GET" action="{{ route('admin.on_tour') }}" style="display:flex;gap:8px;align-items:center">
                        <label style="font-weight:700">Department:</label>
                        <select name="department_id" style="padding:8px;border:1px solid #e6eef8;border-radius:6px">
                            <option value="">All</option>
                            @foreach($departments as $d)
                                <option value="{{ $d->department_id }}" @if((string)($dept ?? '') === (string)$d->department_id) selected @endif>{{ $d->department_name }}</option>
                            @endforeach
                        </select>
                        <button class="btn" type="submit">Filter</button>
                        <a href="{{ route('admin.on_tour.export') }}?{{ http_build_query(request()->except('_token')) }}" class="btn" style="background:#10b981;margin-left:6px">Export CSV</a>
                    </form>
                </div>
            </div>

            <div class="leave-history"><div class="table-wrap"><table class="users requests">
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
                    <tr><td colspan="9" class="empty">No staff on tour right now</td></tr>
                @else
                    @foreach($onTourStaff as $tour)
                        <tr>
                            <td>{{ $tour['employee_name'] ?? '-' }}</td>
                            <td>{{ $tour['designation'] ?? '-' }}</td>
                            <td>{{ $tour['department_name'] ?? '-' }}</td>
                            <td>{{ $tour['place'] ?? '-' }}</td>
                            <td>{{ ! empty($tour['start_date']) ? \Illuminate\Support\Carbon::parse($tour['start_date'])->format('d/m/Y') : '-' }}</td>
                            <td>{{ ! empty($tour['end_date']) ? \Illuminate\Support\Carbon::parse($tour['end_date'])->format('d/m/Y') : '-' }}</td>
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
                                <a class="action-link" href="{{ route('admin.on_tour.edit', $tour['record_id'] ?? $tour['employee_id'] ?? '') }}">Edit</a>
                                |
                                <form method="POST" action="{{ route('admin.on_tour.delete', $tour['record_id'] ?? $tour['employee_id'] ?? '') }}" style="display:inline" onsubmit="return confirm('Delete this tour record?');">
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
@endsection
