<?php
require __DIR__ . '/../database/database.php';
if (! $conn) { echo "DB connection failed\n"; exit(1); }
try {
    $sql = "UPDATE attendance SET checkout = NOW(), checkout_address = 'test', checkout_status = 'complete' WHERE attendance_id = 36";
    $affected = $conn->exec($sql);
    echo "Affected rows: " . $affected . PHP_EOL;
} catch (Throwable $e) {
    echo 'Error: ' . $e->getMessage() . PHP_EOL;
}
