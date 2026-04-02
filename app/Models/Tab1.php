<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tab1 extends Model
{
    protected $table = 'attendance';
    protected $primaryKey = 'attendance_id';
    public $timestamps = false;

    protected $fillable = [
        'employee_id',
        'attendance_date',
        'checkin',
        'checkout',
        'checkin_status',
        'checkout_status',
        'checkout_address',
        'late_reason'
    ];
}
