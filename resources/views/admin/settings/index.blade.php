@extends('admin_layout')

@section('pageTitle', 'Admin Settings')

@section('content')
<div style="padding:18px">
    <h1>Admin Settings</h1>
    

    <div id="settingsGrid" style="display:grid;grid-template-columns:360px 1fr;gap:18px;align-items:start;">
        <!-- Left: Single form (create/edit/change password) - hidden until used -->
        <div id="adminPanel" style="display:none">
            <div class="panel" style="padding:16px;border-radius:8px">
                <div style="text-align:right;margin-bottom:6px">
                    <a href="#" id="closePanel" style="color:#666;text-decoration:none">Close</a>
                </div>
                <form id="adminForm" method="POST" action="/admin/settings/manage">
                    @csrf
                    <input type="hidden" name="_id" id="admin_id" value="">
                    <div style="margin-bottom:12px">
                        <label style="font-weight:700">Admin Name</label>
                        <input id="admin_name" name="name" required style="width:100%;padding:8px;border:1px solid #cfd8db;border-radius:6px">
                    </div>
                    <div style="margin-bottom:12px">
                        <label style="font-weight:700">Username / Email</label>
                        <input id="admin_username" name="username" required style="width:100%;padding:8px;border:1px solid #cfd8db;border-radius:6px">
                    </div>
                    <div style="margin-bottom:12px">
                        <label style="font-weight:700">Password</label>
                        <input id="admin_password" type="password" name="password" placeholder="Leave blank to keep existing" style="width:100%;padding:8px;border:1px solid #cfd8db;border-radius:6px">
                    </div>
                    <div style="margin-bottom:12px">
                        <label style="font-weight:700">Status</label>
                        <select id="admin_active" name="active" style="width:100%;padding:8px;border:1px solid #cfd8db;border-radius:6px">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>

                    <div style="display:flex;gap:8px;align-items:center">
                        <button class="btn" type="submit">Save Admin</button>
                        <button id="changePasswordBtn" type="button" class="btn" style="background:#6c757d">Change Password</button>
                        <button id="toggleBtn" type="button" class="btn btn-secondary" style="margin-left:auto">Toggle Status</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Right: List -->
        <div id="adminList">
            <div style="margin-bottom:12px">
                <a href="{{ route('admin.settings.manage') }}" class="btn" style="font-size:15px;padding:7px 16px;text-decoration:none;">+ New Admin</a>
            </div>

            <div class="leave-history">
                <div class="table-wrap">
                    <table class="users">
                        <thead>
                            <tr>
                                <th>Admin Name</th>
                                <th>Username / Email</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (empty($admins))
                                <tr><td colspan="4">No admin accounts found.</td></tr>
                            @else
                                @foreach($admins as $a)
                                    <tr data-admin='@json($a)'>
                                        <td>{{ $a['name'] ?? ($a['admin_name'] ?? ($a['username'] ?? '-')) }}</td>
                                        <td>{{ $a['username'] ?? $a['email'] ?? '-' }}</td>
                                        <td>
                                            @if (!empty($a['active']) || !empty($a['is_active']) || (!empty($a['is_admin']) && $a['is_admin'] == 1))
                                                <span class="status-active">Active</span>
                                            @else
                                                <span class="status-inactive">Inactive</span>
                                            @endif
                                        </td>
                                        <td style="white-space:nowrap">
                                            <a href="#" class="action-orange edit-link">Edit</a> |
                                            <a href="#" class="action-orange change-pass-link">Change Password</a> |
                                            <a href="#" class="action-orange toggle-link">Toggle</a>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    <script>
        (function(){
            const panel = document.getElementById('adminPanel');
            const grid = document.getElementById('settingsGrid');
            const list = document.getElementById('adminList');
            if (panel) panel.style.display = 'none';
            const rows = document.querySelectorAll('tr[data-admin]');
            const form = document.getElementById('adminForm');
            const idField = document.getElementById('admin_id');
            const nameField = document.getElementById('admin_name');
            const userField = document.getElementById('admin_username');
            const passField = document.getElementById('admin_password');
            const activeField = document.getElementById('admin_active');
            const changeBtn = document.getElementById('changePasswordBtn');
            const toggleBtn = document.getElementById('toggleBtn');

            function clearForm(){ idField.value=''; nameField.value=''; userField.value=''; passField.value=''; activeField.value='1'; form.action='/admin/settings/manage'; }

            rows.forEach(r => {
                const admin = JSON.parse(r.getAttribute('data-admin'));
                r.querySelector('.edit-link').addEventListener('click', (e)=>{
                    e.preventDefault();
                    if (panel) panel.style.display = 'block';
                    if (grid) grid.style.gridTemplateColumns = '1fr';
                    if (list) list.style.display = 'none';
                    // widen and center the inner panel for full-page form
                    try {
                        const inner = panel.querySelector('.panel');
                        if (inner) { inner.style.maxWidth = '900px'; inner.style.margin = '0 auto'; width:inner.style.width='100%'; }
                    } catch(e){}
                    idField.value = admin.id ?? admin.admin_id ?? admin.employee_id ?? '';
                    nameField.value = admin.name ?? admin.admin_name ?? '';
                    userField.value = admin.username ?? admin.email ?? '';
                    activeField.value = (admin.active ?? admin.is_active ?? 1) ? '1' : '0';
                    passField.value = '';
                    form.action = '/admin/settings/manage/' + idField.value;
                    // focus and scroll
                    try { nameField.focus(); panel.scrollIntoView({behavior:'smooth'}); } catch(e){}
                });
                r.querySelector('.change-pass-link').addEventListener('click', (e)=>{
                    e.preventDefault();
                    if (panel) panel.style.display = 'block';
                    if (grid) grid.style.gridTemplateColumns = '1fr';
                    if (list) list.style.display = 'none';
                    try {
                        const inner = panel.querySelector('.panel');
                        if (inner) { inner.style.maxWidth = '900px'; inner.style.margin = '0 auto'; inner.style.width='100%'; }
                    } catch(e){}
                    idField.value = admin.id ?? admin.admin_id ?? admin.employee_id ?? '';
                    form.action = '/admin/settings/manage/' + idField.value;
                    passField.focus();
                });
                r.querySelector('.toggle-link').addEventListener('click', (e)=>{
                    e.preventDefault();
                    const aid = admin.id ?? admin.admin_id ?? admin.employee_id ?? '';
                    if (!aid) return;
                    if (!confirm('Toggle admin status?')) return;
                    fetch('/admin/settings/toggle/' + aid, {method:'POST', headers: {'X-CSRF-TOKEN':'{{ csrf_token() }}'}})
                        .then(()=> location.reload())
                        .catch(()=> alert('Failed'));
                });
            });

            // Note: New Admin link navigates to the manage route; panel only shows on Edit

            // Close button restores list view
            const closeBtn = document.getElementById('closePanel');
            if (closeBtn) {
                closeBtn.addEventListener('click', function(e){
                    e.preventDefault();
                    if (panel) panel.style.display = 'none';
                    if (grid) grid.style.gridTemplateColumns = '360px 1fr';
                    if (list) list.style.display = '';
                    try { const inner = panel.querySelector('.panel'); if (inner) { inner.style.maxWidth=''; inner.style.margin=''; inner.style.width=''; } } catch(e){}
                    clearForm();
                });
            }

            changeBtn.addEventListener('click', ()=>{
                if (!idField.value) { alert('Select an admin to change password'); return; }
                if (!passField.value) { alert('Enter new password'); return; }
                form.submit();
            });

            toggleBtn.addEventListener('click', ()=>{
                const aid = idField.value;
                if (!aid) { alert('Select an admin to toggle'); return; }
                if (!confirm('Toggle admin status?')) return;
                fetch('/admin/settings/toggle/' + aid, {method:'POST', headers: {'X-CSRF-TOKEN':'{{ csrf_token() }}'}})
                    .then(()=> location.reload())
                    .catch(()=> alert('Failed'));
            });

            // init empty
            clearForm();
        })();
    </script>

</div>
@endsection
