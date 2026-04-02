<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LeaveType;

class LeaveTypeSeeder extends Seeder
{
    public function run()
    {
        LeaveType::truncate(); // Clear table before seeding

        LeaveType::create([
            'leave_type_id' => 1,
            'leave_name' => 'Annual leave',
            'leave_code' => 'AL',
            'description' => 'yearly vacation leave',
            'max_per_year' => 21,
            'status' => 'active',
            'created_at' => '2026-03-23 20:17:54',
            'updated_at' => '2026-03-23 20:17:54',
        ]);
        LeaveType::create([
            'leave_type_id' => 2,
            'leave_name' => 'Bereavement leave',
            'leave_code' => 'BL',
            'description' => 'family death leave',
            'max_per_year' => 0,
            'status' => 'active',
            'created_at' => '2026-03-23 20:17:54',
            'updated_at' => '2026-03-23 20:17:54',
        ]);
        LeaveType::create([
            'leave_type_id' => 3,
            'leave_name' => 'Medical leave',
            'leave_code' => 'ML',
            'description' => 'Medical condition leave',
            'max_per_year' => 0,
            'status' => 'active',
            'created_at' => '2026-03-23 20:17:54',
            'updated_at' => '2026-03-23 20:17:54',
        ]);
        LeaveType::create([
            'leave_type_id' => 4,
            'leave_name' => 'Maternity leave',
            'leave_code' => 'MTL',
            'description' => 'child birth leave for mother',
            'max_per_year' => 0,
            'status' => 'active',
            'created_at' => '2026-03-23 20:17:54',
            'updated_at' => '2026-03-23 20:17:54',
        ]);
        LeaveType::create([
            'leave_type_id' => 5,
            'leave_name' => 'Paternity leave',
            'leave_code' => 'PL',
            'description' => 'childbirth leave for father',
            'max_per_year' => 0,
            'status' => 'active',
            'created_at' => '2026-03-23 20:17:54',
            'updated_at' => '2026-03-23 20:17:54',
        ]);
        LeaveType::create([
            'leave_type_id' => 6,
            'leave_name' => 'Others',
            'leave_code' => 'OTH',
            'description' => 'other special leave',
            'max_per_year' => 0,
            'status' => 'active',
            'created_at' => '2026-03-23 20:17:54',
            'updated_at' => '2026-03-23 20:17:54',
        ]);
    }
}
