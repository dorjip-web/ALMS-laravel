@extends('admin_layout')

@section('content')
<div style="padding:28px">
    <h1>Edit Leave Type</h1>
    <form method="POST" action="{{ route('admin.leave_types.update', $leaveType->leave_type_id) }}" class="leave-form" style="width:100%;background:#fff;padding:28px;border-radius:10px;box-shadow:0 2px 8px rgba(0,0,0,0.04);margin-top:18px;box-sizing:border-box;">
        @csrf
        @method('PUT')
        <div class="row-grid-4">
            <div class="col">
                <label>Leave Name</label>
                <input type="text" name="leave_name" value="{{ old('leave_name', $leaveType->leave_name) }}" required>
            </div>
            <div class="col">
                <label>Leave Code</label>
                <input type="text" name="leave_code" value="{{ old('leave_code', $leaveType->leave_code) }}" required>
            </div>
            <div class="col">
                <label>Description</label>
                <textarea name="description">{{ old('description', $leaveType->description) }}</textarea>
            </div>
            <div class="col">
                <label>Max Per Year</label>
                <input type="number" name="max_per_year" value="{{ old('max_per_year', $leaveType->max_per_year) }}">
            </div>
        </div>
        <div style="margin-top:12px">
            <label>Status</label>
            <select name="status">
                <option value="active" {{ old('status', $leaveType->status) == 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ old('status', $leaveType->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>
        <div style="margin-top:18px;display:flex;gap:12px;align-items:center">
            <button type="submit" class="btn">Update</button>
            <a href="#" onclick="window.history.back();return false;" class="btn-cancel">Cancel</a>
        </div>
    </form>
</div>
@endsection
