<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;

try {
    if (Schema::hasTable('adhoc_requests')) {
        Schema::dropIfExists('adhoc_requests');
        echo "Dropped table adhoc_requests\n";
    } else {
        echo "adhoc_requests table does not exist\n";
    }
} catch (Throwable $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
