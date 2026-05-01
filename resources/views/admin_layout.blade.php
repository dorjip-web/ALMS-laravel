<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>@yield('pageTitle', 'Admin') — NTMH</title>
    <link rel="stylesheet" href="/css/admin_dashboard.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
</head>
<body>
@php
    $adminLogo = null;
    foreach (['images/ntmh-logo.png', 'images/ntmh-logo.png.jpg', 'images/ntmh-logo.jpg'] as $candidate) {
        if (file_exists(public_path($candidate))) {
            $adminLogo = asset($candidate);
            break;
        }
    }
@endphp
<div class="app">
    <aside class="sidebar">
        @include('partials.sidebar_profile')
        <nav class="menu">
            <a href="{{ route('admin.dashboard') }}" @class(['active' => ($activeNav ?? '') === 'dashboard'])>Admin Dashboard</a>
            <a href="{{ route('admin.users.index') }}" @class(['active' => ($activeNav ?? '') === 'users'])>User Management</a>
            <a href="{{ route('admin.roles_permissions') }}" @class(['active' => ($activeNav ?? '') === 'roles_permissions'])>Roles &amp; Permissions</a>
            <a href="{{ route('admin.departments_hods.index') }}" @class(['active' => ($activeNav ?? '') === 'departments_hods'])>Department &amp; HoD Management</a>
            <a href="{{ route('admin.leave_balances.index') }}" @class(['active' => ($activeNav ?? '') === 'leave_balances'])>Leave Balance</a>
            <a href="{{ route('admin.on_tour') }}" @class(['active' => ($activeNav ?? '') === 'staff_on_tour'])>Staff on Tour</a>
            <a href="{{ route('admin.adhoc') }}" @class(['active' => ($activeNav ?? '') === 'adhoc_requests'])>Adhoc Requests</a>
            <a href="{{ url('/admin/device-bindings') }}" @class(['active' => ($activeNav ?? '') === 'device_bindings'])>Device Binding</a>
            <a href="{{ route('admin.leave_types.index') }}" @class(['active' => ($activeNav ?? '') === 'leave_types'])>Leave Management</a>
            <a href="{{ route('admin.attendance_logs.index') }}" @class(['active' => ($activeNav ?? '') === 'attendance_logs'])>Attendance Logs</a>
            <a href="{{ route('admin.leave_records.index') }}" @class(['active' => ($activeNav ?? '') === 'leave_records'])>Leave Records</a>
            {{-- Reports link removed per request --}}
            <a href="{{ route('admin.settings.index') }}" @class(['active' => ($activeNav ?? '') === 'settings'])>Settings</a>
        </nav>
    </aside>

    <main class="main">
        <header class="topbar">
            <div class="search"><input placeholder="Search..."></div>
            <div class="logout">
                <form method="POST" action="{{ route('admin.logout') }}" style="display:inline;">
                    @csrf
                    <button type="submit">Logout</button>
                </form>
            </div>
        </header>

        <section>
            @yield('content')
        </section>
    </main>
</div>

@yield('scripts')

<script>
(() => {
    const links = document.querySelectorAll('.sidebar .menu a');
    function normalize(path) { return path.replace(/\/+$/,''); }
    const loc = normalize(location.pathname || '/');
    links.forEach(a => {
        const href = a.getAttribute('href') || '';
        try {
            const p = new URL(href, location.origin).pathname;
            if (normalize(p) === loc || (loc.startsWith(normalize(p)) && normalize(p) !== '')) {
                a.classList.add('active');
            } else {
                a.classList.remove('active');
            }
        } catch (e) {
            // ignore invalid hrefs
        }
        a.addEventListener('click', function () {
            links.forEach(x => x.classList.remove('active'));
            this.classList.add('active');
        });
    });
})();
</script>
<script>
(function () {
    const STAFF_SEARCH_BASE = "{{ route('admin.users.index') }}";
    const searchWrap = document.querySelector('.topbar .search');
    const input = searchWrap && searchWrap.querySelector('input');
    const menuLinks = Array.from(document.querySelectorAll('.sidebar .menu a'));
    if (!input || menuLinks.length === 0) return;

    const navItems = menuLinks
        .map(function (a) {
            return {
                label: (a.textContent || '').trim(),
                url: a.href,
            };
        })
        .filter(function (item) { return item.label && item.url; });

    const dropdown = document.createElement('ul');
    Object.assign(dropdown.style, {
        position: 'absolute',
        top: '100%',
        left: '0',
        right: '0',
        background: '#fff',
        border: '1px solid #e6edf3',
        borderRadius: '8px',
        listStyle: 'none',
        margin: '4px 0 0',
        padding: '4px 0',
        boxShadow: '0 4px 16px rgba(0,0,0,.12)',
        zIndex: '999',
        display: 'none',
    });
    searchWrap.style.position = 'relative';
    searchWrap.appendChild(dropdown);

    let active = -1;

    function clearHighlight() {
        Array.from(dropdown.children).forEach(function (el) { el.style.background = ''; });
    }

    function navigate(url) {
        input.value = '';
        dropdown.style.display = 'none';
        window.location.href = url;
    }

    function buildStaffSearchUrl(term) {
        return STAFF_SEARCH_BASE + '?staff_search=' + encodeURIComponent(term);
    }

    function renderList(query) {
        dropdown.innerHTML = '';
        active = -1;
        const q = (query || '').toLowerCase();
        const results = q
            ? navItems.filter(function (item) { return item.label.toLowerCase().includes(q); })
            : navItems;

        if (q) {
            results.push({
                label: 'Search Staff by Name/EID: "' + query + '"',
                url: buildStaffSearchUrl(query),
            });
        }

        if (!results.length) {
            dropdown.style.display = 'none';
            return;
        }

        results.forEach(function (item, index) {
            const li = document.createElement('li');
            li.textContent = item.label;
            li.dataset.url = item.url;
            Object.assign(li.style, {
                padding: '8px 14px',
                cursor: 'pointer',
                color: '#333',
                fontSize: '14px',
            });
            li.addEventListener('mouseenter', function () {
                clearHighlight();
                li.style.background = '#fff3e8';
                active = index;
            });
            li.addEventListener('mouseleave', function () { li.style.background = ''; });
            li.addEventListener('mousedown', function (e) {
                e.preventDefault();
                navigate(item.url);
            });
            dropdown.appendChild(li);
        });

        dropdown.style.display = 'block';
    }

    input.setAttribute('autocomplete', 'off');
    input.addEventListener('input', function () { renderList(input.value.trim()); });
    input.addEventListener('focus', function () { renderList(input.value.trim()); });
    input.addEventListener('blur', function () {
        setTimeout(function () { dropdown.style.display = 'none'; }, 150);
    });

    input.addEventListener('keydown', function (e) {
        const items = Array.from(dropdown.children);
        if (!items.length) return;

        if (e.key === 'ArrowDown') {
            e.preventDefault();
            clearHighlight();
            active = Math.min(active + 1, items.length - 1);
            if (items[active]) items[active].style.background = '#fff3e8';
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            clearHighlight();
            active = Math.max(active - 1, 0);
            if (items[active]) items[active].style.background = '#fff3e8';
        } else if (e.key === 'Enter') {
            e.preventDefault();
            if (active >= 0 && items[active]) {
                navigate(items[active].dataset.url);
            } else if (input.value.trim() !== '') {
                navigate(buildStaffSearchUrl(input.value.trim()));
            } else {
                navigate(items[0].dataset.url);
            }
        } else if (e.key === 'Escape') {
            dropdown.style.display = 'none';
            input.blur();
        }
    });
})();
</script>

</body>
</html>
