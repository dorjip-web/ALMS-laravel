@extends('admin_layout')

@section('pageTitle','Add New Admin')

@section('content')
    <div class="container">
        <section class="panel">
            <h2>Add New Admin (stub)</h2>
            <form method="POST" action="{{ url('/admin/settings/add-admin') }}" style="max-width:640px">
                @csrf
                <div style="margin-bottom:12px">
                    <label>Username</label>
                    <input name="username" required style="width:100%;padding:8px;border:1px solid #cfd8db;border-radius:6px">
                </div>
                <div style="margin-bottom:12px">
                    <label>Password</label>
                    <input type="password" name="password" required style="width:100%;padding:8px;border:1px solid #cfd8db;border-radius:6px">
                </div>
                <button class="btn" type="submit">Create Admin</button>
            </form>
        </section>
    </div>
@endsection
