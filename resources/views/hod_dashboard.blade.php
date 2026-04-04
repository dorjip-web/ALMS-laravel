<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>HoD Dashboard</title>
    <link rel="stylesheet" href="{{ asset('css/hod_dashboard.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body{font-family: 'Inter', system-ui, -apple-system, 'Segoe UI', Roboto, Arial, sans-serif;}
        .topbar .logout form button{background:transparent;border:0;padding:6px 10px;border-radius:6px;font-weight:600}
        .topbar .logout form button:hover{background:#f8f9fa}
    </style>
</head>
<body>
<div class="app">
    @include('partials.hod_sidebar')

    <main class="main">
        <header class="topbar">
            <div class="search"><input placeholder="Search..."></div>
            <div class="notifications">
                <a href="#" class="notif-btn" title="Notifications" aria-label="Notifications">✉️<span class="badge">0</span></a>
            </div>
            <div class="logout">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit">Logout</button>
                </form>
            </div>
        </header>

        <div class="container">
            <header>
                <h1>HoD Dashboard</h1>
            </header>

            @if (! $authorized)
                <div class="access-denied">
                    <h2>Access Denied</h2>
                    <p>You are not assigned as HoD.</p>
                    <a href="{{ route('dashboard') }}">Return to Dashboard</a>
                </div>
            @else
                @if (session('message'))
                    <div class="notice">{{ session('message') }}</div>
                @endif

                <section class="tiles">
                    <a class="tile tile-purple" href="{{ route('hod.staff_list') }}">
                        <div class="icon">👥</div>
                        <div class="content">
                            <div class="title">Total Staff</div>
                            <div class="subtitle">View your department staff</div>
                        </div>
                        <div class="content" style="text-align:right"><div class="title">{{ $totalStaff ?? 0 }}</div></div>
                    </a>

                    <a class="tile tile-blue" href="{{ route('hod.pending') }}">
                        <div class="icon">⏳</div>
                        <div class="content">
                            <div class="title">Pending</div>
                            <div class="subtitle">Requests awaiting your action</div>
                        </div>
                        <div style="text-align:right"><div class="title">{{ $summary['pending'] }}</div></div>
                    </a>

                    <a class="tile tile-gray" href="{{ route('hod.recent') }}">
                        <div class="icon">📨</div>
                        <div class="content">
                            <div class="title">Forwarded to MS</div>
                            <div class="subtitle">Recently forwarded</div>
                        </div>
                        <div style="text-align:right"><div class="title">{{ $summary['forwarded'] }}</div></div>
                    </a>

                    <a class="tile tile-yellow" href="{{ route('hod.adhoc.index') }}">
                        <div class="icon">📌</div>
                        <div class="content">
                            <div class="title">Adhoc Requests</div>
                            <div class="subtitle">Manage adhoc duty requests</div>
                        </div>
                        <div style="text-align:right"><div class="title">{{ $adhocCount ?? 0 }}</div></div>
                    </a>

                    <a class="tile tile-red" href="{{ route('hod.recent') }}">
                        <div class="icon">❌</div>
                        <div class="content">
                            <div class="title">Rejected</div>
                            <div class="subtitle">Requests you rejected</div>
                        </div>
                        <div style="text-align:right"><div class="title">{{ $summary['rejected'] }}</div></div>
                    </a>

                    <a class="tile tile-orange" href="{{ route('hod.on_tour') }}">
                        <div class="icon">🧭</div>
                        <div class="content">
                            <div class="title">Staff On Tour</div>
                            <div class="subtitle">Current tours in your depts</div>
                        </div>
                        <div style="text-align:right"><div class="title">{{ $onTourCount ?? 0 }}</div></div>
                    </a>
                </section>
                <!-- Removed embedded detailed lists (moved to dedicated pages) -->
            @endif
        </div>
    </main>
</div>

<script>
    document.querySelectorAll('.sidebar .menu a').forEach(function (a) {
        a.addEventListener('click', function () {
            document.querySelectorAll('.sidebar .menu a').forEach(function (x) {
                x.classList.remove('active');
            });
            this.classList.add('active');
        });
    });
</script>
</body>
</html>
