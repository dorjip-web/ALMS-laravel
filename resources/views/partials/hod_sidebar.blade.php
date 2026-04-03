<aside class="sidebar">
    <div class="profile">
        <div class="avatar">H</div>
        <div class="username">HoD</div>
    </div>

    <nav class="menu">
        <a href="{{ route('dashboard') }}" @class(['active' => request()->routeIs('dashboard')])>Back to Dashboard</a>
        <a href="{{ route('hod.dashboard') }}" @class(['active' => request()->routeIs('hod.dashboard')])>HoD Dashboard</a>
        <a href="{{ route('hod.adhoc.index') }}" @class(['active' => request()->routeIs('hod.adhoc.*')])>Adhoc Requests</a>
        <a href="{{ route('hod.staff_list') }}" @class(['active' => request()->routeIs('hod.staff_list')])>View Staff List</a>
        <a href="{{ route('hod.dashboard') }}#pending-requests">Pending Leave Requests</a>
        <a href="{{ route('hod.dashboard') }}#on-tour">Staff On Tour</a>
        <a href="{{ route('hod.dashboard') }}#recent-actions">Recent Leave Actions</a>
    </nav>
    <script>
        // ensure clicked links get highlighted immediately
        (function(){
            document.querySelectorAll('.sidebar .menu a').forEach(function(a){
                a.addEventListener('click', function(){
                    document.querySelectorAll('.sidebar .menu a').forEach(function(x){ x.classList.remove('active'); });
                    this.classList.add('active');
                });
            });
        })();
    </script>
</aside>
