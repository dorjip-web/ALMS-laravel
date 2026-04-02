<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $table = 'tab1';
    protected $primaryKey = 'employee_id';
    public $timestamps = false;
    protected $fillable = [
        'employee_id', 'employee_name', 'status'
    ];
}
