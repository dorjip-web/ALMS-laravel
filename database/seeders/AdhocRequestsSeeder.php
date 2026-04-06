<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdhocRequestsSeeder extends Seeder
{
    public function run()
    {
        if (! DB::table('adhoc_requests')->count()) {
            DB::table('adhoc_requests')->insert([
                'adhoc_id' => 1,
                'application_id' => null,
                'employee_id' => 1,
                'eid' => null,
                'user_id' => 1,
                'date' => now()->toDateString(),
                'purpose' => 'meeting',
                'remarks' => 'Seeded test adhoc request',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
