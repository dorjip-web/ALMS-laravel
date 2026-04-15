<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Admin Login — Attendance App</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        :root{--bg:#0f1724;--card:#0b1220;--accent:#6ee7b7;--muted:#9aa4b2}
        *{box-sizing:border-box}
        html,body{height:100%;margin:0;font-family:Inter, system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', Arial}
        body{background:linear-gradient(135deg,#071029 0%, #09203f 50%, #0b1220 100%);color:#e6eef6;display:flex;align-items:center;justify-content:center;padding:24px}
        .wrap{width:100%;max-width:1120px;display:grid;grid-template-columns:1fr 520px;gap:40px;align-items:center}
        .hero{padding:48px;border-radius:12px;color:var(--accent);background:linear-gradient(180deg, rgba(255,255,255,0.03), transparent);box-shadow:0 10px 30px rgba(2,6,23,0.6)}
        .hero h1{margin:0 0 12px;font-size:24px;line-height:1;color:#fff}
        .hero p{margin:0;color:var(--muted)}

        .card{background:linear-gradient(180deg, rgba(255,255,255,0.02), rgba(255,255,255,0.01));padding:36px;border-radius:12px;box-shadow:0 8px 30px rgba(2,6,23,0.6);backdrop-filter: blur(6px);min-height:450px}
        .brand{display:flex;flex-direction:column;align-items:center;text-align:center;gap:6px;margin-bottom:18px}
        .brand-logo{width:120px;height:120px;border-radius:50%;object-fit:contain;background:#fff;padding:6px;box-shadow:0 8px 24px rgba(2,6,23,0.35);margin-bottom:10px}
        h2{margin:0;font-size:26px;color:#fff}
        .desc{color:var(--muted);font-size:14px;margin-top:0}

        .form-row{margin-top:18px;max-width:760px;margin-left:auto;margin-right:auto}
        label{display:block;font-size:14px;color:var(--muted);margin-bottom:10px}
        .input{display:flex;align-items:center;background:#061222;border:1px solid rgba(255,255,255,0.04);padding:12px 14px;border-radius:10px}
        .input input{background:transparent;border:0;outline:0;color:#e6eef6;font-size:16px;width:100%;line-height:1.4;padding:6px 0}

        .actions{display:flex;gap:14px;margin-top:22px;justify-content:space-between;align-items:center}
        .btn{flex:0 0 auto;padding:14px 22px;border-radius:12px;border:0;cursor:pointer;font-weight:600;font-size:16px}
        .btn-primary{background:linear-gradient(90deg,#3b82f6,#6ee7b7);color:#042028;min-width:220px}
        .btn-ghost{background:rgba(255,255,255,0.02);border:1px solid rgba(255,255,255,0.12);color:#cfe8ef;min-width:180px;text-align:center;padding:12px 20px;border-radius:12px;transition:background .12s,border-color .12s,color .12s;text-decoration:none;display:inline-flex;align-items:center;justify-content:center}
        .btn-ghost:hover{background:rgba(255,255,255,0.04);border-color:rgba(255,255,255,0.2);color:#ffffff;text-decoration:none}
        .btn-ghost:focus{outline:none;box-shadow:0 8px 24px rgba(2,6,23,0.45)}

        .error{margin-top:12px;background:rgba(176,0,32,0.08);color:#ffb3b3;padding:10px;border-radius:8px;font-size:14px}
        .meta{margin-top:12px;color:var(--muted);font-size:13px}

        .small{font-size:13px;color:var(--muted)}
        @media (max-width:900px){.wrap{grid-template-columns:1fr;gap:18px}.hero{order:2}.card{order:1}}
    </style>
</head>
<body>
<main class="wrap" role="main">
    <section class="hero" aria-hidden="false">
        <h1>Attendance and leave Management System-Admin</h1>
        <p>Manage users, view reports and adjust system settings from the admin panel.</p>
        <div style="margin-top:18px;">
            <p class="small">Secure area. Only authorized personnel should sign in.</p>
        </div>
    </section>

    <section class="card" aria-labelledby="admin-login">
        <div class="brand">
            <img class="brand-logo" src="{{ asset('images/ntmh-logo.png') }}" alt="NTMH logo">
            <div>
                <h2 id="admin-login">Admin Login</h2>
                <div class="desc">Use your admin credentials to continue</div>
            </div>
        </div>

        @if (session('login_error'))
            <div class="error" role="alert">{{ session('login_error') }}</div>
        @endif

        <form method="post" action="{{ route('admin.login.post', [], false) }}" autocomplete="off" novalidate>
            @csrf
            <div class="form-row">
                <label for="username">Username</label>
                <div class="input">
                    <input id="username" name="username" type="text" inputmode="text" required placeholder="Enter username" autofocus>
                </div>
            </div>

            <div class="form-row">
                <label for="password">Password</label>
                <div class="input">
                    <input id="password" name="password" type="password" required placeholder="••••••••">
                </div>
            </div>

            <div class="actions">
                <button class="btn btn-primary" type="submit">Login</button>
                <a class="btn btn-ghost" href="{{ route('login', [], false) }}">Employee login</a>
            </div>
        </form>
    </section>
</main>
</body>
</html>
