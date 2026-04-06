@extends('admin_layout')

@section('pageTitle', 'Adhoc Requests')

@section('content')
    <div class="container">
        <section class="panel">
            <h2>Adhoc Requests</h2>

            @if (! $tableExists)
                <div class="summary-empty">Adhoc requests table not found.</div>
            @else
                @if (empty($rows))
                    <div class="summary-empty">No adhoc requests found.</div>
                @else
                    <div class="leave-history" style="margin-top:8px;">
                        <div class="table-wrap">
                            <table class="users">
                                    <section class="panel">
                                        <h2>Adhoc Requests</h2>

                                        <div style="margin:12px 0 18px 0;display:flex;gap:18px;align-items:flex-start;flex-wrap:wrap">
                                            <div style="flex:1;min-width:320px">
                                                @if ($tableExists)
                                                    <form method="POST" action="{{ route('admin.adhoc.store') }}" style="display:block;padding:12px;border:1px solid #e6eef8;border-radius:8px;background:#fff">
                                                        @csrf
                                                        <div style="margin-bottom:8px;font-weight:700">Add Adhoc Request</div>
                                                        <div style="margin-bottom:8px">
                                                            <label style="display:block;margin-bottom:6px">Employee</label>
                                                            <select name="employee_id" required style="padding:8px;width:100%;border:1px solid #e6eef8;border-radius:6px">
                                                                <option value="">-- Select Employee --</option>
                                                                @foreach($employees as $emp)
                                                                    <option value="{{ $emp->employee_id }}">{{ $emp->employee_name }} @if(!empty($emp->department_id)) ({{ $emp->department_id }}) @endif</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div style="display:flex;gap:8px;margin-bottom:8px">
                                                            <div style="flex:1">
                                                                <label style="display:block;margin-bottom:6px">Date</label>
                                                                <input type="date" name="date" class="form-input" value="{{ now()->toDateString() }}" required style="padding:8px;width:100%;border:1px solid #e6eef8;border-radius:6px">
                                                            </div>
                                                            <div style="flex:1">
                                                                <label style="display:block;margin-bottom:6px">Purpose</label>
                                                                <select name="purpose" required style="padding:8px;width:100%;border:1px solid #e6eef8;border-radius:6px">
                                                                    <option value="meeting">Meeting</option>
                                                                    @extends('admin_layout')

                                                                    @section('pageTitle', 'Adhoc Requests')

                                                                    @section('content')
                                                                        <div class="container">
                                                                            <section class="panel">
                                                                                <h2>Adhoc Requests</h2>

                                                                                <div style="margin:12px 0 18px 0;display:flex;gap:18px;align-items:flex-start;flex-wrap:wrap">
                                                                                    <div style="flex:1;min-width:320px">
                                                                                        @if ($tableExists)
                                                                                            <form method="POST" action="{{ route('admin.adhoc.store') }}" style="display:block;padding:12px;border:1px solid #e6eef8;border-radius:8px;background:#fff">
                                                                                                @csrf
                                                                                                <div style="margin-bottom:8px;font-weight:700">Add Adhoc Request</div>
                                                                                                <div style="margin-bottom:8px">
                                                                                                    <label style="display:block;margin-bottom:6px">Employee</label>
                                                                                                    <select name="employee_id" required style="padding:8px;width:100%;border:1px solid #e6eef8;border-radius:6px">
                                                                                                        <option value="">-- Select Employee --</option>
                                                                                                        @foreach($employees as $emp)
                                                                                                            <option value="{{ $emp->employee_id }}">{{ $emp->employee_name }} @if(!empty($emp->department_id)) ({{ $emp->department_id }}) @endif</option>
                                                                                                        @endforeach
                                                                                                    </select>
                                                                                                </div>
                                                                                                <div style="display:flex;gap:8px;margin-bottom:8px">
                                                                                                    <div style="flex:1">
                                                                                                        <label style="display:block;margin-bottom:6px">Date</label>
                                                                                                        <input type="date" name="date" class="form-input" value="{{ now()->toDateString() }}" required style="padding:8px;width:100%;border:1px solid #e6eef8;border-radius:6px">
                                                                                                    </div>
                                                                                                    <div style="flex:1">
                                                                                                        <label style="display:block;margin-bottom:6px">Purpose</label>
                                                                                                        <select name="purpose" required style="padding:8px;width:100%;border:1px solid #e6eef8;border-radius:6px">
                                                                                                            <option value="meeting">Meeting</option>
                                                                                                            <option value="emergency">Emergency</option>
                                                                                                        </select>
                                                                                                    </div>
                                                                                                </div>
                                                                                                <div style="margin-bottom:8px">
                                                                                                    <label style="display:block;margin-bottom:6px">Remarks</label>
                                                                                                    <input type="text" name="remarks" style="padding:8px;width:100%;border:1px solid #e6eef8;border-radius:6px">
                                                                                                </div>
                                                                                                <div>
                                                                                                    <button class="btn" type="submit">Add Request</button>
                                                                                                </div>
                                                                                            </form>
                                                                                        @else
                                                                                            <div class="summary-empty">Adhoc requests table not found.</div>
                                                                                        @endif
                                                                                    </div>

                                                                                    <div style="min-width:240px">
                                                                                        <form method="GET" action="{{ route('admin.adhoc') }}" style="display:flex;gap:8px;align-items:center">
                                                                                            <label style="font-weight:700">Department:</label>
                                                                                            <select name="department_id" style="padding:8px;border:1px solid #e6eef8;border-radius:6px">
                                                                                                <option value="">All</option>
                                                                                                @foreach($departments as $d)
                                                                                                    <option value="{{ $d->department_id }}" @if((string)($dept ?? '') === (string)$d->department_id) selected @endif>{{ $d->department_name }}</option>
                                                                                                @endforeach
                                                                                            </select>
                                                                                            <button class="btn" type="submit">Filter</button>
                                                                                        </form>
                                                                                    </div>
                                                                                </div>

                                                                                @if (! $tableExists)
                                                                                    <div class="summary-empty">Adhoc requests table not found.</div>
                                                                                @else
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
                                                                                                            <th>Employee</th>
                                                                                                            <th>Created</th>
                                                                                                            <th>Actions</th>
                                                                                                        </tr>
                                                                                                    </thead>
                                                                                                    <tbody>
                                                                                                        @foreach($rows as $r)
                                                                                                            <tr>
                                                                                                                <td>{{ $r['date'] ?? '-' }}</td>
                                                                                                                <td>{{ ucfirst($r['purpose'] ?? '-') }}</td>
                                                                                                                <td>{{ $r['remarks'] ?? '-' }}</td>
                                                                                                                <td>{{ $r['employee_name'] ?? $r['eid'] ?? ($r['employee_id'] ?? '-') }}</td>
                                                                                                                <td>{{ $r['created_at'] ?? '-' }}</td>
                                                                                                                <td style="white-space:nowrap">
                                                                                                                    <a class="action-link" href="{{ route('admin.adhoc.edit', $r['id'] ?? $r['adhoc_id'] ?? $r['application_id'] ?? $r['employee_id'] ?? '') }}">Edit</a>
                                                                                                                    |
                                                                                                                    <form method="POST" action="{{ route('admin.adhoc.delete', $r['id'] ?? $r['adhoc_id'] ?? $r['application_id'] ?? $r['employee_id'] ?? '') }}" style="display:inline" onsubmit="return confirm('Delete this adhoc request?');">
                                                                                                                        @csrf
                                                                                                                        <button type="submit" style="background:none;border:none;color:var(--accent);font-weight:700;cursor:pointer;padding:0;margin-left:6px">Delete</button>
                                                                                                                    </form>
                                                                                                                </td>
                                                                                                            </tr>
                                                                                                        @endforeach
                                                                                                    </tbody>
                                                                                                </table>
                                                                                            </div>
                                                                                        </div>
                                                                                    @endif
                                                                                @endif

                                                                            </section>
                                                                        </div>
                                                                    @endsection
