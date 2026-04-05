@extends('admin_layout')

@section('content')
<style>
    :root{--accent:#f97316;--accent-dark:#ff7a1a;--muted:#6b7280}
    body{background:#eaf5f6}
    .page-header{display:flex;flex-direction:column;align-items:flex-start;padding:22px 6px;margin-bottom:6px}
    .page-title{font-size:46px;font-weight:800;color:#061018;margin:0;padding:0}
    .page-sub{margin-top:12px}
    .add-btn{background:var(--accent);color:#fff;padding:10px 18px;border-radius:8px;text-decoration:none;font-weight:800;box-shadow:0 2px 0 rgba(0,0,0,0.04)}

    .rp-grid { display:flex; gap:18px; flex-wrap:wrap; }
    .rp-column { flex:1 1 360px; min-width:320px; }
    .rp-card { background:transparent;border-radius:0;padding:0;box-shadow:none }
    .rp-header { display:flex;align-items:center;justify-content:space-between;margin-bottom:12px }
    .rp-title { font-size:18px;font-weight:700 }
    .rp-actions { display:flex;gap:8px }
    .btn-primary{background:var(--accent);color:#fff;padding:8px 12px;border-radius:6px;border:none;font-weight:700}
    .btn-ghost{background:#fff;color:#333;padding:8px 12px;border-radius:6px;border:1px solid #e9eef3}
    .table-compact{width:100%;border-collapse:collapse}
    .table-compact thead th{padding:14px 20px;text-align:left;border-bottom:3px solid var(--accent-dark);font-weight:700;background:transparent}
    .table-compact th,.table-compact td{padding:18px 20px;text-align:left}
    .table-compact tbody tr td{border-bottom:1px solid var(--accent-dark)}
    .table-compact tbody tr{background:transparent}
    .action-links a{color:var(--accent);font-weight:700;text-decoration:none;margin-left:6px}

    /* Users table alignment tweaks */
    .users{width:100%;border-collapse:collapse}
    .users thead th{padding:14px 20px;border-bottom:3px solid var(--accent-dark);font-weight:700}
    .users th:nth-child(1), .users td:nth-child(1){width:55%;text-align:left}
    .users th:nth-child(2), .users td:nth-child(2){width:3%;text-align:center}
    .users th:nth-child(3), .users td:nth-child(3){width:12%;text-align:left;padding-left:6px}
    .users th:nth-child(4), .users td:nth-child(4){width:30%;text-align:left}
    /* Prevent action links and status from wrapping; allow role name to wrap when needed */
    .users td{padding:12px 12px;vertical-align:middle;border-bottom:1px solid var(--accent-dark);white-space:nowrap}
    .users td .dept-name{white-space:normal;display:block}
    .users td .action-orange{color:var(--accent);font-weight:700;text-decoration:none;margin-right:12px;display:inline-block}
    .status-active{color:#2563eb;font-weight:700}
    .status-inactive{color:var(--muted)}
    .muted{color:var(--muted);font-size:13px}
    .search-input{width:100%;padding:8px;border-radius:6px;border:1px solid #e6eef8}
    @media(max-width:900px){.rp-grid{flex-direction:column}}
</style>

<div style="padding:18px">
    <h1>Roles &amp; Permissions Management</h1>
    <div style="margin-bottom:18px;">
        <a href="#add-role" class="btn" style="font-size:15px;padding:7px 16px;text-decoration:none;">+ Add Role</a>
    </div>
    <div class="leave-history">
        <div class="table-wrap">
            <table class="users">
                <thead>
                    <tr>
                        <th>Role</th>
                        <th>&nbsp;</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
    <!-- Add Role form -->
    <div id="add-role" style="padding:18px;display:none">
        <h2>Create Role</h2>
        <form method="POST" action="{{ route('admin.roles_permissions.saveRole') }}">
            @csrf
            <input type="hidden" name="role_id" value="0">
            <div style="margin-bottom:8px"><label class="muted">Role name</label>
                <input class="search-input" type="text" name="role_name" required placeholder="e.g. Manager"></div>
            <div style="margin-bottom:10px"><label class="muted">Status</label>
                <select name="status" class="search-input">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
            <div style="display:flex;gap:8px"><button class="btn" type="submit">Save Role</button>
                <a href="#" class="btn hide-form" data-target="add-role" style="background:#fff;border:1px solid #cfd8db;color:#333">Cancel</a></div>
        </form>
    </div>
                    @forelse($roles as $r)
                        <tr>
                            <td class="dept-name">{{ $r->role_name }}</td>
                            <td class="dept-hod">&nbsp;</td>
                            <td class="dept-status">
                                @if (strtolower(trim($r->status ?? '')) === 'active')
                                    <span class="status-active">Active</span>
                                @else
                                    <span class="status-inactive">{{ ucfirst($r->status) }}</span>
                                @endif
                            </td>
                            <td>
                                <a class="action-orange" href="{{ route('admin.roles_permissions', ['edit_role' => $r->role_id]) }}">Edit</a> |
                                <a class="action-orange" href="{{ route('admin.roles_permissions', ['assign_role_id' => $r->role_id]) }}">Assign</a> |
                                <a class="action-orange" href="{{ route('admin.roles_permissions.toggleRole', $r->role_id) }}">Toggle Status</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4">No roles found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@if($editRole)
    <div style="padding:18px">
        <h2>Edit Role</h2>
        <form method="POST" action="{{ route('admin.roles_permissions.saveRole') }}">
            @csrf
            <input type="hidden" name="role_id" value="{{ $editRole->role_id }}">
            <div style="margin-bottom:8px"><label class="muted">Role name</label>
                <input class="search-input" type="text" name="role_name" required value="{{ $editRole->role_name }}"></div>
            <div style="margin-bottom:10px"><label class="muted">Status</label>
                <select name="status" class="search-input">
                    <option value="active" @if(($editRole->status ?? '') === 'active') selected @endif>Active</option>
                    <option value="inactive" @if(($editRole->status ?? '') === 'inactive') selected @endif>Inactive</option>
                </select>
            </div>
            <div style="display:flex;gap:8px"><button class="btn" type="submit">Update Role</button>
                <a href="{{ route('admin.roles_permissions') }}" class="btn" style="background:#fff;border:1px solid #cfd8db;color:#333">Cancel</a></div>
        </form>
    </div>
@endif

<div style="padding:18px;margin-top:8px">
    <h2>Permissions</h2>
    <div style="margin-bottom:12px;">
        <a href="#add-perm" class="btn" style="font-size:15px;padding:7px 16px;text-decoration:none;">+ Add Permission</a>
    </div>
    <!-- Add Permission form -->
    <div id="add-perm" style="padding:8px 0 18px 0;display:none">
        <form method="POST" action="{{ route('admin.roles_permissions.savePermission') }}">
            @csrf
            <input type="hidden" name="perm_id" value="0">
            <div style="margin-bottom:8px"><label class="muted">Permission name</label>
                <input class="search-input" type="text" name="permission_name" required placeholder="e.g. Approve leave"></div>
            <div style="margin-bottom:10px"><label class="muted">Status</label>
                <select name="pstatus" class="search-input">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
            <div style="display:flex;gap:8px"><button class="btn" type="submit">Save Permission</button>
                <a href="#" class="btn hide-form" data-target="add-perm" style="background:#fff;border:1px solid #cfd8db;color:#333">Cancel</a></div>
        </form>
    </div>
    <div class="leave-history">
        <div class="table-wrap">
            <table class="users">
                <thead>
                    <tr>
                        <th>Permission</th>
                        <th>&nbsp;</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($permissions as $p)
                        <tr>
                            <td class="dept-name">{{ $p->permission_name }}</td>
                            <td class="dept-hod">&nbsp;</td>
                            <td class="dept-status">
                                @if (strtolower(trim($p->status ?? '')) === 'active')
                                    <span class="status-active">Active</span>
                                @else
                                    <span class="status-inactive">{{ ucfirst($p->status) }}</span>
                                @endif
                            </td>
                            <td>
                                <a class="action-orange" href="{{ route('admin.roles_permissions', ['edit_perm' => $p->permission_id]) }}">Edit</a> |
                                <a class="action-orange" href="#">Assign</a> |
                                <a class="action-orange" href="{{ route('admin.roles_permissions.togglePerm', $p->permission_id) }}">Toggle Status</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4">No permissions found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@if($editPermission)
    <div style="padding:18px">
        <h2>Edit Permission</h2>
        <form method="POST" action="{{ route('admin.roles_permissions.savePermission') }}">
            @csrf
            <input type="hidden" name="perm_id" value="{{ $editPermission->permission_id }}">
            <div style="margin-bottom:8px"><label class="muted">Permission name</label>
                <input class="search-input" type="text" name="permission_name" required value="{{ $editPermission->permission_name }}"></div>
            <div style="margin-bottom:10px"><label class="muted">Status</label>
                <select name="pstatus" class="search-input">
                    <option value="active" @if(($editPermission->status ?? '') === 'active') selected @endif>Active</option>
                    <option value="inactive" @if(($editPermission->status ?? '') === 'inactive') selected @endif>Inactive</option>
                </select>
            </div>
            <div style="display:flex;gap:8px"><button class="btn" type="submit">Update Permission</button>
                <a href="{{ route('admin.roles_permissions') }}" class="btn" style="background:#fff;border:1px solid #cfd8db;color:#333">Cancel</a></div>
        </form>
    </div>
@endif

@if(request('assign_role_id') || request('assign_role'))
    <div style="padding:18px">
        <h2>Assign Permissions</h2>
        <form method="POST" action="{{ route('admin.roles_permissions.saveAssign') }}">
            @csrf
            <div style="margin-bottom:10px">
                <label class="muted">Select Role</label>
                <select name="assign_role_id" class="search-input">
                    <option value="">-- Select Role --</option>
                    @foreach($roles as $rr)
                        <option value="{{ $rr->role_id }}" @if((string)request('assign_role_id') === (string)$rr->role_id || (string)request('assign_role') === (string)$rr->role_id) selected @endif>{{ $rr->role_name }}</option>
                    @endforeach
                </select>
            </div>
            <div style="margin-bottom:8px">
                <label class="muted">Permissions</label>
                <div style="margin-top:8px;max-height:260px;overflow:auto;border-top:0;border-bottom:0;padding:8px">
                    @foreach($permissions as $pp)
                        <div style="display:flex;align-items:center;gap:10px;margin-bottom:6px">
                            <input type="checkbox" name="assign_permissions[]" value="{{ $pp->permission_id }}" @if(in_array($pp->permission_id, $assignedPermissions)) checked @endif>
                            <div>{{ $pp->permission_name }} <span class="muted">{{ ucfirst($pp->status) }}</span></div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div style="display:flex;gap:8px"><button class="btn" type="submit">Save Assignments</button>
                <a href="{{ route('admin.roles_permissions') }}" class="btn" style="background:#fff;border:1px solid #cfd8db;color:#333">Cancel</a></div>
        </form>
    </div>
@endif

@if ($message)
    <div style="color:green;margin-top:12px">{{ $message }}</div>
@endif

<script>
    function filterTable(id, q) {
        q = q.toLowerCase();
        let rows = document.querySelectorAll('#'+id+' tbody tr');
        rows.forEach(r => {
            r.style.display = r.innerText.toLowerCase().includes(q) ? '' : 'none';
        });
    }

    // Show add forms only when Add button clicked; hide on Cancel
    document.addEventListener('DOMContentLoaded', function(){
        // Open add-role/add-perm when their links are clicked
        document.querySelectorAll('a[href="#add-role"]').forEach(a=>{
            a.addEventListener('click', function(e){ e.preventDefault(); document.getElementById('add-role').style.display='block'; document.getElementById('add-role').scrollIntoView({behavior:'smooth'}); });
        });
        document.querySelectorAll('a[href="#add-perm"]').forEach(a=>{
            a.addEventListener('click', function(e){ e.preventDefault(); document.getElementById('add-perm').style.display='block'; document.getElementById('add-perm').scrollIntoView({behavior:'smooth'}); });
        });

        // Hide forms when Cancel clicked
        document.querySelectorAll('.hide-form').forEach(btn=>{
            btn.addEventListener('click', function(e){ e.preventDefault(); const t = this.getAttribute('data-target'); if(t && document.getElementById(t)) document.getElementById(t).style.display='none'; });
        });
    });
</script>

@endsection
