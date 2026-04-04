@extends('admin_layout')

@section('content')
<div style="padding:18px">
	<div style="display:flex;justify-content:space-between;align-items:center">
		<h2>Leave Types</h2>
		<a class="btn" href="{{ route('admin.leave_types.create') }}">Add Leave Type</a>
	</div>

	@if(session('success'))
		<div style="margin-top:12px;color:green">{{ session('success') }}</div>
	@endif

	<table class="users" style="margin-top:12px;width:100%">
		<thead>
			<tr>
				<th>ID</th>
				<th>Name</th>
				<th>Code</th>
				<th>Max/Year</th>
				<th>Status</th>
				<th>Actions</th>
			</tr>
		</thead>
		<tbody>
			@foreach($leaveTypes as $lt)
				<tr>
					<td>{{ $lt->leave_type_id }}</td>
					<td>{{ $lt->leave_name }}</td>
					<td>{{ $lt->leave_code }}</td>
					<td>{{ $lt->max_per_year ?? '-' }}</td>
					<td>{{ ucfirst($lt->status) }}</td>
					<td style="display:flex;gap:8px">
						<a class="btn" href="{{ route('admin.leave_types.edit', $lt->leave_type_id) }}">Edit</a>
						<form method="POST" action="{{ route('admin.leave_types.toggle', $lt->leave_type_id) }}" style="display:inline">
							@csrf
							<button class="btn" type="submit">{{ $lt->status === 'active' ? 'Deactivate' : 'Activate' }}</button>
						</form>
						<form method="POST" action="{{ route('admin.leave_types.destroy', $lt->leave_type_id) }}" style="display:inline" onsubmit="return confirm('Delete this leave type?');">
							@csrf
							@method('DELETE')
							<button class="btn btn-danger">Delete</button>
						</form>
					</td>
				</tr>
			@endforeach
		</tbody>
	</table>
</div>
@endsection
