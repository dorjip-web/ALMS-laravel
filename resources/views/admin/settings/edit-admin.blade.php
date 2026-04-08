@extends('admin_layout')

@section('pageTitle','Edit Admin Details')

@section('content')
    <div class="container">
        <section class="panel">
            <h2>Edit Admin Details (stub)</h2>
            <form method="POST" action="{{ url('/admin/settings/edit-admin') }}" style="max-width:640px">
                @csrf
                <div style="margin-bottom:12px">
                    <label>Admin Username</label>
                    <input name="username" required style="width:100%;padding:8px;border:1px solid #cfd8db;border-radius:6px">
                </div>
                <div style="margin-bottom:12px">
                    <label>Email</label>
                    <input name="email" type="email" style="width:100%;padding:8px;border:1px solid #cfd8db;border-radius:6px">
                </div>
                <button class="btn" type="submit">Save Changes</button>
            </form>
        </section>
    </div>
@endsection
