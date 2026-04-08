@extends('admin_layout')

@section('pageTitle', 'Manage Admin')

@section('content')
<div style="padding:18px">
    <h1>Manage Admin</h1>

    <div class="panel" style="max-width:860px;padding:18px">
        <form method="POST" action="{{ route('admin.settings.manage.save', $admin['id'] ?? '') }}">
            @csrf
            <div class="admin-form">
                <div class="form-grid">
                    <div class="form-group">
                        <label>Admin Name</label>
                        <input name="name" value="{{ old('name', $admin['name'] ?? '') }}" required>
                    </div>
                    <div class="form-group">
                        <label>Username / Email</label>
                        <input name="username" value="{{ old('username', $admin['username'] ?? $admin['email'] ?? '') }}" required>
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" placeholder="Leave blank to keep existing">
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select name="active">
                            <option value="1" {{ (old('active', $admin['active'] ?? $admin['is_active'] ?? 1) == 1) ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ (old('active', $admin['active'] ?? $admin['is_active'] ?? 1) == 0) ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                </div>

                <div class="form-actions">
                    <button class="btn" type="submit">Save Admin</button>
                    <a href="{{ route('admin.settings.index') }}" class="btn btn-secondary" style="margin-left:8px;text-decoration:none;">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
