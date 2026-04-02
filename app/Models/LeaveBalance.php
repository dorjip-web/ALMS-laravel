<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveBalance extends Model
{
    protected $table = 'leave_balance';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $fillable = [
        'employee_id', 'leave_type_id', 'year', 'max_leave_per_year', 'used_leave', 'remaining_leave'
    ];
}
