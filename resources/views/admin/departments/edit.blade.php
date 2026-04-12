@extends('admin_layout')

@section('content')
<style>
    :root{--accent:#f97316;--muted:#6b7280}
    .panel { padding:22px; max-width:640px; background:transparent }
    h2 { font-size:34px; margin:0 0 18px 0 }
    .form-label { display:block; margin-bottom:8px; font-weight:700; color:#061018 }
    .form-input { padding:10px 12px; width:100%; max-width:520px; border:1px solid #e6eef8; border-radius:6px; box-shadow:none }
    .form-select { padding:10px 12px; border:1px solid #e6eef8; border-radius:6px }
    .form-actions { margin-top:18px }
    .btn { background:var(--accent); color:#fff; padding:10px 16px; border-radius:8px; border:none; font-weight:800; cursor:pointer }
    .btn-secondary { background:#fff; color:#333; border:1px solid #cfd8db; padding:10px 14px; border-radius:6px }
    .error { color:#d0342a; margin-bottom:12px }
</style>

<div class="panel">
    <h2>Edit Department</h2>
    @if ($errors->any())
        <div class="error">
            {{ $errors->first() }}
        </div>
    @endif
    <form method="POST" action="{{ route('admin.departments.edit.update', $department->department_id, false) }}">
        @csrf
        @method('POST')
        <label class="form-label">Department Name:</label>
        <input class="form-input" type="text" name="department_name" value="{{ old('department_name', $department->department_name) }}" required>

        <label class="form-label" style="margin-top:14px">Status:</label>
        <select name="status" class="form-select">
            <option value="active" @if($department->status === 'active') selected @endif>Active</option>
            <option value="inactive" @if($department->status === 'inactive') selected @endif>Inactive</option>
        </select>

        <div class="form-actions">
            <button type="submit" name="submit" class="btn">Update Department</button>
            <a href="{{ route('admin.departments_hods.index') }}" class="btn-secondary" style="margin-left:10px;display:inline-block;text-decoration:none;line-height:28px">Cancel</a>
        </div>
    </form>
</div>
@endsection
