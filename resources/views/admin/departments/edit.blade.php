@extends('admin_layout')

@section('content')
<div style="padding:18px;max-width:500px;">
    <h2>Edit Department</h2>
    @if ($errors->any())
        <div style="color:#d0342a;margin-bottom:12px">
            {{ $errors->first() }}
        </div>
    @endif
    <form method="POST" action="{{ route('admin.departments.edit.update', $department->department_id) }}">
        @csrf
        @method('POST')
        <label>Department Name:</label><br>
        <input type="text" name="department_name" value="{{ old('department_name', $department->department_name) }}" required style="padding:8px;width:320px"><br><br>
        <label>Status:</label><br>
        <select name="status" style="padding:8px">
            <option value="active" @if($department->status === 'active') selected @endif>Active</option>
            <option value="inactive" @if($department->status === 'inactive') selected @endif>Inactive</option>
        </select>
        <br><br>
        <button type="submit" name="submit" style="padding:8px 14px">Update Department</button>
    </form>
</div>
@endsection
