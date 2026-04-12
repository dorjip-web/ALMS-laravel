<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Employee Dashboard</title>
    <link rel="stylesheet" href="/css/dashboard.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
</head>
<body>
@php
    $fullName = $employee['employee_name'] ?? auth()->user()->name;
    $parts = preg_split('/\s+/', trim($fullName));
    $initials = count($parts) > 1
        ? strtoupper(substr($parts[0], 0, 1) . substr($parts[1], 0, 1))
        : strtoupper(substr($parts[0] ?? 'U', 0, 2));

    $checkinDisp = !empty($attendanceToday['checkin']) ? \Illuminate\Support\Carbon::parse($attendanceToday['checkin'], 'Asia/Thimphu')->format('h:i A') : '—';
    $checkoutDisp = !empty($attendanceToday['checkout']) ? \Illuminate\Support\Carbon::parse($attendanceToday['checkout'], 'Asia/Thimphu')->format('h:i A') : '—';

    $fill = 0;
    if (!empty($attendanceToday['checkin']) && !empty($attendanceToday['checkout'])) {
        $fill = 100;
    } elseif (!empty($attendanceToday['checkin'])) {
        $fill = 60;
    }

    $nowTime = now('Asia/Thimphu')->format('H:i:s');
    $isNormal = in_array((int)$departmentId, [1,2,4,5,6], true);
    // Check-in is enabled for everyone all day
    $checkinClosed = false;
    $showLateReason = ($nowTime > '09:15:00' && $isNormal);
    $checkoutLockedMessage = 'Checkout allowed after 2:55 PM.';
    $showCheckoutLockedMessage = session('flash_error') === $checkoutLockedMessage;
@endphp

<div class="app">
    @include('partials.sidebar')

    <main class="main">
        <header class="topbar">
            <div class="search"><input placeholder="Search..."></div>
            <div class="logout">
                <form method="POST" action="/logout" style="display:inline;">
                    @csrf
                    <button type="submit" style="background:#fff;padding:8px 12px;border-radius:6px;border:none;color:var(--orange);font-weight:600;cursor:pointer;">Logout</button>
                </form>
            </div>
        </header>

        @if (session('flash_error') && ! $showCheckoutLockedMessage)
            <div class="flash flash-error">{{ session('flash_error') }}</div>
        @endif

        <section class="grid">
            <div id="employee" class="card employee-card">
                <h2><span class="welcome-orange">Welcome,</span> <span class="name-blue">{{ $fullName }}</span></h2>
                <h3>Employee Details</h3>
                <table class="details">
                    <tbody>
                        <tr><td>Name:</td><td>{{ $employee['employee_name'] ?? $fullName }}</td></tr>
                        <tr><td>EID:</td><td>{{ $employee['eid'] ?? auth()->user()->email }}</td></tr>
                        <tr><td>Designation:</td><td>{{ $employee['designation'] ?? 'Employee' }}</td></tr>
                        <tr><td>Department:</td><td>{{ $employee['department_name'] ?? '-' }}</td></tr>
                        <tr><td>Role:</td><td>{{ $employee['role_name'] ?? '-' }}</td></tr>
                        <tr><td>Status:</td><td><span class="status-active">{{ $employee['status'] ?? 'active' }}</span></td></tr>
                    </tbody>
                </table>
            </div>

            <div id="attendance" class="card attendance-card">
                <h3>Attendance</h3>
                <p class="attendance-subtitle">Track your daily activity</p>

                <div class="att-actions">
                    <form method="POST" action="/dashboard/attendance" class="inline attendance-form" data-action="checkin">
                        @csrf
                        <input type="hidden" name="action" value="checkin">
                        <input type="hidden" name="lat" class="lat-input">
                        <input type="hidden" name="lon" class="lon-input">
                        <input type="hidden" name="late_reason" class="late-reason-hidden">
                        <button type="submit" class="btn" {{ $hasMorning ? 'disabled' : '' }}>
                            @if ($hasMorning)
                                Checked-in
                            @else
                                Check-in
                            @endif
                        </button>
                    </form>

                    <form method="POST" action="/dashboard/attendance" class="inline attendance-form" data-action="checkout">
                        @csrf
                        <input type="hidden" name="action" value="checkout">
                        <input type="hidden" name="lat" class="lat-input">
                        <input type="hidden" name="lon" class="lon-input">
                        <button type="submit" class="btn checkout{{ $showCheckoutLockedMessage ? ' checkout-locked' : '' }}" {{ (!$hasMorning || $hasEvening) ? 'disabled' : '' }}>
                            {{ $showCheckoutLockedMessage ? $checkoutLockedMessage : 'Check-out' }}
                        </button>
                    </form>
                </div>

                <div class="status-box">
                    <div class="icon">{{ $fill === 100 ? '✓' : '•' }}</div>
                    <div>
                        <div class="status-title">{{ $fill === 100 ? 'Completed' : ($fill > 0 ? 'Checked-in' : 'Not Checked') }}</div>
                        <div class="time-line">Check-in: {{ $checkinDisp }}</div>
                        <div class="time-line">Check-out: {{ $checkoutDisp }}</div>
                    </div>
                </div>

                <div class="timeline">
                    <div class="title">Today's Timeline</div>
                    <div class="timeline-row">
                        <div class="timeline-time">{{ $checkinDisp }}</div>
                        <div class="bar">
                            <div class="fill" data-fill="{{ (int) $fill }}"></div>
                        </div>
                        <div class="timeline-time timeline-time-end">{{ $checkoutDisp }}</div>
                    </div>
                </div>

                <div class="late-box" style="margin-top:18px;">
                    @if ($showLateReason)
                        <label for="late-reason-area" style="display:block;margin-bottom:6px;font-weight:600;">Late reason (required after 09:15)</label>
                        <textarea id="late-reason-area" placeholder="Provide reason for being late" style="width:100%;min-height:76px;padding:10px;border-radius:8px;border:1px solid #e6edf3;"></textarea>
                    @endif
                </div>

                @if (!empty($notifications))
                    <div class="reminder">{{ implode(' | ', $notifications) }}</div>
                @endif

                
            </div>

            <!-- Leave moved to separate page: /dashboard/leave -->
        </section>
    </main>
</div>

<script>
    // expose server-side flag to client
    window.__SHOW_LATE_REASON = <?php echo $showLateReason ? 'true' : 'false'; ?>;
    let leaveBalances = {};

    document.addEventListener('DOMContentLoaded', function() {
        const el = document.getElementById('leave-balances-json');
        if (el) {
            leaveBalances = JSON.parse(el.textContent);
        }
    });

    (function () {
        const links = document.querySelectorAll('.menu a');
        function setActiveByHash() {
            const hash = location.hash || '#employee';
            links.forEach(a => {
                const href = (a.getAttribute('href') || '').toString();
                // compute fragment part of link (if any)
                const frag = href.includes('#') ? '#' + href.split('#').pop() : null;
                // mark active if fragment equals current hash, or if the href is exactly the hash
                if (frag === hash || href === hash) {
                    a.classList.add('active');
                } else {
                    a.classList.remove('active');
                }
            });
        }
        links.forEach(a => a.addEventListener('click', () => {
            links.forEach(x => x.classList.remove('active'));
            a.classList.add('active');
        }));
        window.addEventListener('hashchange', setActiveByHash);
        setActiveByHash();
    })();
    // Initialization for dynamic grid features moved into initGridFeatures()
    // so injected HTML won't get duplicate handlers. initGridFeatures()
    // is called on page load and after AJAX injections.
</script>
<script id="leave-balances-json" type="application/json">@json($leaveBalances)</script>
<!-- profile pic change handled by initGridFeatures -->
<script>
    // Load attendance summary (and other ajax links) into current dashboard main area without hiding sidebar
    (function () {
        function initGridFeatures() {
            // timeline fills
            document.querySelectorAll('.timeline .fill').forEach(el => el.style.width = (el.dataset.fill || '0') + '%');

            // idempotent bind for attendance forms
            document.querySelectorAll('.attendance-form').forEach(form => {
                if (form.dataset.bound === '1') return;
                form.dataset.bound = '1';
                form.addEventListener('submit', function (event) {
                    event.preventDefault();
                    const action = form.dataset.action;
                    if (action === 'checkin' && window.__SHOW_LATE_REASON) {
                        const lateArea = document.getElementById('late-reason-area');
                        if (lateArea && lateArea.value.trim() === '') {
                            alert('Please provide a late reason before checking in.');
                            return;
                        }
                    }
                    if (!navigator.geolocation) {
                        alert('Geolocation is not supported in this browser. Please use a modern browser.');
                        return;
                    }
                    const latInput = form.querySelector('.lat-input');
                    const lonInput = form.querySelector('.lon-input');
                    navigator.geolocation.getCurrentPosition(function (pos) {
                        const lat = pos.coords.latitude;
                        const lon = pos.coords.longitude;
                        const hiddenLate = form.querySelector('.late-reason-hidden');
                        console.log('DEBUG attendance submit', { action: form.action, origin: window.location.origin, actionProtocol: new URL(form.action, window.location.href).protocol });
                        try { const lateArea = document.getElementById('late-reason-area'); if (lateArea && hiddenLate) hiddenLate.value = lateArea.value.trim(); } catch (e) {}
                        latInput.value = lat; lonInput.value = lon; form.submit();
                    }, function () { alert('Location permission is required.'); }, { timeout: 60000, enableHighAccuracy: false, maximumAge: 30000 });
                });
            });

            // leave selector
            const sel = document.getElementById('leave-type-select');
            const bal = document.getElementById('leave-balance');
            if (sel && bal && sel.dataset.bound !== '1') {
                sel.dataset.bound = '1';
                const update = () => { const id = parseInt(sel.value, 10); bal.value = typeof leaveBalances[id] !== 'undefined' ? leaveBalances[id] : '-'; };
                sel.addEventListener('change', update); update();
            }

            // profile pic input
            const pic = document.getElementById('profilePicInput');
            if (pic && pic.dataset.bound !== '1') { pic.dataset.bound = '1'; pic.addEventListener('change', function () { if (this.files && this.files[0]) document.getElementById('profilePicForm').submit(); }); }
        }

        async function loadAndInject(url, pushState = true) {
            try {
                const res = await fetch(url, { credentials: 'same-origin', headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                if (!res.ok) throw new Error('Network response not ok');
                const text = await res.text();
                const parser = new DOMParser();
                const doc = parser.parseFromString(text, 'text/html');
                const newGrid = doc.querySelector('section.grid');
                const currGrid = document.querySelector('section.grid');
                if (newGrid && currGrid) {
                    // update leaveBalances if provided in the fetched document
                    try {
                        const remoteBalances = doc.getElementById('leave-balances-json');
                        if (remoteBalances) {
                            const parsed = JSON.parse(remoteBalances.textContent || '{}');
                            leaveBalances = parsed;
                            const localEl = document.getElementById('leave-balances-json');
                            if (localEl) localEl.textContent = remoteBalances.textContent;
                        }
                    } catch (e) { console.warn('Failed to update leaveBalances from remote doc', e); }

                    currGrid.innerHTML = newGrid.innerHTML;
                    initGridFeatures();
                    // if URL had a fragment, set it so section becomes visible
                    try {
                        const frag = url.split('#')[1];
                        if (frag) {
                            location.hash = frag;
                            const el = document.getElementById(frag);
                            if (el) el.scrollIntoView();
                        } else {
                            window.scrollTo(0,0);
                        }
                    } catch (e) { window.scrollTo(0,0); }

                    if (pushState) history.pushState({ ajax: true }, '', url);
                } else {
                    window.location.href = url;
                }
            } catch (e) { console.error(e); window.location.href = url; }
        }

        const DASHBOARD_URL = "{{ route('dashboard') }}";

        // intercept sidebar ajax-links or anchors pointing to attendance summary or hashes
        document.addEventListener('click', function (ev) {
            const a = ev.target.closest && ev.target.closest('.menu a');
            if (!a) return;
            const href = (a.getAttribute('href') || '').toString();

            // if it's a hash link (eg. #attendance), load the dashboard then set the hash
            if (href.startsWith('#')) {
                ev.preventDefault();
                document.querySelectorAll('.menu a').forEach(x => x.classList.remove('active'));
                a.classList.add('active');
                loadAndInject(DASHBOARD_URL + href, true);
                return;
            }

            // allow interception if anchor marked ajax-link or its href references attendance_summary route
            const shouldAjax = a.classList.contains('ajax-link') || href.includes('attendance_summary');
            if (!shouldAjax) return;
            ev.preventDefault();
            document.querySelectorAll('.menu a').forEach(x => x.classList.remove('active'));
            a.classList.add('active');
            console.debug('[ALMS] AJAX nav click:', href);
            loadAndInject(a.href, true);
        });

        // handle back/forward
        window.addEventListener('popstate', function (ev) { loadAndInject(location.href, false); });

        // init on page load
        document.addEventListener('DOMContentLoaded', initGridFeatures);
    })();
</script>
</body>
</html>