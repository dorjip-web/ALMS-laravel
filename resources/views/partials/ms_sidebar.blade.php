<aside class="sidebar">
    @php
        $msAuthorized = $authorized ?? false;
        $msUsername = $username ?? auth()->user()->name;
    @endphp
    <div class="profile">
        <div class="avatar">{{ $msAuthorized ? 'MS' : strtoupper(substr($msUsername, 0, 1) ?? 'M') }}</div>
        <div class="username">{{ $msUsername }}</div>
    </div>

    <nav class="menu">
        <a href="{{ route('dashboard') }}" @class(['active' => request()->routeIs('dashboard')])>Back to Dashboard</a>
        <a href="{{ route('ms.dashboard') }}" @class(['active' => request()->routeIs('ms.dashboard')])>MS Dashboard</a>
        <a href="{{ route('ms.adhoc.index') }}" @class(['active' => request()->routeIs('ms.adhoc.*')])>Adhoc Requests</a>
        <a href="{{ route('ms.dashboard') }}#pending-approvals">Pending Approvals</a>
        <a href="{{ route('ms.dashboard') }}#on-tour">Staff On Tour</a>
        <a href="{{ route('ms.dashboard') }}#recent-decisions">Recent Decisions</a>
    </nav>
    <script>
        (function () {
            try {
                var links = Array.from(document.querySelectorAll('.sidebar .menu a'));
                links.forEach(function (a) {
                    a.addEventListener('click', function () {
                        links.forEach(function (x) { x.classList.remove('active'); });
                        this.classList.add('active');
                    });
                });

                // If page loads with a hash, activate the corresponding link
                var hash = location.hash;
                if (hash) {
                    var selector = '.sidebar .menu a[href$="' + hash + '"]';
                    var match = document.querySelector(selector);
                    if (match) {
                        links.forEach(function (x) { x.classList.remove('active'); });
                        match.classList.add('active');
                    }
                }
            } catch (e) {
                // ignore
            }
        }());
    </script>
</aside>
