<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>HoD Dashboard</title>
    <link rel="stylesheet" href="/css/hod_dashboard.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body{font-family: 'Inter', system-ui, -apple-system, 'Segoe UI', Roboto, Arial, sans-serif;}
        .topbar .logout form button{background:transparent;border:0;padding:6px 10px;border-radius:6px;font-weight:600}
        .topbar .logout form button:hover{background:#f8f9fa}
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

        <div class="container">
            <header>
                <h1>HoD Dashboard</h1>
            </header>

            @if (! $authorized)
                <div class="access-denied">
                    <h2>Access Denied</h2>
                    <p>You are not assigned as HoD.</p>
                    <a href="{{ route('dashboard') }}">Return to Dashboard</a>
                </div>
            @else
                @if (session('message'))
                    <div class="notice">{{ session('message') }}</div>
                @endif

                <section class="tiles">
                    <a class="tile tile-purple" href="{{ route('hod.staff_list') }}">
                        <div class="icon">👥</div>
                        <div class="content">
                            <div class="title">Total Staff</div>
                            <div class="subtitle">View your department staff</div>
                        </div>
                        <div class="content" style="text-align:right"><div class="title">{{ $totalStaff ?? 0 }}</div></div>
                    </a>

                    <a class="tile tile-blue" href="{{ route('hod.pending') }}">
                        <div class="icon">⏳</div>
                        <div class="content">
                            <div class="title">Pending</div>
                            <div class="subtitle">Requests awaiting your action</div>
                        </div>
                        <div style="text-align:right"><div class="title">{{ $summary['pending'] }}</div></div>
                    </a>

                    <a class="tile tile-gray" href="{{ route('hod.recent') }}">
                        <div class="icon">📨</div>
                        <div class="content">
                            <div class="title">Forwarded to MS</div>
                            <div class="subtitle">Recently forwarded</div>
                        </div>
                        <div style="text-align:right"><div class="title">{{ $summary['forwarded'] }}</div></div>
                    </a>

                    <a class="tile tile-yellow" href="{{ route('hod.adhoc.index') }}">
                        <div class="icon">📌</div>
                        <div class="content">
                            <div class="title">Adhoc Requests</div>
                            <div class="subtitle">Manage adhoc duty requests</div>
                        </div>
                        <div style="text-align:right"><div class="title">{{ $adhocCount ?? 0 }}</div></div>
                    </a>

                    <a class="tile tile-red" href="{{ route('hod.recent') }}">
                        <div class="icon">❌</div>
                        <div class="content">
                            <div class="title">Rejected</div>
                            <div class="subtitle">Requests you rejected</div>
                        </div>
                        <div style="text-align:right"><div class="title">{{ $summary['rejected'] }}</div></div>
                    </a>

                    <a class="tile tile-orange" href="{{ route('hod.on_tour') }}">
                        <div class="icon">🧭</div>
                        <div class="content">
                            <div class="title">Staff On Tour</div>
                            <div class="subtitle">Current tours in your depts</div>
                        </div>
                        <div style="text-align:right"><div class="title">{{ $onTourCount ?? 0 }}</div></div>
                    </a>
                </section>
                <!-- Removed embedded detailed lists (moved to dedicated pages) -->
            @endif
        </div>
    </main>
</div>

<script>
    document.querySelectorAll('.sidebar .menu a').forEach(function (a) {
        a.addEventListener('click', function () {
            document.querySelectorAll('.sidebar .menu a').forEach(function (x) {
                x.classList.remove('active');
            });
            this.classList.add('active');
        });
    });
</script>
<script>
(function () {
    const STAFF_SEARCH_BASE = "{{ route('hod.staff_list') }}";
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
