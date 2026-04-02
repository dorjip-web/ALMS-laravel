@extends('admin_layout')

@section('content')
<div style="padding:18px;max-width:500px;">
    <h2>Assign HoD to {{ $department->department_name }}</h2>
    @if ($errors->any())
        <div style="color:#d0342a;margin-bottom:12px">
            {{ $errors->first() }}
        </div>
    @endif
    <form method="POST" action="{{ route('admin.departments.assign_hod.store', $department->department_id) }}">
        @csrf
        <label>Select HoD:</label><br>
        <select name="employee_id" required style="padding:8px;width:320px">
            <option value="">-- Select HoD --</option>
            @foreach ($employees as $row)
                <option value="{{ $row->employee_id }}" @if($row->employee_id == $current_hod_id) selected @endif>{{ $row->employee_name }}</option>
            @endforeach
        </select>
        <br><br>
        <button type="submit" name="submit" style="padding:8px 14px">Assign HoD</button>
    </form>
</div>
@endsection
