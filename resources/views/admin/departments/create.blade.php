@extends('admin_layout')

@section('content')
<div style="padding:18px;max-width:500px;">
    <h2>Add Department</h2>
    @if ($errors->any())
        <div style="color:#d0342a;margin-bottom:12px">
            {{ $errors->first() }}
        </div>
    @endif
    <form method="POST" action="{{ route('admin.departments.create.store') }}">
        @csrf
        <label>Department Name:</label><br>
        <input type="text" name="department_name" required style="padding:8px;width:320px" value="{{ old('department_name') }}"><br><br>
        <button type="submit" name="submit" style="padding:8px 14px">Add Department</button>
    </form>
</div>
@endsection
