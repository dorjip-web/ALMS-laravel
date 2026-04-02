<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            min-height: 100vh;
            display: grid;
            place-items: center;
            background: #f5f7fb;
            color: #1f2937;
        }

        .card {
            background: #fff;
            padding: 24px;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
            text-align: center;
        }

        a {
            color: #2563eb;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="card">
        <h1>{{ config('app.name', 'Laravel') }}</h1>
        <p><a href="{{ route('login') }}">Go to login</a></p>
    </div>
</body>
</html>
