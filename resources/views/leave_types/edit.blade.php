@extends('admin_layout')

@section('content')
<div style="padding:18px">
    <h3>Edit Leave Type</h3>
    <form method="POST" action="{{ route('leave-types.update', $leaveType->leave_type_id) }}">
        @csrf
        @method('PUT')
        <div class="row-grid-4">
            <div class="col">
                <label>Leave Name</label>
                <input type="text" name="leave_name" class="form-control" value="{{ old('leave_name', $leaveType->leave_name) }}" required>
            </div>
            <div class="col">
                <label>Leave Code</label>
                <input type="text" name="leave_code" class="form-control" value="{{ old('leave_code', $leaveType->leave_code) }}" required>
            </div>
            <div class="col">
                <label>Description</label>
                <textarea name="description" class="form-control">{{ old('description', $leaveType->description) }}</textarea>
            </div>
            <div class="col">
                <label>Max Per Year</label>
                <input type="number" name="max_per_year" class="form-control" value="{{ old('max_per_year', $leaveType->max_per_year) }}">
            </div>
        </div>
        <div style="margin-top:12px">
            <label>Status</label>
            <select name="status" class="form-control">
                <option value="active" {{ old('status', $leaveType->status) == 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ old('status', $leaveType->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>
        <div style="margin-top:12px;display:flex;gap:12px;align-items:center">
            <button type="submit" class="btn">Update</button>
            <a href="{{ route('leave-types.index') }}" class="btn-cancel">Cancel</a>
        </div>
    </form>
</div>
@endsection
