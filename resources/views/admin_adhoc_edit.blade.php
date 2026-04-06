@extends('admin_layout')

@section('pageTitle', 'Edit Adhoc Request')

@section('content')
    <div class="container">
        <section class="panel">
            <h2>Edit Adhoc Request</h2>

            <form method="POST" action="{{ route('admin.adhoc.update', $record['id'] ?? $record['adhoc_request_id'] ?? $record['adhoc_id'] ?? $record['application_id'] ?? $record['employee_id'] ?? 0) }}">
                @csrf
                <div style="margin-bottom:8px">
                    <label style="display:block;margin-bottom:6px">Employee</label>
                    <select name="employee_id" required style="padding:8px;width:100%;border:1px solid #e6eef8;border-radius:6px">
                        <option value="">-- Select Employee --</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->employee_id }}" @if((string)($record['employee_id'] ?? '') === (string)$emp->employee_id) selected @endif>{{ $emp->employee_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div style="display:flex;gap:8px;margin-bottom:8px">
                    <div style="flex:1">
                        <label style="display:block;margin-bottom:6px">Date</label>
                        <input type="date" name="date" value="{{ $record['date'] ?? '' }}" style="padding:8px;width:100%;border:1px solid #e6eef8;border-radius:6px">
                    </div>
                    <div style="flex:1">
                        <label style="display:block;margin-bottom:6px">Purpose</label>
                        <select name="purpose" style="padding:8px;width:100%;border:1px solid #e6eef8;border-radius:6px">
                            <option value="meeting" @if(($record['purpose'] ?? '') === 'meeting') selected @endif>Meeting</option>
                            <option value="emergency" @if(($record['purpose'] ?? '') === 'emergency') selected @endif>Emergency</option>
                        </select>
                    </div>
                </div>
                <div style="margin-bottom:8px">
                    <label style="display:block;margin-bottom:6px">Remarks</label>
                    <input type="text" name="remarks" value="{{ $record['remarks'] ?? '' }}" style="padding:8px;width:100%;border:1px solid #e6eef8;border-radius:6px">
                </div>
                <div>
                    <button class="btn" type="submit">Save Changes</button>
                    <a href="{{ route('admin.adhoc') }}" class="btn btn-secondary" style="margin-left:8px">Cancel</a>
                </div>
            </form>

        </section>
    </div>
@endsection
