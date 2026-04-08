@extends('admin_layout')

@section('pageTitle', 'Admin Settings')

@section('content')
<div style="padding:18px">
    <h1>Admin Settings</h1>
    

    <div id="settingsGrid" style="display:grid;grid-template-columns:1fr;gap:18px;align-items:start;">
        <!-- Left: Single form (create/edit/change password) - hidden until used -->
        <div id="adminPanel" style="display:none">
            <div class="panel" style="padding:16px;border-radius:8px">
                <!-- top Close removed; Cancel button will be next to Save Admin -->
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
                        <button id="cancelBtn" type="button" class="btn" style="background:#fff;border:1px solid #cfd8db;color:#333">Cancel</button>
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
                                            @php
                                                $isActive = false;
                                                if (!empty($a['active']) || !empty($a['is_active'])) { $isActive = true; }
                                                if (!empty($a['is_admin']) && $a['is_admin'] == 1) { $isActive = true; }
                                                if (isset($a['status'])) {
                                                    $s = strtolower(trim((string)$a['status']));
                                                    if ($s === 'active' || $s === '1' || $s === 'true') { $isActive = true; }
                                                }
                                            @endphp
                                            @if ($isActive)
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
            // remember original panel location so we can restore it
            const panelOriginalParent = panel ? panel.parentNode : null;
            const panelOriginalNext = panel ? panel.nextSibling : null;
            if (panel) panel.style.display = 'none';
            if (grid) grid.style.gridTemplateColumns = '1fr';
            if (list) list.style.display = '';
            const rows = document.querySelectorAll('tr[data-admin]');
            const form = document.getElementById('adminForm');
            const idField = document.getElementById('admin_id');
            const nameField = document.getElementById('admin_name');
            const userField = document.getElementById('admin_username');
            const passField = document.getElementById('admin_password');
            const activeField = document.getElementById('admin_active');
            // change/toggle buttons removed from form intentionally

            function clearForm(){ idField.value=''; nameField.value=''; userField.value=''; passField.value=''; activeField.value='1'; form.action='/admin/settings/manage'; }

            rows.forEach(r => {
                const admin = JSON.parse(r.getAttribute('data-admin'));
                r.querySelector('.edit-link').addEventListener('click', (e)=>{
                    e.preventDefault();
                    // move panel to sit immediately below the table and show full-width
                    if (panel && list) {
                        try { list.parentNode.insertBefore(panel, list.nextSibling); } catch(err) {}
                        panel.style.display = 'block';
                        panel.style.width = '100%';
                        if (grid) grid.style.gridTemplateColumns = '1fr';
                        try { const inner = panel.querySelector('.panel'); if (inner) { inner.style.maxWidth=''; inner.style.margin='0 0 12px 0'; inner.style.width='100%'; } } catch(e){}
                    }
                    idField.value = admin.id ?? admin.admin_id ?? admin.employee_id ?? '';
                    nameField.value = admin.name ?? admin.admin_name ?? '';
                    userField.value = admin.username ?? admin.email ?? '';
                    activeField.value = (admin.active ?? admin.is_active ?? 1) ? '1' : '0';
                    passField.value = '';
                    form.action = '/admin/settings/manage/' + idField.value;
                    // focus the name field and scroll to the form
                    try { nameField.focus(); panel.scrollIntoView({behavior:'smooth'}); } catch(e){}
                });

                // change password link (table row)
                const changeLink = r.querySelector('.change-pass-link');
                if (changeLink) {
                    changeLink.addEventListener('click', (e) => {
                        e.preventDefault();
                        if (panel && list) {
                            try { list.parentNode.insertBefore(panel, list.nextSibling); } catch(err) {}
                            panel.style.display = 'block';
                            panel.style.width = '100%';
                            if (grid) grid.style.gridTemplateColumns = '1fr';
                            try { const inner = panel.querySelector('.panel'); if (inner) { inner.style.maxWidth=''; inner.style.margin='0 0 12px 0'; inner.style.width='100%'; } } catch(e){}
                        }
                        idField.value = admin.id ?? admin.admin_id ?? admin.employee_id ?? '';
                        form.action = '/admin/settings/manage/' + idField.value;
                        passField.focus();
                    });
                }

                // toggle link (table row)
                const toggleLink = r.querySelector('.toggle-link');
                    if (toggleLink) {
                    toggleLink.addEventListener('click', (e) => {
                        e.preventDefault();
                        const aid = admin.id ?? admin.admin_id ?? admin.employee_id ?? '';
                        if (!aid) return;
                        if (!confirm('Toggle admin status?')) return;
                        // compute desired state (opposite of current shown state) and send it explicitly
                        (function(){
                            let desiredVal = null;
                            const statusCell = r.querySelector('td:nth-child(3)');
                            if (statusCell) {
                                const activeSpan = statusCell.querySelector('.status-active') || statusCell.querySelector('.status-inactive');
                                if (activeSpan) {
                                    const cur = activeSpan.textContent.trim().toLowerCase();
                                    desiredVal = (cur === 'active') ? 'Inactive' : 'Active';
                                }
                            }

                            fetch('/admin/settings/toggle/' + aid, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Content-Type': 'application/json'
                                },
                                body: JSON.stringify({ desired: desiredVal })
                            })
                            .then((res)=>{
                                if (!res.ok) throw new Error('Request failed');
                                return res.json();
                            })
                            .then((data) => {
                                // update status cell using authoritative server response
                                try {
                                    const statusCell = r.querySelector('td:nth-child(3)');
                                    if (statusCell && data && data.new !== undefined && data.new !== null) {
                                        const activeSpan = statusCell.querySelector('.status-active') || statusCell.querySelector('.status-inactive');
                                        if (activeSpan) {
                                            const newVal = String(data.new).toLowerCase();
                                            if (newVal === 'active' || newVal === '1' || newVal === 'true') {
                                                activeSpan.classList.remove('status-inactive');
                                                activeSpan.classList.add('status-active');
                                                activeSpan.textContent = 'Active';
                                            } else {
                                                activeSpan.classList.remove('status-active');
                                                activeSpan.classList.add('status-inactive');
                                                activeSpan.textContent = 'Inactive';
                                            }
                                        }
                                    } else {
                                        // fallback to reload if response missing
                                        location.reload();
                                    }
                                } catch (err) {
                                    location.reload();
                                }
                            })
                            .catch(()=> alert('Failed to toggle admin'));
                        })();
                    });
                }
                // table-level change-password and toggle links removed; keep form buttons
            });

            // Note: New Admin link navigates to the manage route; panel only shows on Edit

            // Cancel button next to Save Admin restores list view
            const cancelBtn = document.getElementById('cancelBtn');
            if (cancelBtn) {
                cancelBtn.addEventListener('click', function(e){
                    e.preventDefault();
                    if (panel) panel.style.display = 'none';
                    if (grid) grid.style.gridTemplateColumns = '1fr';
                    if (list) list.style.display = '';
                    // restore panel back to its original DOM position
                    try { if (panel && panelOriginalParent) panelOriginalParent.insertBefore(panel, panelOriginalNext); } catch(err){}
                    try { const inner = panel.querySelector('.panel'); if (inner) { inner.style.maxWidth=''; inner.style.margin=''; inner.style.width=''; } } catch(e){}
                    clearForm();
                });
            }

            // form-level change/toggle actions removed; use main table actions instead

            // init empty
            clearForm();
        })();
    </script>

</div>
@endsection
