<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>{{ $title ?? 'Mobile' }}</title>
    <link rel="stylesheet" href="/css/mobile_dashboard.css?v=3">
    <style>
        body {
            font-family: Inter, system-ui, Arial, Helvetica, sans-serif;
            background: transparent
        }

        .m-mobile-root {
            padding: 12px
        }

        .m-card {
            background: #fff;
            border-radius: 12px;
            padding: 12px;
            margin-bottom: 12px;
            box-shadow: 0 2px 8px rgba(12, 22, 34, 0.04)
        }

        .m-card h2 {
            margin: 0 0 8px 0
        }

        .table-wrap {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch
        }
    </style>
</head>

<body>
    <div class="m-mobile-root">
        <header class="m-header">
            <div style="display:flex;align-items:center;gap:8px;margin-bottom:8px">
                <a href="/dashboard" style="text-decoration:none;color:inherit">🏠 Home</a>
                <div style="flex:1;text-align:center;font-weight:700">{{ $title ?? '' }}</div>
                <div style="width:36px"></div>
            </div>
        </header>

        <main>
            <div class="m-card">
                {!! $content !!}
            </div>
        </main>

        <nav
            style="position:fixed;left:12px;right:12px;bottom:12px;background:rgba(255,255,255,0.98);padding:8px;border-radius:14px;box-shadow:0 4px 18px rgba(0,0,0,0.06);display:flex;justify-content:space-around;align-items:center">
            <a href="/dashboard">🏠</a>
            <a href="{{ route('dashboard.attendance_summary') }}">📊</a>
            <a href="{{ route('dashboard.leave_form') }}">🧾</a>
            <a href="{{ route('dashboard.tour') }}">⏰</a>
            <a href="{{ route('dashboard.adhoc_requests') }}">👤</a>
        </nav>
    </div>
</body>

</html>
