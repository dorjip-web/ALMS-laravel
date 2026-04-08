<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Staff On Tour</title>
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
</head>
<body>
<div class="app">
    @include('partials.ms_sidebar')

    <main class="main">
        <header class="topbar">
            <div class="search"><input placeholder="Search..." disabled></div>
            <div class="logout">
                <form method="POST" action="{{ route('logout') }}" style="display:inline;">@csrf<button type="submit">Logout</button></form>
            </div>
        </header>

        <section class="grid" style="grid-template-columns:1fr; padding: 18px 0;">
            <div class="card leave-card">
                <h3>Staff Currently On Tour</h3>

                @if(session('flash_error'))
                    <div class="flash flash-error">{{ session('flash_error') }}</div>
                @endif
                @if(session('flash_success'))
                    <div class="flash flash-success">{{ session('flash_success') }}</div>
                @endif
                @if ($errors->any())
                    <div class="flash flash-error">{{ $errors->first() }}</div>
                @endif

                <!-- Add Tour Record form removed per user request -->

                <!-- Recent Staff On Tour removed per user request -->
            </div>
        </section>
    </main>
</div>
</body>
</html>
