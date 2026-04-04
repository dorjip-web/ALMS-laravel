@extends('admin_layout')

@section('content')
<div style="padding:24px">
    <h2>Leave Records</h2>

    <form method="GET" action="{{ route('admin.leave_records.index') }}" style="display:flex;gap:12px;align-items:flex-end;margin-bottom:12px;flex-wrap:wrap;">
        <div>
            <label>Status</label>
            <select name="status" class="form-control">
                <option value="all" {{ $status === 'all' ? 'selected' : '' }}>All</option>
                <option value="approved" {{ $status === 'approved' ? 'selected' : '' }}>Approved</option>
                <option value="rejected" {{ $status === 'rejected' ? 'selected' : '' }}>Rejected</option>
                <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pending</option>
            </select>
        </div>

        <div>
            <label>Department</label>
            <select name="department_id" class="form-control">
                <option value="">All Departments</option>
                @foreach($departments as $d)
                    <option value="{{ $d->department_id }}" {{ $dept == $d->department_id ? 'selected' : '' }}>{{ $d->department_name }}</option>
                @endforeach
            </select>
        </div>

        <div style="display:flex;gap:8px">
            <button class="btn" type="submit">Filter</button>
            <a class="btn" href="{{ route('admin.leave_records.export', array_merge(request()->query(), ['export' => 'csv'])) }}">Export CSV</a>
        </div>
    </form>

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

    <div style="margin-top:12px">{{ $records->links() }}</div>
</div>
@endsection
