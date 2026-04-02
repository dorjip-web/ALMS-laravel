@extends('admin_layout')

@section('content')
<div style="padding:18px">
    <h1>Department &amp; HoD Management</h1>
    <div style="margin-bottom:18px;">
        <a href="{{ route('admin.departments.create') }}" class="btn" style="font-size:15px;padding:7px 16px;text-decoration:none;">+ Add Department</a>
    </div>
    <div class="leave-history">
        <div class="table-wrap">
            <table class="users">
                <thead>
                    <tr>
                        <th>Department</th>
                        <th>HOD</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @if ($rows->isEmpty())
                        <tr>
                            <td colspan="4">No departments found.</td>
                        </tr>
                    @else
                        @foreach ($rows as $row)
                            <tr>
                                <td class="dept-name">{{ $row->department_name }}</td>
                                <td class="dept-hod">{!! $row->hod_name ? e($row->hod_name) : '<span style="color:#d0342a">Not Assigned</span>' !!}</td>
                                <td class="dept-status">
                                    @if (strtolower(trim($row->status ?? '')) === 'active')
                                        <span class="status-active">Active</span>
                                    @else
                                        <span class="status-inactive">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <a class="action-orange" href="{{ route('admin.departments.edit', $row->department_id) }}">Edit</a> |
                                    <a class="action-orange" href="{{ route('admin.departments.assign_hod', $row->department_id) }}">Assign HOD</a> |
                                    <a class="action-orange" href="{{ route('admin.departments.toggle-status', $row->department_id) }}">Toggle Status</a>
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
