<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

$tables = ['adhoc_requests','adhoc_request'];
foreach ($tables as $t) {
    echo "-- table: $t\n";
    $exists = Schema::hasTable($t) ? 'yes' : 'no';
    echo "exists: $exists\n";
    if (! Schema::hasTable($t)) continue;
    try {
        $cnt = DB::table($t)->count();
        echo "count: $cnt\n";
    } catch (Throwable $e) {
        echo 'count_err:' . $e->getMessage() . "\n";
    }

    try {
        $q = DB::table($t . ' as a');
        $select = [$t . '.*'];
        $rows = $q->select($select)->limit(5)->get();
        echo "select_rows: " . count($rows) . "\n";
    } catch (Throwable $e) {
        echo 'select_err:' . $e->getMessage() . "\n";
    }

    try {
        $q2 = DB::table($t . ' as a');
        $rows2 = $q2->select('a.*')->limit(5)->get();
        echo "select_a_rows: " . count($rows2) . "\n";
    } catch (Throwable $e) {
        echo 'select_a_err:' . $e->getMessage() . "\n";
    }
}
