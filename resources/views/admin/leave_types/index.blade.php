@extends('admin_layout')

@section('content')
<div style="padding:18px">
	<h1>Leave Management</h1>
	<div style="margin-bottom:18px;">
		<a href="{{ route('admin.leave_types.create') }}" class="btn" style="font-size:15px;padding:7px 16px;text-decoration:none;">➕ Add Leave Type</a>
	</div>

	@if(session('success'))
		<div style="margin-top:12px;color:green">{{ session('success') }}</div>
	@endif

	@if ($leaveTypes->isEmpty())
		<p>No leave types found.</p>
	@else
		<div class="leave-history">
			<div class="table-wrap">
				<table class="users">
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
							<td>
								@php $status = trim($lt->status ?? ''); $lower = strtolower($status); @endphp
								@if ($lower === 'active')
									<span class="status-active">{{ ucfirst($status) }}</span>
								@elseif ($lower === 'inactive')
									<span class="status-inactive">{{ ucfirst($status) }}</span>
								@else
									{{ ucfirst($status) }}
								@endif
							</td>
							<td>
								<a class="action-orange" href="{{ route('admin.leave_types.edit', $lt->leave_type_id) }}">Edit</a> |
								<form method="POST" action="{{ route('admin.leave_types.toggle', $lt->leave_type_id) }}" style="display:inline">
									@csrf
									<button class="action-orange" type="submit" style="background:none;border:none;padding:0;margin:0;cursor:pointer">Toggle</button>
								</form>
								|
								<form method="POST" action="{{ route('admin.leave_types.destroy', $lt->leave_type_id) }}" style="display:inline" onsubmit="return confirm('Delete this leave type?');">
									@csrf
									@method('DELETE')
									<button class="action-orange" style="background:none;border:none;padding:0;margin:0;cursor:pointer">Delete</button>
								</form>
							</td>
						</tr>
						@endforeach
					</tbody>
				</table>
			</div>
		</div
	@endif
</div>
@endsection
