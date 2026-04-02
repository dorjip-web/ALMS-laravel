@extends('admin_layout')

@section('content')
<div class="roles-grid" style="display:grid;grid-template-columns:1fr 1fr;gap:16px;width:100%">
    <div class="card">
        <h2>Manage Roles</h2>
        <a class="small-link" href="#add-role">+ Add Role</a>
        <div class="leave-history" style="margin-top:10px;">
            <div class="table-wrap">
                <table class="users">
                    <thead>
                        <tr><th>ID</th><th>Role Name</th><th>Status</th><th>Action</th></tr>
                    </thead>
                    <tbody>
                        @forelse ($roles as $r)
                            <tr>
                                <td>{{ $r->role_id }}</td>
                                <td>{{ $r->role_name }}</td>
                                <td>{{ $r->status }}</td>
                                <td>
                                    <a class="action-orange" href="{{ route('admin.roles_permissions', ['edit_role' => $r->role_id]) }}">Edit</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4">No roles found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card" id="add-role">
        <h3>{{ $editRole ? 'Edit Role' : 'Add Role' }}</h3>
        <form method="POST" action="{{ route('admin.roles_permissions.saveRole') }}" class="leave-form">
            @csrf
            <input type="hidden" name="role_id" value="{{ $editRole ? $editRole->role_id : 0 }}">
            <div class="row">
                <label>Role Name</label>
                <input type="text" name="role_name" required value="{{ $editRole ? $editRole->role_name : '' }}">
            </div>
            <div class="row" style="margin-top:8px">
                <label>Status</label>
                <select name="status">
                    <option value="active" @if(($editRole->status ?? '') === 'active') selected @endif>Active</option>
                    <option value="inactive" @if(($editRole->status ?? '') === 'inactive') selected @endif>Inactive</option>
                </select>
            </div>
            <div class="row" style="margin-top:8px">
                <button type="submit" class="btn">{{ $editRole ? 'Update' : 'Save' }}</button>
                <a href="{{ route('admin.roles_permissions') }}" class="btn" style="background:#fff;color:#333;border:1px solid #cfd8db;margin-left:8px">Cancel</a>
            </div>
        </form>
    </div>

    <div class="card">
        <h2>Permissions</h2>
        <a class="small-link" href="#add-perm">+ Add Permission</a>
        <div class="leave-history" style="margin-top:10px;">
            <div class="table-wrap">
                <table class="users">
                    <thead>
                        <tr><th>ID</th><th>Permission Name</th><th>Status</th><th>Action</th></tr>
                    </thead>
                    <tbody>
                        @forelse ($permissions as $p)
                            <tr>
                                <td>{{ $p->permission_id }}</td>
                                <td>{{ $p->permission_name }}</td>
                                <td>{{ $p->status }}</td>
                                <td><a class="action-orange" href="{{ route('admin.roles_permissions', ['edit_perm' => $p->permission_id]) }}">Edit</a></td>
                            </tr>
                        @empty
                            <tr><td colspan="4">No permissions found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card" id="add-perm">
        <h3>{{ $editPermission ? 'Edit Permission' : 'Add Permission' }}</h3>
        <form method="POST" action="{{ route('admin.roles_permissions.savePermission') }}" class="leave-form">
            @csrf
            <input type="hidden" name="perm_id" value="{{ $editPermission ? $editPermission->permission_id : 0 }}">
            <div class="row">
                <label>Permission Name</label>
                <input type="text" name="permission_name" required value="{{ $editPermission ? $editPermission->permission_name : '' }}">
            </div>
            <div class="row" style="margin-top:8px">
                <label>Status</label>
                <select name="pstatus">
                    <option value="active" @if(($editPermission->status ?? '') === 'active') selected @endif>Active</option>
                    <option value="inactive" @if(($editPermission->status ?? '') === 'inactive') selected @endif>Inactive</option>
                </select>
            </div>
            <div class="row" style="margin-top:8px">
                <button type="submit" class="btn">{{ $editPermission ? 'Update' : 'Save' }}</button>
                <a href="{{ route('admin.roles_permissions') }}" class="btn" style="background:#fff;color:#333;border:1px solid #cfd8db;margin-left:8px">Cancel</a>
            </div>
        </form>
    </div>

    <div class="card">
        <h2>Assign Permissions</h2>
        <form method="POST" action="{{ route('admin.roles_permissions.saveAssign') }}" class="leave-form">
            @csrf
            <div class="row">
                <label>Select Role</label>
                <select name="assign_role_id" id="assign_role_id" onchange="this.form.submit()">
                    <option value="">-- Select Role --</option>
                    @foreach ($roles as $r)
                        <option value="{{ $r->role_id }}" @if(request('assign_role_id') == $r->role_id) selected @endif>{{ $r->role_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="row" style="margin-top:8px">
                <label>Permissions</label>
            </div>
            <div class="leave-history" style="margin-top:8px">
                <div class="table-wrap">
                    <table class="users">
                        <thead>
                            <tr><th></th><th>Permission</th><th>Status</th></tr>
                        </thead>
                        <tbody>
                            @forelse ($permissions as $p)
                                <tr>
                                    <td style="width:40px">
                                        <input type="checkbox" name="assign_permissions[]" value="{{ $p->permission_id }}" @if(in_array($p->permission_id, $assignedPermissions)) checked @endif>
                                    </td>
                                    <td>{{ $p->permission_name }}</td>
                                    <td>{{ $p->status }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3">No permissions available.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="row" style="margin-top:8px">
                <button type="submit" name="save_assign" class="btn">Save Assignments</button>
            </div>
        </form>
    </div>
</div>
@if ($message)
    <div style="color:green;margin-bottom:8px">{{ $message }}</div>
@endif
@endsection
