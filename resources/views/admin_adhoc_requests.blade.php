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
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Purpose</th>
                                        <th>Remarks</th>
                                        <th>Employee</th>
                                        <th>Created</th>
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
