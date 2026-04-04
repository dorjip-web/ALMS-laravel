@extends('admin_layout')

@section('content')
<div class="container">
    <h2>Leave Records</h2>

    <div class="filter-box" style="background:#1e293b;padding:15px;border-radius:10px;margin-bottom:20px;">
        <form method="GET" action="{{ route('admin.leave_records.index') }}" class="leave-form row-inline" style="margin-bottom:0;gap:12px;align-items:center;">
            <select name="status" style="max-width:220px">
                <option value="all" {{ $status === 'all' ? 'selected' : '' }}>All</option>
                <option value="approved" {{ $status === 'approved' ? 'selected' : '' }}>Approved</option>
                <option value="rejected" {{ $status === 'rejected' ? 'selected' : '' }}>Rejected</option>
                <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pending</option>
            </select>

            <select name="department_id" style="max-width:260px">
                <option value="">All Departments</option>
                @foreach($departments as $d)
                    <option value="{{ $d->department_id }}" {{ $dept == $d->department_id ? 'selected' : '' }}>{{ $d->department_name }}</option>
                @endforeach
            </select>

            <input type="date" name="from_date" value="{{ request('from_date', '') }}" style="max-width:180px">
            <input type="date" name="to_date" value="{{ request('to_date', '') }}" style="max-width:180px">

            <button type="submit" class="btn">Apply</button>
            <a class="btn" href="{{ route('admin.leave_records.export', request()->query()) }}">Export CSV</a>
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
                        <td>{{ $r->from_date }}</td>
                        <td>{{ $r->to_date }}</td>
                        <td>{{ $r->days }}</td>
                        <td>{{ $r->reason }}</td>
                        <td>{{ $r->HoD_status }}</td>
                        <td>{{ $r->medical_superintendent_status }}</td>
                        <td>{{ $r->department_name }}</td>
                        <td>{{ $r->applied_at }}</td>
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
