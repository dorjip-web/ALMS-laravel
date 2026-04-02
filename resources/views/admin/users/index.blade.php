@extends('admin_layout')

@section('content')
<div style="padding:18px">
    <h1>User Management</h1>
    <div style="margin-bottom:18px;">
        <a href="{{ route('admin.users.create') }}" class="btn" style="font-size:15px;padding:7px 16px;text-decoration:none;">➕ Add New User</a>
    </div>
    @if ($users->isEmpty())
        <p>No users found.</p>
    @else
        <div class="leave-history">
            <div class="table-wrap">
                <table class="users">
                    <thead>
                        <tr>
                            <th>Employee ID</th>
                            <th>EID</th>
                            <th>Employee Name</th>
                            <th>Designation</th>
                            <th>Department</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $row)
                        <tr>
                            <td>{{ $row->employee_id }}</td>
                            <td>{{ $row->eid }}</td>
                            <td>{{ $row->employee_name }}</td>
                            <td>{{ $row->designation ?? '' }}</td>
                            <td>{{ $row->department_name ?? '' }}</td>
                            <td>{{ $row->role_name ?? '' }}</td>
                            <td>
                                @php $status = trim($row->status ?? ''); $lower = strtolower($status); @endphp
                                @if ($lower === 'active')
                                    <span class="status-active">{{ $status }}</span>
                                @elseif ($lower === 'inactive')
                                    <span class="status-inactive">{{ $status }}</span>
                                @else
                                    {{ $status }}
                                @endif
                            </td>
                            <td>
                                <a class="action-orange" href="{{ url('admin/users/edit/' . $row->employee_id) }}">Edit</a> |
                                <a class="action-orange" href="{{ url('admin/users/toggle-status/' . $row->employee_id) }}">Toggle Status</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
@endsection
