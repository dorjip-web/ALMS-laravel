@extends('admin_layout')

@section('content')
<div style="padding:18px">
    <h1>Leave Types</h1>
    <a href="{{ route('leave-types.create') }}" class="btn">+ Add Leave Type</a>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <div class="leave-history" style="margin-top:18px">
        <div class="table-wrap">
            <table class="users">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Code</th>
                        <th>Description</th>
                        <th>Max/Year</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($leaveTypes as $leaveType)
                    <tr>
                        <td>{{ $leaveType->leave_type_id }}</td>
                        <td>{{ $leaveType->leave_name }}</td>
                        <td>{{ $leaveType->leave_code }}</td>
                        <td>{{ $leaveType->description }}</td>
                        <td>{{ $leaveType->max_per_year }}</td>
                        <td>
                            @if($leaveType->status === 'active')
                                <span class="status-active">Active</span>
                            @else
                                <span class="status-inactive">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('leave-types.edit', $leaveType->leave_type_id) }}">Edit</a> |
                            <form action="{{ route('leave-types.destroy', $leaveType->leave_type_id, false) }}" method="POST" style="display:inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('Are you sure?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
