@extends('admin_layout')

@section('content')
<div style="padding:18px">
    <h2>Edit User</h2>
    <form method="post" action="{{ route('admin.users.edit.update', $user->employee_id, false) }}">
        @csrf
        @method('POST')
        <input type="hidden" name="employee_id" value="{{ $user->employee_id }}">
        <div class="leave-form" style="padding:0">
            <div class="row-grid-3">
                <div class="col">
                    <label>EID</label>
                    <input type="text" name="eid" value="{{ old('eid', $user->eid) }}" required class="form-control">
                </div>
                <div class="col">
                    <label>Employee Name</label>
                    <input type="text" name="employee_name" value="{{ old('employee_name', $user->employee_name) }}" required class="form-control">
                </div>
                <div class="col">
                    <label>Designation</label>
                    <input type="text" name="designation" value="{{ old('designation', $user->designation) }}" required class="form-control">
                </div>
                <div class="col">
                    <label>Username</label>
                    <input type="text" name="username" value="{{ old('username', $user->username) }}" class="form-control">
                </div>
                <div class="col">
                    <label>Password (leave blank to keep current)</label>
                    <input type="password" name="password" class="form-control">
                </div>
                <div class="col">
                    <label>Department</label>
                    <select name="department_id" class="form-control">
                        @foreach(($departments ?? []) as $d)
                            <option value="{{ $d->department_id }}" @if($user->department_id == $d->department_id) selected @endif>{{ $d->department_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col">
                    <label>Role</label>
                    <select name="role_id" class="form-control">
                        <option value="1" @if($user->role_id == 1) selected @endif>Medical Superintendent</option>
                        <option value="2" @if($user->role_id == 2) selected @endif>HoD</option>
                        <option value="3" @if($user->role_id == 3) selected @endif>Employee</option>
                    </select>
                </div>
                <div class="col">
                    <label>Status</label>
                    <select name="status" class="form-control">
                        <option value="Active" @if($user->status === 'Active') selected @endif>Active</option>
                        <option value="Inactive" @if($user->status === 'Inactive') selected @endif>Inactive</option>
                    </select>
                </div>
            </div>
            <div style="margin-top:12px">
                <button type="submit" name="update" class="btn">Update User</button>
            </div>
        </div>
    </form>
</div>
@endsection
