@extends('admin_layout')

@section('pageTitle', 'Admin Settings')

@section('content')
<div style="padding:18px">
    <h1>Admin Accounts</h1>
    <div style="margin-bottom:18px;">
        <a href="{{ route('admin.settings.add_admin') }}" class="btn" style="font-size:15px;padding:7px 16px;text-decoration:none;">+ Add Admin</a>
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
                    @if (empty($admins))
                        <tr>
                            <td colspan="4">No admin accounts found.</td>
                        </tr>
                    @else
                        @foreach($admins as $a)
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
                                    <a class="action-orange" href="{{ route('admin.settings.edit_admin') }}">Edit</a> |
                                    <a class="action-orange" href="{{ route('admin.settings.change_password') }}">Change Password</a> |
                                    <a class="action-orange" href="{{ route('admin.settings.toggle') }}">Toggle Status</a>
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
