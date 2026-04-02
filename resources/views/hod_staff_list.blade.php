<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Staff List</title>
    <link rel="stylesheet" href="{{ asset('css/hod_dashboard.css') }}">
</head>
<body>
<div class="app">
    <aside class="sidebar">
        <div class="profile">
            <div class="avatar">H</div>
            <div class="username">{{ $username ?? auth()->user()->name }}</div>
        </div>

        <nav class="menu">
            <a href="{{ route('dashboard') }}">Back to My Dashboard</a>
            <a href="{{ route('hod.dashboard') }}">HoD Dashboard</a>
            <a href="{{ route('hod.staff_list') }}" class="active">View Staff List</a>
        </nav>
    </aside>

    <main class="main">
        <header class="topbar">
            <div class="search"><input placeholder="Search..."></div>
            <div class="logout">
                <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                    @csrf
                    <button type="submit">Logout</button>
                </form>
            </div>
        </header>

        <div class="container">
            <header>
                <h1>Staff List</h1>
            </header>

            <section class="panel">
                <div class="leave-history"><div class="table-wrap"><table class="users requests">
                    <thead>
                        <tr>
                            <th><strong>ID</strong></th>
                            <th><strong>Name</strong></th>
                            <th><strong>EID</strong></th>
                            <th><strong>Designation</strong></th>
                            <th><strong>Department</strong></th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (empty($staff))
                            <tr><td colspan="5" class="empty">No staff found for your department(s).</td></tr>
                        @else
                            @foreach ($staff as $s)
                                <tr>
                                    <td>{{ $s['employee_id'] ?? '-' }}</td>
                                    <td>{{ $s['employee_name'] ?? '-' }}</td>
                                    <td>{{ $s['eid'] ?? '-' }}</td>
                                    <td>{{ $s['designation'] ?? '-' }}</td>
                                    <td>{{ $s['department_name'] ?? '-' }}</td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table></div></div>
            </section>
        </div>
    </main>
</div>
</body>
</html>
