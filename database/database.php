<?php

// Legacy PDO bridge for standalone admin/*.php pages.
// Uses Laravel .env database credentials and exposes $conn.

if (!function_exists('legacy_env_value')) {
    function legacy_env_value(string $key, ?string $default = null): ?string
    {
        $value = getenv($key);
        if ($value !== false) {
            return $value;
        }

        $envPath = dirname(__DIR__) . '/.env';
        if (!is_file($envPath) || !is_readable($envPath)) {
            return $default;
        }

        static $cache = null;
        if ($cache === null) {
            $cache = [];
            $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            if (is_array($lines)) {
                foreach ($lines as $line) {
                    $trimmed = trim($line);
                    if ($trimmed === '' || str_starts_with($trimmed, '#')) {
                        continue;
                    }

                    $parts = explode('=', $line, 2);
                    if (count($parts) !== 2) {
                        continue;
                    }

                    $k = trim($parts[0]);
                    $v = trim($parts[1]);

                    if ((str_starts_with($v, '"') && str_ends_with($v, '"')) || (str_starts_with($v, "'") && str_ends_with($v, "'"))) {
                        $v = substr($v, 1, -1);
                    }

                    $cache[$k] = $v;
                }
            }
        }

        return $cache[$key] ?? $default;
    }
}

$host = legacy_env_value('DB_HOST', '127.0.0.1');
$port = legacy_env_value('DB_PORT', '3306');
$dbname = legacy_env_value('DB_DATABASE', 'attendance_db');
$user = legacy_env_value('DB_USERNAME', 'root');
$pass = legacy_env_value('DB_PASSWORD', '');

$conn = null;

try {
    $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4";
    $conn = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (Throwable $e) {
    $conn = null;
    error_log('Legacy DB connection failed: ' . $e->getMessage());
}
