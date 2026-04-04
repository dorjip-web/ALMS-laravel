@extends('admin_layout')

@section('content')
<div style="padding:28px">
    <h3>Add Leave Type</h3>
    <form method="POST" action="{{ route('admin.leave_types.store') }}" class="leave-form" style="width:100%;background:#fff;padding:28px;border-radius:10px;box-shadow:0 2px 8px rgba(0,0,0,0.04);margin-top:18px;box-sizing:border-box;">
        @csrf
        <div class="row-grid-4">
            <div class="col">
                <label>Leave Name</label>
                <input type="text" name="leave_name" class="form-control" value="{{ old('leave_name') }}" required>
            </div>
            <div class="col">
                <label>Leave Code</label>
                <input type="text" name="leave_code" class="form-control" value="{{ old('leave_code') }}" required>
            </div>
            <div class="col">
                <label>Description</label>
                <textarea name="description" class="form-control">{{ old('description') }}</textarea>
            </div>
            <div class="col">
                <label>Max Per Year</label>
                <input type="number" name="max_per_year" class="form-control" value="{{ old('max_per_year') }}">
            </div>
        </div>
        <div style="margin-top:12px">
            <label>Status</label>
            <select name="status" class="form-control">
                <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>
        <div style="margin-top:12px;display:flex;gap:12px;align-items:center">
            <button type="submit" class="btn">Save</button>
            <a href="#" onclick="window.history.back();return false;" class="btn-cancel">Cancel</a>
        </div>
    </form>
</div>
@endsection
