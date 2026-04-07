@extends('admin_layout')

@section('content')
<div style="padding:18px">
    <h1>Device Bindings</h1>

    

    @if (! empty($error))
        <div class="flash-error">{{ $error }}</div>
    @endif

    @if (session('flash_success'))
        <div class="flash-success">{{ session('flash_success') }}</div>
    @endif
    @if (session('flash_error'))
        <div class="flash-error">{{ session('flash_error') }}</div>
    @endif

    <div class="leave-history">
        <div class="table-wrap">
            <table class="users">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Employee ID</th>
                        <th>EID</th>
                        <th>Employee Name</th>
                        <th>Device Token</th>
                        <th>Bind Date</th>
                        <th>Flags</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rows as $r)
                        <tr @if(!empty($r['suspicious'])) class="suspect" @endif>
                            <td>{{ $r['id'] ?? '-' }}</td>
                            <td>{{ $r['employee_id'] ?? '-' }}</td>
                            <td>{{ $r['employee_eid'] ?? '-' }}</td>
                            <td>{{ $r['employee_name'] ?? '-' }}</td>
                            <td style="font-family:monospace">{{ $r['device_token'] ?? '-' }}</td>
                            <td>{{ !empty($r['bind_date']) ? \Illuminate\Support\Carbon::parse($r['bind_date'])->format('d/m/Y') : (!empty($r['created_at']) ? \Illuminate\Support\Carbon::parse($r['created_at'])->format('d/m/Y') : '-') }}</td>
                            <td>
                                @if(!empty($r['multiple_devices'])) <span class="flag">Multiple Devices</span><br>@endif
                                @if(!empty($r['token_shared'])) <span class="flag">Shared Token</span><br>@endif
                                @if(!empty($r['missing_eid'])) <span class="flag">Missing/Invalid EID</span><br>@endif
                            </td>
                            <td>
                                @if(! empty($r['id']))
                                    <div style="display:flex;align-items:center;gap:12px;flex-wrap:nowrap;white-space:nowrap">
                                        <form method="POST" action="{{ route('admin.device_bindings.unbind', ['id' => $r['id']]) }}" style="display:inline">
                                            @csrf
                                            <button type="submit" class="action-orange" onclick="return confirm('Unbind this device?')">Unbind</button>
                                        </form>

                                        <form method="POST" action="{{ route('admin.device_bindings.rebind', ['id' => $r['id']]) }}" style="display:inline;">
                                            @csrf
                                            <select name="employee_id" required style="padding:8px;border:1px solid #cfd8db;border-radius:6px;min-width:90px;max-width:180px;box-sizing:border-box">
                                                <option value="">--select employee--</option>
                                                @if(!empty($employees))
                                                    @foreach($employees as $emp)
                                                        <option value="{{ $emp['employee_id'] }}" @if(($r['employee_id'] ?? '') == $emp['employee_id']) selected @endif>{{ $emp['employee_name'] ?? $emp['eid'] ?? $emp['employee_id'] }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                            <button type="submit" class="action-orange" style="margin-left:8px">Rebind</button>
                                        </form>
                                    </div>
                                @else
                                    <em>No binding id</em>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8">No device bindings found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
