@extends('admin_layout')

@section('pageTitle','Change Admin Password')

@section('content')
    <div class="container">
        <section class="panel">
            <h2>Change Admin Password (stub)</h2>
            <form method="POST" action="{{ url('/admin/settings/change-admin-password') }}" style="max-width:640px">
                @csrf
                <div style="margin-bottom:12px">
                    <label>Current Password</label>
                    <input type="password" name="current_password" required style="width:100%;padding:8px;border:1px solid #cfd8db;border-radius:6px">
                </div>
                <div style="margin-bottom:12px">
                    <label>New Password</label>
                    <input type="password" name="new_password" required style="width:100%;padding:8px;border:1px solid #cfd8db;border-radius:6px">
                </div>
                <button class="btn" type="submit">Change Password</button>
            </form>
        </section>
    </div>
@endsection
