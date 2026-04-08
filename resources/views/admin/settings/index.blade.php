@extends('admin_layout')

@section('pageTitle', 'Admin Settings')

@section('content')
@extends('admin_layout')

@section('content')
<div style="padding:18px">
    <h1>Admin Management</h1>
    <div style="margin-bottom:18px;">
        <a href="{{ route('admin.settings.manage') }}" class="btn" style="font-size:15px;padding:7px 16px;text-decoration:none;">+ Add Admin</a>
    </div>

    <div class="leave-history">
        <div class="table-wrap">
            <table class="users">
                <thead>
                    <tr>
                        <th>Admin Name</th>
                        <th>Username / Email</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @if (empty($admins) || count($admins) === 0)
                        <tr><td colspan="4">No admin accounts found.</td></tr>
                    @else
                        @foreach($admins as $a)
                            @php
                                $id = $a['id'] ?? $a['admin_id'] ?? $a['employee_id'] ?? null;
                            @endphp
                            <tr>
                                <td>{{ $a['name'] ?? ($a['admin_name'] ?? ($a['username'] ?? '-')) }}</td>
                                <td>{{ $a['username'] ?? $a['email'] ?? '-' }}</td>
                                <td>
                                    @if (!empty($a['active']) || !empty($a['is_active']) || (!empty($a['is_admin']) && $a['is_admin'] == 1))
                                        <span class="status-active">Active</span>
                                    @else
                                        <span class="status-inactive">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <a class="action-orange" href="{{ route('admin.settings.manage', $id) }}">Edit</a> |
                                    <a class="action-orange" href="{{ route('admin.settings.manage', $id) }}#password">Change Password</a> |
                                    <form method="POST" action="{{ route('admin.settings.toggle', $id) }}" style="display:inline;margin:0;padding:0">
                                        @csrf
                                        <button type="submit" class="action-orange" style="background:none;border:none;padding:0;margin:0;cursor:pointer">Toggle Status</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
@endsection
