@extends('admin_layout')
@section('content')
<div style="padding:18px">
    <h1>Leave Balance Management</h1>
    @if(session('error_message'))
        <div style="color:#d0342a;margin-bottom:10px">{{ session('error_message') }}</div>
    @endif
    <!-- Set / Adjust Balance links -->
    <div style="display:flex;gap:28px;align-items:center;margin-bottom:8px;flex-wrap:wrap">
        <h3 style="margin:0"><a href="#" class="toggle-form" data-target="set-balance-form">Set Leave Balance</a></h3>
        <h3 style="margin:0"><a href="#" class="toggle-form" data-target="adjust-balance-form">Adjust Leave Balance</a></h3>
    </div>
    <div id="set-balance-form" class="leave-form" style="margin-bottom:18px;display:none">
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
                <a href="#" class="btn hide-form" data-target="set-balance-form" style="background:#fff;border:1px solid #cfd8db;color:#333;margin-left:8px">Cancel</a>
            </div>
        </form>
    </div>
    <!-- Adjust Balance -->
    <div id="adjust-balance-form" class="leave-form" style="margin-bottom:18px;display:none">
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
                <a href="#" class="btn hide-form" data-target="adjust-balance-form" style="background:#fff;border:1px solid #cfd8db;color:#333;margin-left:8px">Cancel</a>
            </div>
        </form>
    </div>
    <!-- Reset -->
        <!-- Reset Yearly + Leave Balance List header (single row) -->
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:18px;gap:18px;flex-wrap:wrap">
            <div style="flex:1;min-width:220px">
                <h3 style="margin:0">Leave Balance List</h3>
            </div>
            <div style="flex:0 0 auto">
                <div style="font-weight:700;margin-bottom:6px">Reset Yearly</div>
                <form method="POST" action="{{ route('admin.leave_balances.reset') }}" style="display:inline-block">
                    @csrf
                    <button type="submit" class="btn" onclick="return confirm('Reset all balances?')">Reset All</button>
                </form>
            </div>
        </div>
    <!-- Table -->
    <div class="leave-history">
        <div class="table-wrap">
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

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function(){
        // Toggle form display when header link clicked
        document.querySelectorAll('.toggle-form').forEach(el=>{
            el.addEventListener('click', function(e){
                e.preventDefault();
                const target = this.getAttribute('data-target');
                if(!target) return;
                const node = document.getElementById(target);
                if(!node) return;
                node.style.display = (node.style.display === 'none' || node.style.display === '') ? 'block' : 'none';
                if(node.style.display === 'block') node.scrollIntoView({behavior:'smooth', block:'start'});
            });
        });

        // Hide form when cancel clicked
        document.querySelectorAll('.hide-form').forEach(btn=>{
            btn.addEventListener('click', function(e){
                e.preventDefault();
                const t = this.getAttribute('data-target');
                if(t && document.getElementById(t)) document.getElementById(t).style.display = 'none';
            });
        });
    });
</script>
@endsection
