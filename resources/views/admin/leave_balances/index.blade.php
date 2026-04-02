@extends('admin_layout')
@section('content')
<div style="padding:18px">
    <h1>Leave Balance Management</h1>
    @if(session('error_message'))
        <div style="color:#d0342a;margin-bottom:10px">{{ session('error_message') }}</div>
    @endif
    <!-- Set Balance -->
    <div class="leave-form" style="margin-bottom:18px">
        <h3>Set Leave Balance</h3>
        <form method="POST" action="{{ route('admin.leave_balances.set') }}">
            @csrf
            <div class="row-grid-3">
                <div class="col">
                    <label>Employee</label>
                    <select name="employee_id" required class="form-control">
                        <option value="">Select Employee</option>
                        @foreach($employees as $e)
                            <option value="{{ $e->employee_id }}">{{ $e->employee_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col">
                    <label>Leave Type</label>
                    <select name="leave_type_id" required class="form-control">
                        <option value="">Select Leave Type</option>
                        @foreach($leave_types as $l)
                            <option value="{{ $l->leave_type_id }}">{{ $l->leave_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col">
                    <label>Max Per Year</label>
                    <input type="number" name="max_leave" required class="form-control">
                </div>
            </div>
            <div style="margin-top:12px">
                <button type="submit" class="btn">Save</button>
            </div>
        </form>
    </div>
    <!-- Adjust Balance -->
    <div class="leave-form" style="margin-bottom:18px">
        <h3>Adjust Leave Balance</h3>
        <form method="POST" action="{{ route('admin.leave_balances.adjust') }}">
            @csrf
            <div class="row-grid-3">
                <div class="col">
                    <label>Employee</label>
                    <select name="employee_id" required class="form-control">
                        <option value="">Select Employee</option>
                        @foreach($employees as $e)
                            <option value="{{ $e->employee_id }}">{{ $e->employee_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col">
                    <label>Leave Type</label>
                    <select name="leave_type_id" required class="form-control">
                        <option value="">Select Leave Type</option>
                        @foreach($leave_types as $l)
                            <option value="{{ $l->leave_type_id }}">{{ $l->leave_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col">
                    <label>Adjustment (+/-)</label>
                    <input type="number" name="adjustment" required class="form-control">
                </div>
            </div>
            <div style="margin-top:12px">
                <button type="submit" class="btn">Apply</button>
            </div>
        </form>
    </div>
    <!-- Reset -->
    <div style="margin-bottom:18px">
        <h3>Reset Yearly</h3>
        <form method="POST" action="{{ route('admin.leave_balances.reset') }}">
            @csrf
            <button type="submit" class="btn" onclick="return confirm('Reset all balances?')">Reset All</button>
        </form>
    </div>
    <!-- Table -->
    <div class="leave-history">
        <div class="table-wrap">
            <h3>Leave Balance List</h3>
            <table class="users">
                <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Leave Type</th>
                        <th>Max</th>
                        <th>Used</th>
                        <th>Remaining</th>
                        <th>Year</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($balances as $b)
                    <tr>
                        <td>{{ $b->employee_name }}</td>
                        <td>{{ $b->leave_name }}</td>
                        <td>{{ $b->max_leave_per_year }}</td>
                        <td>{{ $b->used_leave }}</td>
                        <td>{{ $b->remaining_leave }}</td>
                        <td>{{ $b->year }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
