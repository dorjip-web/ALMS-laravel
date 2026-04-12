@extends('admin_layout')

@section('content')
<div class="leave-form" style="padding:18px">
    <h2>Add New User</h2>
    <form method="POST" action="{{ route('admin.users.create.store', [], false) }}">
        @csrf
        <div class="row-grid-3">
            <div class="col">
                <label>EID</label>
                <input type="text" name="eid" required class="form-control" value="{{ old('eid') }}">
            </div>
            <div class="col">
                <label>Employee Name</label>
                <input type="text" name="employee_name" required class="form-control" value="{{ old('employee_name') }}">
            </div>
            <div class="col">
                <label>Designation</label>
                <input type="text" name="designation" required class="form-control" value="{{ old('designation') }}">
            </div>
            <div class="col">
                <label>Username</label>
                <input type="text" name="username" required class="form-control" value="{{ old('username') }}">
            </div>
            <div class="col">
                <label>Password</label>
                <input type="password" name="password" required class="form-control">
            </div>
            <div class="col">
                <label>Department</label>
                <select name="department_id" class="form-control" required>
                    @foreach(($departments ?? []) as $d)
                        <option value="{{ $d->department_id }}" {{ old('department_id') == $d->department_id ? 'selected' : '' }}>{{ $d->department_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col">
                <label>Role</label>
                <select name="role_id" class="form-control" required>
                    <option value="1">Medical_Superintendent</option>
                    <option value="2">HoD</option>
                    <option value="3">Employee</option>
                </select>
            </div>
            <div class="col">
                <label>Status</label>
                <select name="status" class="form-control" required>
                    <option value="Active">Active</option>
                    <option value="Inactive">Inactive</option>
                </select>
            </div>
        </div>
        <button type="submit" name="create" class="btn">Create User</button>
    </form>
</div>
@endsection
