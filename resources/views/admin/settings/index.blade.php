@extends('admin_layout')

@section('pageTitle','Admin Settings')

@section('content')
    <div class="container">
        <h1>Admin Accounts</h1>
        <p style="margin-bottom:12px"><a class="btn" href="{{ route('admin.settings.add_admin') }}">+ Add Admin</a></p>

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
                            <tr><td colspan="4" style="text-align:left;padding:14px 10px;color:#444;">No admin accounts found.</td></tr>
                        @else
                            @foreach($admins as $a)
                                <tr>
                                    <td>{{ $a['name'] ?? ($a['admin_name'] ?? ($a['username'] ?? '-')) }}</td>
                                    <td>{{ $a['username'] ?? $a['email'] ?? '-' }}</td>
                                    <td>{{ (!empty($a['active']) || !empty($a['is_active']) || ($a['is_admin'] ?? null) === 1) ? 'Active' : 'Inactive' }}</td>
                                    <td style="white-space:nowrap">
                                        <a class="action-link" href="{{ route('admin.settings.edit_admin') }}">Edit</a> |
                                        <a class="action-link" href="{{ route('admin.settings.change_password') }}">Change Password</a> |
                                        <a class="action-link" href="{{ route('admin.settings.toggle') }}">Toggle</a>
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
