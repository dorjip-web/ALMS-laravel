<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveType extends Model
{
    protected $table = 'leave_type';
    protected $primaryKey = 'leave_type_id';
    public $incrementing = true;
    protected $keyType = 'int';
    protected $fillable = [
        'leave_name',
        'leave_code',
        'description',
        'max_per_year',
        'status',
    ];
}
