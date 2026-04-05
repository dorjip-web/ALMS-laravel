@extends('admin_layout')

@section('content')
<div class="card">
    <h2>Roles & Permissions</h2>
    <p>This administration area has been simplified. The original Roles & Permissions management was removed.</p>
    <p>If you want a new Roles & Permissions management UI, tell me the desired behaviour and I'll implement it.</p>
    <p style="margin-top:12px"><a href="{{ route('admin.users.index') }}" class="btn">Back to User Management</a></p>
</div>
@endsection
