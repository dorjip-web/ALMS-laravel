@extends('admin_layout')
@section('content')
<div class="container">
    <h2>📊 Staff Attendance Dashboard</h2>
    <div class="cards" style="display:flex; gap:12px; margin-bottom:18px; flex-wrap:nowrap; overflow-x:auto; align-items:center;padding-bottom:6px;">
        <div class="card stat-card" style="flex:0 0 120px; min-width:120px; height:120px; padding:14px; background:#fff; box-shadow:0 4px 10px rgba(0,0,0,0.04); border-radius:10px; display:flex; flex-direction:column; justify-content:center; align-items:center; text-align:center;">
            <h4 style="margin:0 0 8px;color:#334155;font-size:13px">Attendance %</h4>
            <div style="font-size:22px;font-weight:700;color:#0ea5a4">{{ $attendancePercent }}%</div>
        </div>
        <div class="card stat-card" style="flex:0 0 120px; min-width:120px; height:120px; padding:14px; background:#fff; border-radius:10px; display:flex; flex-direction:column; justify-content:center; align-items:center; text-align:center;">
            <h4 style="margin:0 0 8px;color:#334155;font-size:13px">Total Staff</h4>
            <div style="font-size:20px;font-weight:700;color:#0b86a5">{{ $totalStaff ?? 0 }}</div>
        </div>
        <div class="card stat-card" style="flex:0 0 120px; min-width:120px; height:120px; padding:14px; background:#fff; border-radius:10px; display:flex; flex-direction:column; justify-content:center; align-items:center; text-align:center;">
            <h4 style="margin:0 0 8px;color:#334155;font-size:13px">Total Present</h4>
            <div style="font-size:20px;font-weight:700;color:#0b8590">{{ $present }}</div>
        </div>
        <div class="card stat-card" style="flex:0 0 120px; min-width:120px; height:120px; padding:14px; background:#fff; border-radius:10px; display:flex; flex-direction:column; justify-content:center; align-items:center; text-align:center;">
            <h4 style="margin:0 0 8px;color:#334155;font-size:13px">Total Absent</h4>
            <div style="font-size:20px;font-weight:700;color:#ef4444">{{ $absent }}</div>
        </div>
        <div class="card stat-card" style="flex:0 0 120px; min-width:120px; height:120px; padding:14px; background:#fff; border-radius:10px; display:flex; flex-direction:column; justify-content:center; align-items:center; text-align:center;">
            <h4 style="margin:0 0 8px;color:#334155;font-size:13px">On Time</h4>
            <div style="font-size:20px;font-weight:700;color:#16a34a">{{ $onTime }}</div>
        </div>
        <div class="card stat-card" style="flex:0 0 120px; min-width:120px; height:120px; padding:14px; background:#fff; border-radius:10px; display:flex; flex-direction:column; justify-content:center; align-items:center; text-align:center;">
            <h4 style="margin:0 0 8px;color:#334155;font-size:13px">Late</h4>
            <div style="font-size:20px;font-weight:700;color:#f97316">{{ $late }}</div>
        </div>
        <div class="card stat-card" style="flex:0 0 160px; min-width:160px; height:120px; padding:14px; background:#fff; border-radius:10px; display:flex; flex-direction:column; justify-content:center; align-items:center; text-align:center;">
            <h4 style="margin:0 0 8px;color:#334155;font-size:13px">Missing Checkout</h4>
            <div style="font-size:20px;font-weight:700;color:#f59e0b">{{ $missing }}</div>
        </div>
    </div>
    <div class="filter-box" style="background:#1e293b;padding:15px;border-radius:10px;margin-bottom:20px;">
        <form method="GET" class="leave-form row-inline" style="margin-bottom:0;gap:12px;align-items:center;">
            <select name="filter">
                <option value="">All</option>
                <option value="absent" {{ $filter == 'absent' ? 'selected' : '' }}>Absent</option>
                <option value="late" {{ $filter == 'late' ? 'selected' : '' }}>Late</option>
                <option value="missing_checkout" {{ $filter == 'missing_checkout' ? 'selected' : '' }}>Missing Checkout</option>
                <option value="present" {{ $filter == 'present' ? 'selected' : '' }}>Present</option>
            </select>
            <select name="department_id">
                <option value="">All Departments</option>
                @foreach($departments as $department)
                    <option value="{{ $department->department_id }}" {{ $dept == $department->department_id ? 'selected' : '' }}>{{ $department->department_name }}</option>
                @endforeach
            </select>
                <input type="date" name="from_date" value="{{ $from_date ?? '' }}">
                <input type="date" name="date" value="{{ $date }}">
            <button type="submit" class="btn">Apply</button>
        </form>
    </div>
    <div class="leave-history">
        <div class="table-wrap">
            <table class="users">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Designation</th>
                        <th>Department</th>
                        <th>Date</th>
                        <th>Shift Type</th>
                        <th>Check-in</th>
                        <th>Check-in Address</th>
                        <th>Check-in Status</th>
                        <th>Check-out</th>
                        <th>Check-out Address</th>
                        <th>Check-out Status</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($logs as $row)
                    @php
                        $styles = [];
                        if ($row->checkin_status == 'Late') $styles[] = 'background:#ffcccc;';
                        if ($row->checkout_status == 'Missing') $styles[] = 'border-bottom:4px solid #ffc107;';
                        if ($row->checkout_status == 'Complete') $styles[] = 'background:#d4edda;';
                        $class = implode(' ', $styles);
                        $styleAttr = !empty($class) ? 'style="'.$class.'"' : '';
                    @endphp
                    <tr {{ $styleAttr }}>
                        <td>{{ $row->employee_id }}</td>
                        <td>{{ $row->employee_name }}</td>
                        <td>{{ $row->designation ?? '-' }}</td>
                        <td>{{ $row->department_name ?? '-' }}</td>
                        <td>{{ $row->attendance_date }}</td>
                        <td>{{ $row->shift_type ?? '-' }}</td>
                        <td>{{ $row->checkin ?? '-' }}</td>
                        <td>{{ $row->checkin_address ?? '-' }}</td>
                        <td>{{ $row->checkin_status ?? '-' }}</td>
                        <td>{{ $row->checkout ?? 'Not Yet' }}</td>
                        <td>{{ $row->checkout_address ?? '-' }}</td>
                        <td>{{ $row->checkout_status ?? 'Pending' }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
