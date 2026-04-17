<!-- resources/views/mobile/dashboard.blade.php -->
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Mobile Dashboard</title>
    <link rel="stylesheet" href="/css/mobile_dashboard.css?v=3">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Make injected attendance summary responsive and mobile-friendly */
        #m-summary-content .table-wrap {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            padding-bottom: 6px
        }

        #m-summary-content table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px
        }

        #m-summary-content th,
        #m-summary-content td {
            padding: 8px 6px;
            border: 1px solid #e6eef4;
            text-align: left;
            font-size: 13px;
            white-space: nowrap
        }

        #m-summary-content h2,
        #m-summary-content h3 {
            font-size: 18px;
            margin: 0 0 8px 0
        }

        @media (max-width:480px) {

            #m-summary-content th,
            #m-summary-content td {
                padding: 6px 8px;
                font-size: 12px
            }

            #m-summary-content table {
                font-size: 12px
            }
        }

        /* Mobile form styles for Leave / Tour / Adhoc */
        #m-forms-root .m-card {
            background: #fff;
            border-radius: 10px;
            padding: 12px;
            margin-top: 12px;
            box-shadow: 0 2px 10px rgba(12, 22, 34, 0.04)
        }

        #m-forms-root .leave-form .row-grid,
        #m-forms-root .leave-form .row-grid-3,
        #m-forms-root .leave-form .row-grid-4 {
            display: grid;
            gap: 10px;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr))
        }

        #m-forms-root .leave-form .row {
            margin-top: 10px
        }

        #m-forms-root .leave-form label {
            display: block;
            margin-bottom: 6px;
            font-weight: 600
        }

        #m-forms-root .leave-form .form-control,
        #m-forms-root .leave-form input,
        #m-forms-root .leave-form select,
        #m-forms-root .leave-form textarea {
            width: 100%;
            padding: 10px;
            border-radius: 8px;
            border: 1px solid #e6edf3;
            box-sizing: border-box;
            font-size: 14px
        }

        #m-forms-root .leave-form input[readonly] {
            background: #f7fafc
        }

        #m-forms-root .leave-form .btn {
            display: inline-block;
            background: #0b7a75;
            color: #fff;
            padding: 10px 14px;
            border-radius: 8px;
            border: none;
            font-weight: 600
        }

        #m-forms-root .leave-history .table-wrap {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch
        }

        /* reduce padding on very small screens */
        @media (max-width:420px) {

            #m-forms-root .leave-form .row-grid,
            #m-forms-root .leave-form .row-grid-3,
            #m-forms-root .leave-form .row-grid-4 {
                grid-template-columns: 1fr
            }

            #m-forms-root .leave-form .form-control {
                font-size: 13px;
                padding: 8px
            }

            #m-forms-root .m-card {
                padding: 10px
            }
        }
    </style>
</head>

<body>
    <div class="m-mobile-root">
        @php
            $fill = 0;
            if (!empty($attendanceToday['checkin']) && !empty($attendanceToday['checkout'])) {
                $fill = 100;
            } elseif (!empty($attendanceToday['checkin'])) {
                // show a half-width green fill when user has checked in but not checked out
                $fill = 50;
            }

            $nowTime = now('Asia/Thimphu')->format('H:i:s');
            $isNormal = in_array((int) ($departmentId ?? 0), [1, 2, 4, 5, 6], true);
            $showLateReason = $nowTime > '09:15:00' && $isNormal;
            $fillWidth = intval($fill ?? 0) . '%';

            // Precompute the check-in display string to avoid complex inline expressions
            $checkinDisplay = '';
            if (!empty($attendanceToday['checkin'])) {
                try {
                    $checkinDisplay = \Illuminate\Support\Carbon::parse(
                        $attendanceToday['checkin'],
                        'Asia/Thimphu',
                    )->format('h:i A');
                } catch (\Throwable $e) {
                    $checkinDisplay = '';
                }
            }

            // keep empty when missing (no placeholder)
            $checkinDisplay = $checkinDisplay ?: '';

            $checkoutDisplay = '';
            if (!empty($attendanceToday['checkout'])) {
                try {
                    $checkoutDisplay = \Illuminate\Support\Carbon::parse(
                        $attendanceToday['checkout'],
                        'Asia/Thimphu',
                    )->format('h:i A');
                } catch (\Throwable $e) {
                    $checkoutDisplay = '';
                }
            }

            $checkoutDisplay = $checkoutDisplay ?: '';
            $fillStyle = $fillWidth;
        @endphp

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
                <h3 class="m-card-title"><span class="m-welcome-prefix">Welcome,</span> <span
                        class="m-welcome-name">{{ $employee['employee_name'] ?? auth()->user()->name }}</span></h3>
                <div class="m-detail-box">
                    <div class="m-row">
                        <div class="m-row-label">Name:</div>
                        <div class="m-row-value">{{ $employee['employee_name'] ?? auth()->user()->name }}</div>
                    </div>
                    <div class="m-row">
                        <div class="m-row-label">EID:</div>
                        <div class="m-row-value">{{ $employee['eid'] ?? auth()->user()->email }}</div>
                    </div>
                    <div class="m-row">
                        <div class="m-row-label">Designation:</div>
                        <div class="m-row-value">{{ $employee['designation'] ?? 'Employee' }}</div>
                    </div>
                    <div class="m-row">
                        <div class="m-row-label">Department:</div>
                        <div class="m-row-value">{{ $employee['department_name'] ?? '-' }}</div>
                    </div>
                    <div class="m-row">
                        <div class="m-row-label">Role:</div>
                        <div class="m-row-value">{{ $employee['role_name'] ?? '-' }}</div>
                    </div>
                    <div class="m-row">
                        <div class="m-row-label">Status:</div>
                        <div class="m-row-value">{{ $employee['status'] ?? 'Active' }}</div>
                    </div>
                </div>
            </div>

            <div class="m-card m-att-row">
                <a id="m-checkin-btn" href="/dashboard/attendance/checkin" class="m-btn m-btn-primary"
                    aria-label="Check In">
                    <span class="m-btn-badge"><span class="m-btn-icon-inner">
                            @if ($hasMorning)
                                ✓
                            @else
                                ⏱️
                            @endif
                        </span></span>
                    <span class="m-btn-label">
                        @if ($hasMorning)
                            Checked In
                        @else
                            Check In
                        @endif
                    </span>
                </a>

                <a id="m-checkout-btn" href="/dashboard/attendance/checkout" class="m-btn m-btn-secondary"
                    aria-label="Check Out">
                    <span class="m-btn-badge"><span class="m-btn-icon-inner">✕</span></span>
                    <span class="m-btn-label">Check Out</span>
                </a>
            </div>

            <!-- Checked-in summary card removed as requested -->

            <!-- Desktop-like Timeline card -->
            <div class="m-card">
                <h4 class="m-card-title">Today's Timeline</h4>
                <div class="m-timeline-row">
                    <div class="m-timeline-time">{{ $checkinDisplay }}</div>
                    @if (empty($attendanceToday['checkin']) && empty($attendanceToday['checkout']))
                        <div class="m-timeline-edge" aria-hidden="true"></div>
                    @endif
                    <div class="m-timeline-bar">
                        @if (!empty($attendanceToday['checkin']))
                            <div class="m-timeline-fill"></div>
                            <!-- blade: end timeline fill -->
                        @endif
                    </div>
                    @if (empty($attendanceToday['checkin']) && empty($attendanceToday['checkout']))
                        <div class="m-timeline-edge" aria-hidden="true"></div>
                    @endif
                    <div class="m-timeline-time m-timeline-now">{{ $checkoutDisplay }}</div>
                </div>
            </div>

            <!-- Late reason card (desktop-style) -->
            <div class="m-card">
                @if ($showLateReason)
                    <label for="late-reason-area" style="display:block;margin-bottom:6px;font-weight:600;">Late reason
                        (required after 09:15)</label>
                    <textarea id="late-reason-area" placeholder="Provide reason for being late"
                        style="width:100%;min-height:76px;padding:10px;border-radius:8px;border:1px solid #e6edf3;"></textarea>
                @endif
            </div>

            <!-- Reminder card -->
            @if (!empty($notifications) && is_array($notifications))
                <div class="m-card">
                    <div class="m-reminder">{{ implode(' | ', $notifications) }}</div>
                </div>
            @endif

            <!-- Hidden mobile forms (Summary / Leave / Tour / Adhoc) -->
            <div id="m-forms-root">
                <div id="m-summary-card" class="m-card" style="display:none">
                    <h4 class="m-card-title">Attendance Summary</h4>
                    <div id="m-summary-content">Loading...</div>
                </div>

                <div id="m-leave-card" class="m-card" style="display:none">
                    <h4 class="m-card-title">Leave</h4>
                    <form method="post" action="{{ route('dashboard.leave', [], false) }}"
                        class="leave-form space-y-3">
                        @csrf
                        <div class="row-grid-3">
                            <div class="col">
                                <label>Type</label>
                                <select name="leave_type_id" id="m-leave-type-select"
                                    class="form-control px-3 py-2 rounded-md border border-gray-200">
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
                                <select name="submit_to"
                                    class="form-control px-3 py-2 rounded-md border border-gray-200">
                                    <option value="hod">HoD</option>
                                    <option value="ms">Medical Superintendent</option>
                                </select>
                            </div>
                            <div class="col">
                                <label>Balance</label>
                                <input type="text" id="m-leave-balance" readonly
                                    class="form-control px-3 py-2 rounded-md border border-gray-200 bg-gray-50">
                            </div>

                            <div class="col">
                                <label>Start</label>
                                <input type="date" name="from_date" required
                                    min="{{ now('Asia/Thimphu')->toDateString() }}"
                                    class="form-control px-3 py-2 rounded-md border border-gray-200">
                            </div>
                            <div class="col">
                                <label>End</label>
                                <input type="date" name="to_date" required
                                    min="{{ now('Asia/Thimphu')->toDateString() }}"
                                    class="form-control px-3 py-2 rounded-md border border-gray-200">
                            </div>
                            <div class="col">
                                <label>Total Days</label>
                                <input name="total_days" type="number" step="0.5" min="0.5"
                                    placeholder="e.g. 1 or 0.5"
                                    class="form-control px-3 py-2 rounded-md border border-gray-200">
                            </div>
                        </div>

                        <div class="row">
                            <label>Reason</label>
                            <input name="reason" placeholder="Reason" required
                                class="form-control px-3 py-2 rounded-md border border-gray-200">
                        </div>

                        <div class="row">
                            <button class="btn bg-blue-700 text-white px-4 py-2 rounded-md font-semibold"
                                type="submit">Submit</button>
                        </div>
                    </form>
                </div>

                <div id="m-tour-card" class="m-card" style="display:none">
                    <h4 class="m-card-title">Tour</h4>
                    <form method="POST" action="{{ route('dashboard.tour.store', [], false) }}"
                        class="leave-form space-y-3" style="margin-top:10px;" enctype="multipart/form-data">
                        @csrf
                        <div class="row-grid-4">
                            <div class="col">
                                <label>Place</label>
                                <input type="text" name="place" placeholder="Tour place" required
                                    class="form-control px-3 py-2 rounded-md border border-gray-200">
                            </div>
                            <div class="col">
                                <label>Start</label>
                                <input type="date" name="start_date" required
                                    min="{{ now('Asia/Thimphu')->toDateString() }}"
                                    class="form-control px-3 py-2 rounded-md border border-gray-200">
                            </div>
                            <div class="col">
                                <label>End</label>
                                <input type="date" name="end_date" required
                                    min="{{ now('Asia/Thimphu')->toDateString() }}"
                                    class="form-control px-3 py-2 rounded-md border border-gray-200">
                            </div>
                            <div class="col">
                                <label>Total Days</label>
                                <input type="text" id="m-tour-total-days" value="-" readonly
                                    class="form-control px-3 py-2 rounded-md border border-gray-200 bg-gray-50">
                            </div>
                        </div>
                        <div class="row">
                            <label>Purpose</label>
                            <input type="text" name="purpose" placeholder="Purpose of tour" required
                                class="form-control px-3 py-2 rounded-md border border-gray-200">
                        </div>
                        <div class="row">
                            <label>Office Order (PDF)</label>
                            <input type="file" name="office_order_pdf" accept="application/pdf"
                                class="form-control">
                        </div>
                        <div class="row">
                            <button class="btn bg-blue-700 text-white px-4 py-2 rounded-md font-semibold"
                                type="submit">Save Tour Record</button>
                        </div>
                    </form>
                </div>

                <div id="m-adhoc-card" class="m-card" style="display:none">
                    <h4 class="m-card-title">Adhoc Request</h4>
                    <form method="POST" action="{{ route('dashboard.adhoc_requests.store', [], false) }}"
                        class="leave-form space-y-3" style="margin-bottom:18px;">
                        @csrf
                        <div class="row-grid">
                            <div class="col">
                                <label>Date</label>
                                <input type="date" name="date" required
                                    class="form-control px-3 py-2 rounded-md border border-gray-200"
                                    placeholder="mm/dd/yyyy">
                            </div>
                            <div class="col">
                                <label>Purpose</label>
                                <select name="purpose" required
                                    class="form-control px-3 py-2 rounded-md border border-gray-200">
                                    <option value="meeting">Meeting</option>
                                    <option value="emergency">Emergency</option>
                                </select>
                            </div>
                        </div>

                        <div class="row" style="margin-top:12px">
                            <label>Remarks</label>
                            <input type="text" name="remarks" maxlength="255"
                                class="form-control px-3 py-2 rounded-md border border-gray-200">
                        </div>

                        <div style="margin-top:12px">
                            <button type="submit"
                                class="btn bg-blue-700 text-white px-4 py-2 rounded-md font-semibold">Submit Adhoc
                                Request</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Timeline removed as requested -->
        </main>

        <nav class="m-bottom-nav">
            <a class="m-nav-item active" href="/dashboard"><span class="m-nav-icon">🏠</span><span
                    class="m-nav-label">Home</span></a>
            <a class="m-nav-item" href="{{ route('dashboard.attendance_summary') }}"><span
                    class="m-nav-icon">📊</span><span class="m-nav-label">Summary</span></a>
            <a class="m-nav-item" href="{{ route('dashboard.leave_form') }}"><span class="m-nav-icon">🧾</span><span
                    class="m-nav-label">Leave</span></a>
            <a class="m-nav-item" href="{{ route('dashboard.tour') }}"><span class="m-nav-icon">⏰</span><span
                    class="m-nav-label">Tour</span></a>
            <a class="m-nav-item" href="{{ route('dashboard.adhoc_requests') }}"><span
                    class="m-nav-icon">👤</span><span class="m-nav-label">Adhoc</span></a>
        </nav>

        <script id="m-leave-balances-json" type="application/json">@json($leaveBalances ?? [])</script>

        <script>
            (function() {
                // Initialize leave balances for mobile leave form (no nav interception)
                try {
                    const el = document.getElementById('m-leave-balances-json');
                    const leaveBalances = el ? JSON.parse(el.textContent || '{}') : {};
                    const sel = document.getElementById('m-leave-type-select');
                    const bal = document.getElementById('m-leave-balance');
                    if (sel && bal) {
                        const update = function() {
                            const id = parseInt(sel.value, 10);
                            bal.value = typeof leaveBalances[id] !== 'undefined' ? leaveBalances[id] : '-';
                        };
                        sel.addEventListener('change', update);
                        update();
                    }
                } catch (e) {
                    console.warn('mobile leave-balances init failed', e);
                }

                // tour total days calc
                (function() {
                    const start = document.querySelector('#m-tour-card input[name="start_date"]');
                    const end = document.querySelector('#m-tour-card input[name="end_date"]');
                    const total = document.getElementById('m-tour-total-days');

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
            }());
        </script>
        <script>
            (function() {
                const checkin = document.getElementById('m-checkin-btn');
                const checkout = document.getElementById('m-checkout-btn');
                const lateInput = document.getElementById('late-reason-input') || document.getElementById(
                    'late-reason-area');
                const showLate = @json($showLateReason);

                function navigateWithLocation(anchor, opts = {}) {
                    if (!anchor) return;
                    anchor.addEventListener('click', function(e) {
                        e.preventDefault();

                        if (opts.requireLate) {
                            const reason = (lateInput && lateInput.value || '').trim();
                            if (!reason) {
                                alert('Please enter a reason for being late before checking in.');
                                lateInput && lateInput.focus();
                                return;
                            }
                        }

                        if (!navigator.geolocation) {
                            alert(
                                'Location permission is required to record address. Please use a device/browser with geolocation support.'
                            );
                            return;
                        }

                        navigator.geolocation.getCurrentPosition(function(pos) {
                            try {
                                const url = new URL(anchor.href, window.location.origin);
                                url.searchParams.set('lat', pos.coords.latitude);
                                url.searchParams.set('lon', pos.coords.longitude);
                                if (opts.includeLate && lateInput && lateInput.value.trim() !== '') {
                                    url.searchParams.set('late_reason', lateInput.value.trim());
                                }
                                window.location.href = url.toString();
                            } catch (err) {
                                // fallback: build query string manually
                                let href = anchor.href;
                                const sep = href.indexOf('?') === -1 ? '?' : '&';
                                href += sep + 'lat=' + encodeURIComponent(pos.coords.latitude) + '&lon=' +
                                    encodeURIComponent(pos.coords.longitude);
                                if (opts.includeLate && lateInput && lateInput.value.trim() !== '') {
                                    href += '&late_reason=' + encodeURIComponent(lateInput.value.trim());
                                }
                                window.location.href = href;
                            }
                        }, function(err) {
                            alert(
                                'Location permission is required to record address. Please allow location access and try again.'
                            );
                        }, {
                            timeout: 20000,
                            enableHighAccuracy: false,
                            maximumAge: 30000
                        });
                    });
                }

                // Apply handlers
                navigateWithLocation(checkin, {
                    requireLate: showLate,
                    includeLate: true
                });
                navigateWithLocation(checkout, {
                    requireLate: false,
                    includeLate: false
                });
            }());
        </script>
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

                    // Close dropdown when clicking outside
                    document.addEventListener('click', function(e) {
                        if (!menuDropdown.contains(e.target) && !menuBtn.contains(e.target)) {
                            menuDropdown.classList.remove('open');
                            menuDropdown.setAttribute('aria-hidden', 'true');
                        }
                    });

                    // Intercept logout to use fetch() instead of native form submit
                    const logoutBtn = document.getElementById('m-logout-btn');
                    const logoutForm = document.getElementById('m-logout-form');
                    if (logoutBtn && logoutForm) {
                        logoutBtn.addEventListener('click', function(ev) {
                            ev.preventDefault();
                            const action = logoutForm.getAttribute('action');
                            let tokenInput = logoutForm.querySelector('input[name="_token"]');
                            let token = tokenInput ? tokenInput.value : null;
                            if (!token) {
                                // fallback to meta tag
                                const meta = document.querySelector('meta[name="csrf-token"]');
                                if (meta) token = meta.getAttribute('content');
                            }

                            // Send POST via fetch to avoid browser "insecure form" prompt.
                            // Use a same-origin path to avoid mixed-scheme blocking when
                            // route() emits an absolute URL with a different scheme.
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
                                    // Always refresh the page so the UI reflects the
                                    // server state — some servers send non-OK responses
                                    // but still clear the session; reloading shows the
                                    // actual login state without a misleading "failed" alert.
                                    if (!res.ok) {
                                        console.warn('Logout returned non-OK status', res.status);
                                    }
                                    window.location.reload();
                                }).catch(function(err) {
                                    console.error('Fetch error during logout', err);
                                    // Still reload to pick up possible server-side logout
                                    // that completed despite the network error.
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
            // Apply dynamic widths for timeline fills (avoid inline CSS Blade parsing issues)
            document.addEventListener('DOMContentLoaded', function() {
                var widthVal = @json($fillStyle) || '0%';
                document.querySelectorAll('.m-timeline-fill').forEach(function(el) {
                    el.style.width = widthVal;
                });
            });
        </script>
    </div>
</body>

</html>
