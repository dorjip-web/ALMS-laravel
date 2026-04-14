<!-- resources/views/mobile/dashboard.blade.php -->
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Mobile Dashboard</title>
    <link rel="stylesheet" href="/css/mobile_dashboard.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
</head>
<body>
<div class="m-mobile-root">
    @php
        $fill = 0;
        if (! empty($attendanceToday['checkin']) && ! empty($attendanceToday['checkout'])) {
            $fill = 100;
        } elseif (! empty($attendanceToday['checkin'])) {
            // show a half-width green fill when user has checked in but not checked out
            $fill = 50;
        }

        $nowTime = now('Asia/Thimphu')->format('H:i:s');
        $isNormal = in_array((int) ($departmentId ?? 0), [1,2,4,5,6], true);
        $showLateReason = ($nowTime > '09:15:00' && $isNormal);
        $fillWidth = intval($fill ?? 0) . '%';

        // Precompute the check-in display string to avoid complex inline expressions
        $checkinDisplay = '';
        if (! empty($attendanceToday['checkin'])) {
            try {
                $checkinDisplay = \Illuminate\Support\Carbon::parse($attendanceToday['checkin'], 'Asia/Thimphu')->format('h:i A');
            } catch (\Throwable $e) {
                $checkinDisplay = '';
            }
        }

        // keep empty when missing (no placeholder)
        $checkinDisplay = $checkinDisplay ?: '';

        $checkoutDisplay = '';
        if (! empty($attendanceToday['checkout'])) {
            try {
                $checkoutDisplay = \Illuminate\Support\Carbon::parse($attendanceToday['checkout'], 'Asia/Thimphu')->format('h:i A');
            } catch (\Throwable $e) {
                $checkoutDisplay = '';
            }
        }

        $checkoutDisplay = $checkoutDisplay ?: '';
        $fillStyle = $fillWidth;
    @endphp

    <header class="m-header">
        <button class="m-menu-btn" aria-label="menu">☰</button>
        <div class="m-header-center">
            <div class="m-logo-text m-logo-left">NTMH</div>
            <img class="m-logo-img" src="/images/ntmh-logo.png" alt="logo" />
            <div class="m-logo-text m-logo-right">ALMS</div>
        </div>
        <div style="width:36px"></div>
    </header>

    <main class="m-main">
        <div class="m-card">
            <h3 class="m-card-title"><span class="m-welcome-prefix">Welcome,</span> <span class="m-welcome-name">{{ $employee['employee_name'] ?? auth()->user()->name }}</span></h3>
            <div class="m-detail-box">
                <div class="m-row"><div class="m-row-label">Name:</div><div class="m-row-value">{{ $employee['employee_name'] ?? auth()->user()->name }}</div></div>
                <div class="m-row"><div class="m-row-label">EID:</div><div class="m-row-value">{{ $employee['eid'] ?? auth()->user()->email }}</div></div>
                <div class="m-row"><div class="m-row-label">Designation:</div><div class="m-row-value">{{ $employee['designation'] ?? 'Employee' }}</div></div>
                <div class="m-row"><div class="m-row-label">Department:</div><div class="m-row-value">{{ $employee['department_name'] ?? '-' }}</div></div>
                <div class="m-row"><div class="m-row-label">Role:</div><div class="m-row-value">{{ $employee['role_name'] ?? '-' }}</div></div>
                <div class="m-row"><div class="m-row-label">Status:</div><div class="m-row-value">{{ $employee['status'] ?? 'Active' }}</div></div>
            </div>
        </div>

        <div class="m-card m-att-row">
            <a id="m-checkin-btn" href="/dashboard/attendance/checkin" class="m-btn m-btn-primary" aria-label="Check In">
                <span class="m-btn-badge"><span class="m-btn-icon-inner">@if($hasMorning) ✓ @else ⏱️ @endif</span></span>
                <span class="m-btn-label">@if($hasMorning) Checked In @else Check In @endif</span>
            </a>

            <a id="m-checkout-btn" href="/dashboard/attendance/checkout" class="m-btn m-btn-secondary" aria-label="Check Out">
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
                        <div class="m-timeline-fill" style="width: {{ $fillStyle }}; background: var(--green) !important; height: 100% !important; border-radius: 999px !important; box-shadow: inset 0 -2px 0 rgba(0,0,0,0.06), 0 6px 14px rgba(20,184,102,0.06) !important; transition: width .36s ease !important;"></div>
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
                <label for="late-reason-area" style="display:block;margin-bottom:6px;font-weight:600;">Late reason (required after 09:15)</label>
                <textarea id="late-reason-area" placeholder="Provide reason for being late" style="width:100%;min-height:76px;padding:10px;border-radius:8px;border:1px solid #e6edf3;"></textarea>
            @endif
        </div>

        <!-- Reminder card -->
        @if (!empty($notifications) && is_array($notifications))
            <div class="m-card">
                <div class="m-reminder">{{ implode(' | ', $notifications) }}</div>
            </div>
        @endif

        <!-- Timeline removed as requested -->
    </main>

    <nav class="m-bottom-nav">
        <a class="m-nav-item active" href="/dashboard"><span class="m-nav-icon">🏠</span><span class="m-nav-label">Home</span></a>
        <a class="m-nav-item" href="/attendance"><span class="m-nav-icon">⏰</span><span class="m-nav-label">Attendance</span></a>
        <a class="m-nav-item" href="/summary"><span class="m-nav-icon">📊</span><span class="m-nav-label">Summary</span></a>
        <a class="m-nav-item" href="/leave"><span class="m-nav-icon">🧾</span><span class="m-nav-label">Leave</span></a>
        <a class="m-nav-item" href="/profile"><span class="m-nav-icon">👤</span><span class="m-nav-label">Profile</span></a>
    </nav>
    <script>
        (function () {
            const checkin = document.getElementById('m-checkin-btn');
            const checkout = document.getElementById('m-checkout-btn');
            const lateInput = document.getElementById('late-reason-input') || document.getElementById('late-reason-area');
            const showLate = <?php echo json_encode($showLateReason); ?>;

            function navigateWithLocation(anchor, opts = {}) {
                if (!anchor) return;
                anchor.addEventListener('click', function (e) {
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
                        alert('Location permission is required to record address. Please use a device/browser with geolocation support.');
                        return;
                    }

                    navigator.geolocation.getCurrentPosition(function (pos) {
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
                            href += sep + 'lat=' + encodeURIComponent(pos.coords.latitude) + '&lon=' + encodeURIComponent(pos.coords.longitude);
                            if (opts.includeLate && lateInput && lateInput.value.trim() !== '') {
                                href += '&late_reason=' + encodeURIComponent(lateInput.value.trim());
                            }
                            window.location.href = href;
                        }
                    }, function (err) {
                        alert('Location permission is required to record address. Please allow location access and try again.');
                    }, { timeout: 20000, enableHighAccuracy: false, maximumAge: 30000 });
                });
            }

            // Apply handlers
            navigateWithLocation(checkin, { requireLate: showLate, includeLate: true });
            navigateWithLocation(checkout, { requireLate: false, includeLate: false });
        }());
    </script>
</div>
</body>
</html>
