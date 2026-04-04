<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>MS Dashboard</title>
    <link rel="stylesheet" href="{{ asset('css/ms_dashboard.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
</head>
<body>
<div class="app">
    @include('partials.ms_sidebar')

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
                <h1>MS Dashboard</h1>
            </header>

            @if (! $authorized)
                <div class="access-denied">
                    <h2>Access Denied</h2>
                    <p>You are not assigned as MS.</p>
                    <a href="{{ route('dashboard') }}">Return to Dashboard</a>
                </div>
            @else
                @if (session('message'))
                    <div class="notice">{{ session('message') }}</div>
                @endif

                <section class="tiles">
                    <a class="tile tile-purple" href="{{ route('ms.staff_list') }}">
                        <div class="icon">👥</div>
                        <div class="content">
                            <div class="title">Total Staff</div>
                            <div class="subtitle">View staff directory</div>
                        </div>
                        <div class="content" style="text-align:right"><div class="title">{{ $totalStaff ?? 0 }}</div></div>
                    </a>

                    <a class="tile tile-blue" href="{{ route('ms.pending') }}">
                        <div class="icon">⏳</div>
                        <div class="content">
                            <div class="title">Pending</div>
                            <div class="subtitle">Requests awaiting your action</div>
                        </div>
                        <div style="text-align:right"><div class="title">{{ $summary['pending'] }}</div></div>
                    </a>

                    <a class="tile tile-gray" href="{{ route('ms.recent') }}?filter=approved">
                        <div class="icon">📨</div>
                        <div class="content">
                            <div class="title">Approved Leaves</div>
                            <div class="subtitle">Recently approved</div>
                        </div>
                        <div style="text-align:right"><div class="title">{{ $summary['approved'] }}</div></div>
                    </a>

                    <a class="tile tile-yellow" href="{{ route('ms.adhoc.index') }}">
                        <div class="icon">📌</div>
                        <div class="content">
                            <div class="title">Adhoc Requests</div>
                            <div class="subtitle">Manage adhoc duty requests</div>
                        </div>
                        <div style="text-align:right"><div class="title">{{ $adhocCount ?? 0 }}</div></div>
                    </a>

                    <a class="tile tile-red" href="{{ route('ms.recent') }}?filter=rejected">
                        <div class="icon">❌</div>
                        <div class="content">
                            <div class="title">Rejected</div>
                            <div class="subtitle">Requests you rejected</div>
                        </div>
                        <div style="text-align:right"><div class="title">{{ $summary['rejected'] }}</div></div>
                    </a>

                    <a class="tile tile-orange" href="{{ route('ms.on_tour') }}">
                        <div class="icon">🧭</div>
                        <div class="content">
                            <div class="title">Staff On Tour</div>
                            <div class="subtitle">Current tours in your depts</div>
                        </div>
                        <div style="text-align:right"><div class="title">{{ $onTourCount ?? 0 }}</div></div>
                    </a>
                </section>

                <!-- Detailed lists moved to dedicated pages: use sidebar links -->
            @endif
        </div>
    </main>
</div>
<script>
document.querySelectorAll('.sidebar .menu a').forEach(function (a) {
    a.addEventListener('click', function () {
        document.querySelectorAll('.sidebar .menu a').forEach(function (x) { x.classList.remove('active'); });
        this.classList.add('active');
    });
});
</script>
</body>
</html>
