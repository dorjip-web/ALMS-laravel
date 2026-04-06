@extends('admin_layout')

@section('pageTitle', 'Edit Tour Record')

@section('content')
    <div class="container">
        <header><h1>Edit Tour Record</h1></header>

        <section class="panel">
            <form method="POST" action="{{ route('admin.on_tour.update', $record['id'] ?? 0) }}">
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
                        <label style="display:block;margin-bottom:6px">Start Date</label>
                        <input type="date" name="start_date" value="{{ $record['start_date'] ?? '' }}" style="padding:8px;width:100%;border:1px solid #e6eef8;border-radius:6px">
                    </div>
                    <div style="flex:1">
                        <label style="display:block;margin-bottom:6px">End Date</label>
                        <input type="date" name="end_date" value="{{ $record['end_date'] ?? '' }}" style="padding:8px;width:100%;border:1px solid #e6eef8;border-radius:6px">
                    </div>
                </div>
                <div style="margin-bottom:8px">
                    <label style="display:block;margin-bottom:6px">Place</label>
                    <input type="text" name="place" value="{{ $record['place'] ?? '' }}" style="padding:8px;width:100%;border:1px solid #e6eef8;border-radius:6px">
                </div>
                <div style="margin-bottom:8px">
                    <label style="display:block;margin-bottom:6px">Purpose</label>
                    <textarea name="purpose" rows="2" style="padding:8px;width:100%;border:1px solid #e6eef8;border-radius:6px">{{ $record['purpose'] ?? '' }}</textarea>
                </div>
                <div>
                    <button class="btn" type="submit">Save Changes</button>
                    <a href="{{ route('admin.on_tour') }}" class="btn btn-secondary" style="margin-left:8px">Cancel</a>
                </div>
            </form>
        </section>
    </div>
@endsection
