<?php
require __DIR__ . '/../database/database.php';
if (! $conn) {
    echo "DB connection failed\n";
    exit(1);
}
try {
    $stmt = $conn->query('DESCRIBE attendance');
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($rows as $r) {
        echo $r['Field'] . ': ' . $r['Type'] . ' ' . ($r['Null']=='YES' ? 'NULL' : 'NOT NULL') . PHP_EOL;
    }
} catch (Throwable $e) {
    echo 'Error: ' . $e->getMessage() . PHP_EOL;
}
