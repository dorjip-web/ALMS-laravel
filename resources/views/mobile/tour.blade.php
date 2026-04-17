<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Tour Records</title>
    <link rel="stylesheet" href="/css/mobile_dashboard.css?v=3">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        .form-control {
            padding: 10px;
            border-radius: 8px;
            border: 1px solid #e6edf3;
            width: 100%;
            box-sizing: border-box
        }
    </style>
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
            <div id="tour" class="m-card">
                <h3>Tour Records</h3>
                <form method="POST" action="{{ route('dashboard.tour.store', [], false) }}"
                    class="leave-form space-y-3" style="margin-top:10px;" enctype="multipart/form-data">
                    @csrf
                    <div class="row-grid-4">
                        <div class="col">
                            <label>Place</label>
                            <input type="text" name="place" value="{{ old('place') }}" placeholder="Tour place"
                                required class="form-control px-3 py-2 rounded-md border border-gray-200">
                        </div>
                        <div class="col">
                            <label>Start</label>
                            <input type="date" name="start_date" value="{{ old('start_date') }}" required
                                min="{{ now('Asia/Thimphu')->toDateString() }}"
                                class="form-control px-3 py-2 rounded-md border border-gray-200">
                        </div>
                        <div class="col">
                            <label>End</label>
                            <input type="date" name="end_date" value="{{ old('end_date') }}" required
                                min="{{ now('Asia/Thimphu')->toDateString() }}"
                                class="form-control px-3 py-2 rounded-md border border-gray-200">
                        </div>
                        <div class="col">
                            <label>Total Days</label>
                            <input type="text" id="tour-total-days" value="-" readonly
                                class="form-control px-3 py-2 rounded-md border border-gray-200 bg-gray-50">
                        </div>
                    </div>
                    <div class="row">
                        <label>Purpose</label>
                        <input type="text" name="purpose" value="{{ old('purpose') }}" placeholder="Purpose of tour"
                            required class="form-control px-3 py-2 rounded-md border border-gray-200">
                    </div>
                    <div class="row">
                        <label>Office Order (PDF)</label>
                        <input type="file" name="office_order_pdf" accept="application/pdf" class="form-control">
                    </div>
                    <div class="row">
                        <button class="btn bg-blue-700 text-white px-4 py-2 rounded-md font-semibold"
                            type="submit">Save Tour Record</button>
                    </div>
                </form>

                <div class="leave-history" style="margin-top:8px;">
                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>To date</th>
                                    <th>Total Days</th>
                                    <th>Destination</th>
                                    <th>Purpose</th>
                                    <th>Office Order</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($tourRecords ?? [] as $tour)
                                    <tr>
                                        <td>{{ $tour['date'] ?? '-' }}</td>
                                        <td>{{ $tour['to_date'] ?? '-' }}</td>
                                        <td>{{ $tour['total_days'] ?? '-' }}</td>
                                        <td>{{ $tour['destination'] ?? '-' }}</td>
                                        <td>{{ $tour['purpose'] ?? '-' }}</td>
                                        <td>
                                            @if (!empty($tour['office_order_pdf']))
                                                <a href="{{ asset('storage/' . $tour['office_order_pdf']) }}"
                                                    target="_blank">View PDF</a>
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6">No tour records found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>

        <nav class="m-bottom-nav">
            <a class="m-nav-item" href="/dashboard"><span class="m-nav-icon">🏠</span><span
                    class="m-nav-label">Home</span></a>
            <a class="m-nav-item" href="{{ route('dashboard.attendance_summary') }}"><span
                    class="m-nav-icon">📊</span><span class="m-nav-label">Summary</span></a>
            <a class="m-nav-item" href="{{ route('dashboard.leave_form') }}"><span class="m-nav-icon">🧾</span><span
                    class="m-nav-label">Leave</span></a>
            <a class="m-nav-item active" href="{{ route('dashboard.tour') }}"><span class="m-nav-icon">⏰</span><span
                    class="m-nav-label">Tour</span></a>
            <a class="m-nav-item" href="{{ route('dashboard.adhoc_requests') }}"><span
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

        <script>
            (function() {
                const start = document.querySelector('input[name="start_date"]');
                const end = document.querySelector('input[name="end_date"]');
                const total = document.getElementById('tour-total-days');

                function update() {
                    if (!start || !end || !total) return;
                    if (!start.value || !end.value) {
                        total.value = '-';
                        return;
                    }
                    const s = new Date(start.value + 'T00:00:00');
                    const e = new Date(end.value + 'T00:00:00');
                    if (Number.isNaN(s.getTime()) || Number.isNaN(e.getTime()) || e < s) {
                        total.value = '-';
                        return;
                    }
                    const diffMs = e.getTime() - s.getTime();
                    const days = Math.floor(diffMs / 86400000) + 1;
                    total.value = days + (days === 1 ? ' day' : ' days');
                }
                start?.addEventListener('change', update);
                end?.addEventListener('change', update);
                update();
            })();
        </script>
    </div>
</body>

</html>
