<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Adhoc Requests</title>
    <link rel="stylesheet" href="/css/mobile_dashboard.css?v=3">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
</head>

<body>
    @php $fullName = $employee['employee_name'] ?? auth()->user()->name; @endphp
    <div class="m-mobile-root">
        <header class="m-header">
            <button class="m-menu-btn" aria-label="menu" id="m-menu-btn">☰</button>
            <div class="m-menu-dropdown" id="m-menu-dropdown" aria-hidden="true">
                <form id="m-logout-form" method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="button" id="m-logout-btn" class="m-logout-btn">Logout</button>
                </form>
            </div>
            <div class="m-header-center">
                <div class="m-logo-text m-logo-left">NTMH</div>
                <img class="m-logo-img" src="/images/ntmh-logo.png" alt="logo" />
                <div class="m-logo-text m-logo-right">ALMS</div>
            </div>
            <div style="width:36px"></div>
        </header>

        <main class="m-main">
            <div class="m-card">
                <h3>Adhoc Request</h3>
                @if (!$tableExists)
                    <div class="summary-empty">Adhoc requests table not found.</div>
                @else
                    <form method="POST" action="{{ route('dashboard.adhoc_requests.store', [], false) }}"
                        class="leave-form" style="margin-bottom:18px;">
                        @csrf
                        <div class="row-grid">
                            <div class="col">
                                <label>Date</label>
                                <input type="date" name="date" required class="form-control"
                                    placeholder="mm/dd/yyyy">
                            </div>
                            <div class="col">
                                <label>Purpose</label>
                                <select name="purpose" required class="form-control">
                                    <option value="meeting">Meeting</option>
                                    <option value="emergency">Emergency</option>
                                </select>
                            </div>
                        </div>

                        <div class="row" style="margin-top:12px">
                            <label>Remarks</label>
                            <input type="text" name="remarks" maxlength="255" class="form-control">
                        </div>

                        <div style="margin-top:12px">
                            <button type="submit" class="btn">Submit Adhoc Request</button>
                        </div>
                    </form>

                    <h4>Your recent adhoc requests</h4>
                    @if (empty($rows))
                        <div class="summary-empty">No adhoc requests found.</div>
                    @else
                        <div class="leave-history" style="margin-top:8px;">
                            <div class="table-wrap">
                                <table class="users">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Purpose</th>
                                            <th>Remarks</th>
                                            <th>Created</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($rows as $r)
                                            <tr>
                                                <td>{{ $r['date'] ?? '-' }}</td>
                                                <td>{{ ucfirst($r['purpose'] ?? '-') }}</td>
                                                <td>{{ $r['remarks'] ?? '-' }}</td>
                                                <td>{{ $r['created_at'] ?? '-' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                @endif
            </div>
            @if (!empty($rows))
                <div class="leave-history" style="margin-top:8px;">
                    <div class="table-wrap">
                        <table class="users">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Purpose</th>
                                    <th>Remarks</th>
                                    <th>Created</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($rows as $r)
                                    <tr>
                                        <td>{{ $r['date'] ?? '-' }}</td>
                                        <td>{{ ucfirst($r['purpose'] ?? '-') }}</td>
                                        <td>{{ $r['remarks'] ?? '-' }}</td>
                                        <td>{{ $r['created_at'] ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </main>

        <nav class="m-bottom-nav">
            <a class="m-nav-item" href="/dashboard"><span class="m-nav-icon">🏠</span><span
                    class="m-nav-label">Home</span></a>
            <a class="m-nav-item" href="{{ route('dashboard.attendance_summary') }}"><span
                    class="m-nav-icon">📊</span><span class="m-nav-label">Summary</span></a>
            <a class="m-nav-item" href="{{ route('dashboard.leave_form') }}"><span class="m-nav-icon">🧾</span><span
                    class="m-nav-label">Leave</span></a>
            <a class="m-nav-item" href="{{ route('dashboard.tour') }}"><span class="m-nav-icon">⏰</span><span
                    class="m-nav-label">Tour</span></a>
            <a class="m-nav-item active" href="{{ route('dashboard.adhoc_requests') }}"><span
                    class="m-nav-icon">👤</span><span class="m-nav-label">Adhoc</span></a>
        </nav>
        <script>
            (function() {
                const menuBtn = document.getElementById('m-menu-btn');
                const menuDropdown = document.getElementById('m-menu-dropdown');

                if (menuBtn && menuDropdown) {
                    menuBtn.addEventListener('click', function(e) {
                        e.stopPropagation();
                        const opened = menuDropdown.classList.toggle('open');
                        menuDropdown.setAttribute('aria-hidden', (!opened).toString());
                    });

                    document.addEventListener('click', function(e) {
                        if (!menuDropdown.contains(e.target) && !menuBtn.contains(e.target)) {
                            menuDropdown.classList.remove('open');
                            menuDropdown.setAttribute('aria-hidden', 'true');
                        }
                    });

                    const logoutBtn = document.getElementById('m-logout-btn');
                    const logoutForm = document.getElementById('m-logout-form');
                    if (logoutBtn && logoutForm) {
                        logoutBtn.addEventListener('click', function(ev) {
                            ev.preventDefault();
                            const action = logoutForm.getAttribute('action');
                            let tokenInput = logoutForm.querySelector('input[name="_token"]');
                            let token = tokenInput ? tokenInput.value : null;
                            if (!token) {
                                const meta = document.querySelector('meta[name="csrf-token"]');
                                if (meta) token = meta.getAttribute('content');
                            }
                            try {
                                const urlObj = new URL(action, window.location.origin);
                                const sameOriginPath = urlObj.pathname + (urlObj.search || '');

                                function readCookie(name) {
                                    const v = document.cookie.match('(^|;)\\s*' + name + '\\s*=\\s*([^;]+)');
                                    return v ? v.pop() : null;
                                }
                                const xsrfCookie = readCookie('XSRF-TOKEN');
                                const xsrfHeader = xsrfCookie ? decodeURIComponent(xsrfCookie) : (token || '');

                                fetch(sameOriginPath, {
                                    method: 'POST',
                                    credentials: 'same-origin',
                                    headers: {
                                        'X-CSRF-TOKEN': token || '',
                                        'X-XSRF-TOKEN': xsrfHeader || '',
                                        'Content-Type': 'application/x-www-form-urlencoded'
                                    },
                                    body: new URLSearchParams({
                                        '_token': token || ''
                                    })
                                }).then(function(res) {
                                    window.location.reload();
                                }).catch(function(err) {
                                    console.error('Logout fetch failed', err);
                                    window.location.reload();
                                });
                            } catch (ex) {
                                console.error('Logout URL parsing error', ex);
                                alert('Logout failed. Please try again.');
                            }
                        });
                    }
                }
            }());
        </script>
    </div>
</body>

</html>
