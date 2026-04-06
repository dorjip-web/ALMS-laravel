<?php
function env($key, $default = null) {
    $path = __DIR__ . '/../.env';
    if (!file_exists($path)) return $default;
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (!str_contains($line, '=')) continue;
        [$k, $v] = explode('=', $line, 2);
        $k = trim($k);
        $v = trim($v);
        if ($k === $key) {
            // strip surrounding quotes
            if ((str_starts_with($v, '"') && str_ends_with($v, '"')) || (str_starts_with($v, "'") && str_ends_with($v, "'"))) {
                $v = substr($v, 1, -1);
            }
            return $v;
        }
    }
    return $default;
}

$host = env('DB_HOST', '127.0.0.1');
$port = env('DB_PORT', '3306');
$db = env('DB_DATABASE');
$user = env('DB_USERNAME');
$pass = env('DB_PASSWORD');

echo "host=$host port=$port db=$db user=$user\n";
try {
    $dsn = "mysql:host={$host};port={$port};dbname={$db}";
    $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    $stmt = $pdo->query("SHOW TABLES LIKE 'adhoc_requests'");
    $tables = $stmt->fetchAll(PDO::FETCH_NUM);
    echo 'table_exists:' . (count($tables) ? 'yes' : 'no') . "\n";
    if (count($tables)) {
        $cnt = $pdo->query("SELECT COUNT(*) FROM adhoc_requests")->fetchColumn();
        echo "count:$cnt\n";
        $rows = $pdo->query("SELECT * FROM adhoc_requests LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
        foreach ($rows as $r) echo json_encode($r) . "\n";
    }
} catch (PDOException $e) {
    echo 'err:' . $e->getMessage() . "\n";
}
