<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    $table = 'adhoc_requests';
    if (! DB::getSchemaBuilder()->hasTable($table)) {
        echo "table_missing\n";
        exit(1);
    }
    $id = DB::table($table)->insertGetId([
        'adhoc_id' => null,
        'application_id' => null,
        'employee_id' => null,
        'eid' => 'TEST-EID',
        'user_id' => null,
        'date' => date('Y-m-d'),
        'purpose' => 'meeting',
        'remarks' => 'Inserted by script for admin testing',
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    echo "inserted_id:" . $id . "\n";
} catch (Throwable $e) {
    echo 'err:' . $e->getMessage() . "\n";
}
