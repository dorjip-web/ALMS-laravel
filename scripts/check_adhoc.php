<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $exists = Illuminate\Support\Facades\DB::select("SHOW TABLES LIKE 'adhoc_requests'");
    echo "table_exists:" . (count($exists) ? 'yes' : 'no') . "\n";
    if (count($exists)) {
        $count = Illuminate\Support\Facades\DB::table('adhoc_requests')->count();
        echo "count:" . $count . "\n";
        $rows = Illuminate\Support\Facades\DB::table('adhoc_requests')->limit(5)->get();
        foreach ($rows as $r) {
            echo json_encode((array)$r) . "\n";
        }
    }
} catch (Throwable $e) {
    echo 'err:' . $e->getMessage() . "\n";
}
