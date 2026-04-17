<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Leave Request</title>
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

        .date-wrapper {
            position: relative;
        }

        .date-wrapper .date-placeholder {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            font-size: 0.95rem;
            pointer-events: none;
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
            <div class="m-card">
                <h3 class="m-card-title m-page-title">Leave Application</h3>
                <form method="post" action="{{ route('dashboard.leave', [], false) }}" class="leave-form">
                    @csrf
                    <div class="row-grid-3">
                        <div class="col">
                            <label>Type</label>
                            <select name="leave_type_id" id="m-leave-type-select" class="form-control">
                                @forelse ($leaveTypes as $lt)
                                    <option value="{{ $lt['leave_type_id'] }}">
                                        {{ str_replace('_', ' ', $lt['leave_name']) }}</option>
                                @empty
                                    <option value="">No leave type found</option>
                                @endforelse
                            </select>
                        </div>
                        <div class="col">
                            <label>Submit To</label>
                            <select name="submit_to" class="form-control">
                                <option value="hod">HoD</option>
                                <option value="ms">Medical Superintendent</option>
                            </select>
                        </div>
                        <div class="col">
                            <label>Start</label>
                            <div class="date-wrapper">
                                <input type="date" name="from_date" required
                                    data-min="{{ now('Asia/Thimphu')->toDateString() }}"
                                    class="form-control date-input">
                                <span class="date-placeholder">mm/dd/yyyy</span>
                            </div>
                        </div>

                        <div class="col">
                            <label>End</label>
                            <div class="date-wrapper">
                                <input type="date" name="to_date" required
                                    data-min="{{ now('Asia/Thimphu')->toDateString() }}"
                                    class="form-control date-input">
                                <span class="date-placeholder">mm/dd/yyyy</span>
                            </div>
                        </div>
                        <div class="col">
                            <label>Total Days</label>
                            <input name="total_days" type="number" step="0.5" min="0.5"
                                placeholder="e.g. 1 or 0.5" class="form-control">
                        </div>
                        <div class="col">
                            <label>Balance</label>
                            <input type="text" id="m-leave-balance" readonly class="form-control">
                        </div>
                    </div>

                    <div class="row">
                        <label>Reason</label>
                        <input name="reason" placeholder="Reason" required class="form-control">
                    </div>

                    <div class="row">
                        <button class="btn" type="submit">Submit</button>
                    </div>
                </form>
            </div>

            <div class="leave-history" style="margin-top:12px">
                <h4>Leave History</h4>
                <div class="table-wrap">
                    <table class="users">
                        <thead>
                            <tr>
                                <th>Type</th>
                                <th>S.date</th>
                                <th>E.date</th>
                                <th>Reason</th>
                                <th>Days</th>
                                <th>HoD</th>
                                <th>HoD Status</th>
                                <th>MS</th>
                                <th>MS Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($leaveApplications as $lv)
                                <tr>
                                    <td>{{ $lv['type'] ?? '-' }}</td>
                                    <td>{{ $lv['start_date'] ?? '-' }}</td>
                                    <td>{{ $lv['end_date'] ?? '-' }}</td>
                                    <td>{{ $lv['reason'] ?? '-' }}</td>
                                    <td>{{ $lv['days'] ?? '-' }}</td>
                                    <td>{{ empty($lv['hod_status']) ? '' : 'HoD' }}</td>
                                    <td>
                                        @if (!empty($lv['hod_status']))
                                            <span
                                                class="leave-status-badge status-{{ strtolower($lv['hod_status']) }}">{{ ucfirst($lv['hod_status']) }}</span>
                                        @endif
                                    </td>
                                    <td>{{ empty($lv['ms_status']) ? '' : 'MS' }}</td>
                                    <td>
                                        @if (!empty($lv['ms_status']))
                                            <span
                                                class="leave-status-badge status-{{ strtolower($lv['ms_status']) }}">{{ ucfirst($lv['ms_status']) }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9">No leave records</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </main>

        <nav class="m-bottom-nav">
            <a class="m-nav-item" href="/dashboard"><span class="m-nav-icon">🏠</span><span
                    class="m-nav-label">Home</span></a>
            <a class="m-nav-item" href="{{ route('dashboard.attendance_summary') }}"><span
                    class="m-nav-icon">📊</span><span class="m-nav-label">Summary</span></a>
            <a class="m-nav-item active" href="{{ route('dashboard.leave_form') }}"><span
                    class="m-nav-icon">🧾</span><span class="m-nav-label">Leave</span></a>
            <a class="m-nav-item" href="{{ route('dashboard.tour') }}"><span class="m-nav-icon">⏰</span><span
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
        <script id="leave-balances-json" type="application/json">@json($leaveBalances)</script>
        <script>
            (function() {
                try {
                    const el = document.getElementById('leave-balances-json');
                    const leaveBalances = el ? JSON.parse(el.textContent || '{}') : {};
                    const sel = document.getElementById('m-leave-type-select');
                    const bal = document.getElementById('m-leave-balance');
                    if (sel && bal) {
                        const update = () => {
                            const id = parseInt(sel.value, 10);
                            bal.value = typeof leaveBalances[id] !== 'undefined' ? leaveBalances[id] : '-';
                        };
                        sel.addEventListener('change', update);
                        update();
                    }
                } catch (e) {
                    console.warn('m leaveBalances init failed', e);
                }
            })();
        </script>
        <script>
            (function() {
                const start = document.querySelector('input[name="from_date"]');
                const end = document.querySelector('input[name="to_date"]');

                function togglePlaceholder(input) {
                    try {
                        const wrapper = input.closest('.date-wrapper');
                        if (!wrapper) return;
                        const ph = wrapper.querySelector('.date-placeholder');
                        if (!ph) return;
                        if (input.value) ph.style.display = 'none';
                        else ph.style.display = 'block';
                    } catch (e) {
                        // ignore
                    }
                }

                [start, end].forEach(function(inp) {
                    if (!inp) return;
                    togglePlaceholder(inp);
                    inp.addEventListener('focus', function() {
                        togglePlaceholder(inp);
                    });
                    inp.addEventListener('blur', function() {
                        togglePlaceholder(inp);
                    });
                    inp.addEventListener('change', function() {
                        togglePlaceholder(inp);
                    });
                    inp.addEventListener('input', function() {
                        togglePlaceholder(inp);
                    });
                });
            })();
        </script>
    </div>
</body>

</html>
