@extends('admin_layout')

@section('content')
<div class="container">
    <h2>Leave Records</h2>

    <div class="filter-box" style="background:#1e293b;padding:15px;border-radius:10px;margin-bottom:20px;">
        <form method="GET" action="{{ route('admin.leave_records.index') }}" class="leave-form row-inline" style="margin-bottom:0;gap:12px;align-items:center;">
            <select name="status" style="width:160px;">
                <option value="all" {{ $status === 'all' ? 'selected' : '' }}>All</option>
                <option value="approved" {{ $status === 'approved' ? 'selected' : '' }}>Approved</option>
                <option value="rejected" {{ $status === 'rejected' ? 'selected' : '' }}>Rejected</option>
                <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pending</option>
            </select>

            <select name="department_id" style="width:220px;">
                <option value="">All Departments</option>
                @foreach($departments as $d)
                    <option value="{{ $d->department_id }}" {{ $dept == $d->department_id ? 'selected' : '' }}>{{ $d->department_name }}</option>
                @endforeach
            </select>

            <input type="date" name="from_date" value="{{ request('from_date', '') }}" style="width:160px;">
            <input type="date" name="to_date" value="{{ request('to_date', '') }}" style="width:160px;">

            <button type="submit" class="btn" style="padding:6px 10px;">Apply</button>
            <a class="btn" href="{{ route('admin.leave_records.export', request()->query()) }}" style="padding:6px 10px;">Export CSV</a>
        </form>
    </div>

    <div class="leave-history">
        <div class="table-wrap">
        <table class="users">
            <thead>
                <tr>
                    <th>Employee</th>
                    <th>Type</th>
                    <th>S.date</th>
                    <th>E.date</th>
                    <th>Days</th>
                    <th>Reason</th>
                    <th>HoD</th>
                    <th>MS</th>
                    <th>Department</th>
                    <th>Applied At</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($records as $r)
                    <tr>
                        <td>{{ $r->employee_name }}</td>
                        <td>{{ $r->type }}</td>
                        <td>{{ !empty($r->from_date) ? \Illuminate\Support\Carbon::parse($r->from_date)->format('d/m/Y') : '-' }}</td>
                        <td>{{ !empty($r->to_date) ? \Illuminate\Support\Carbon::parse($r->to_date)->format('d/m/Y') : '-' }}</td>
                        <td>{{ $r->days }}</td>
                        <td>{{ $r->reason }}</td>
                        <td>{{ $r->HoD_status }}</td>
                        <td>{{ $r->medical_superintendent_status }}</td>
                        <td>{{ $r->department_name }}</td>
                        <td>{{ !empty($r->applied_at) ? \Illuminate\Support\Carbon::parse($r->applied_at)->format('d/m/Y') : '-' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="10">No leave records</td></tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>

    <div style="margin-top:12px">{{ $records->links() }}</div>
</div>
@endsection
