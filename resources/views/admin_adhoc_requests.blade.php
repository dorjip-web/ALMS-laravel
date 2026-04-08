@extends('admin_layout')

@section('pageTitle', 'Adhoc Requests')

@section('content')
    <div class="container">
        <section class="panel">
            <h2>Adhoc Requests</h2>

            {{-- debug info removed --}}
            <div style="display:flex;gap:18px;align-items:center;margin-bottom:12px">
                <form method="GET" action="{{ route('admin.adhoc') }}" style="display:flex;gap:8px;align-items:center">
                    <label style="font-weight:700">Department:</label>
                    <select name="department_id" style="padding:8px;border:1px solid #e6eef8;border-radius:6px">
                        <option value="">All</option>
                        @foreach($departments as $d)
                            <option value="{{ $d->department_id }}" @if((string)($dept ?? '') === (string)$d->department_id) selected @endif>{{ $d->department_name }}</option>
                        @endforeach
                    </select>
                    <button class="btn" type="submit">Filter</button>
                    <a href="{{ route('admin.adhoc.export') }}?{{ http_build_query(request()->except('_token')) }}" class="btn" style="background:#10b981;margin-left:6px">Export CSV</a>
                </form>
            </div>

            @if (! $tableExists)
                <div class="summary-empty">Adhoc requests table not found.</div>
            @else
                <div class="leave-history" style="margin-top:8px;">
                    <div class="table-wrap">
                        <table class="users">
                            <thead>
                                <tr>
                                    <th>Employee</th>
                                    <th>Designation</th>
                                    <th>Department</th>
                                    <th>Purpose</th>
                                    <th>Remarks</th>
                                    <th>Date</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (empty($rows))
                                    <tr>
                                        <td colspan="8" style="text-align:left;padding:14px 10px;color:#444;">No adhoc requests found.</td>
                                    </tr>
                                @else
                                    @foreach($rows as $r)
                                        <tr>
                                            <td>{{ $r['employee_name'] ?? $r['eid'] ?? ($r['employee_id'] ?? '-') }}</td>
                                            <td>{{ $r['designation'] ?? '-' }}</td>
                                            <td>{{ $r['department_name'] ?? '-' }}</td>
                                            <td>{{ ucfirst($r['purpose'] ?? '-') }}</td>
                                            <td>{{ $r['remarks'] ?? '-' }}</td>
                                            <td>{{ $r['date'] ?? '-' }}</td>
                                            <td>{{ $r['created_at'] ?? '-' }}</td>
                                            <td style="white-space:nowrap">
                                                <a class="action-link" href="{{ route('admin.adhoc.edit', $r['id'] ?? $r['adhoc_request_id'] ?? $r['adhoc_id'] ?? $r['application_id'] ?? $r['employee_id'] ?? '') }}">Edit</a>
                                                |
                                                <form method="POST" action="{{ route('admin.adhoc.delete', $r['id'] ?? $r['adhoc_request_id'] ?? $r['adhoc_id'] ?? $r['application_id'] ?? $r['employee_id'] ?? '') }}" style="display:inline" onsubmit="return confirm('Delete this adhoc request?');">
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
            @endif

        </section>
    </div>
@endsection
