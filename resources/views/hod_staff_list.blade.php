<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Staff List</title>
    <link rel="stylesheet" href="/css/dashboard.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        /* Match other dashboard pages */
        .topbar .logout form button{
            background:#fff;
            padding:8px 12px;
            border-radius:6px;
            border:none;
            color:var(--orange);
            font-weight:600;
            cursor:pointer;
        }
        .topbar .logout form button:hover{filter:brightness(0.97)}
        /* Staff list: remove outer panel/card styling so table is plain */
        .staff-list-panel {
            border: none !important;
            box-shadow: none !important;
            background: transparent !important;
            padding: 0 !important;
            border-radius: 0 !important;
        }
        .staff-list-panel .leave-history,
        .staff-list-panel .table-wrap {
            border: none !important;
            box-shadow: none !important;
            background: transparent !important;
            padding: 0 !important;
            margin: 0 !important;
        }
        /* Ensure the table header area isn't inside a card-like container */
        .staff-list-panel .users { background: transparent !important; }
        /* Make the staff list table container full width */
        .staff-list-container{max-width:100% !important;padding:0 18px !important;margin:0 auto}
        .staff-list-container .panel{margin:0;border-radius:8px 8px 0 0}
    </style>
</head>
<body>
<div class="app">
    @include('partials.hod_sidebar')

    <main class="main">
        <header class="topbar">
            <div class="search"><input placeholder="Search..."></div>
            <div class="logout">
                <form method="POST" action="/logout">
                    @csrf
                    <button type="submit">Logout</button>
                </form>
            </div>
        </header>

        <div class="staff-list-container">
            <header>
                <h1>Staff List</h1>
            </header>

            <section class="panel staff-list-panel">
                <div class="leave-history"><div class="table-wrap"><table class="users">
                    <thead>
                        <tr>
                            <th><strong>SL.No</strong></th>
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
                                    <td>{{ $loop->iteration }}</td>
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
