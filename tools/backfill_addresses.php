<?php
require __DIR__ . '/../database/database.php';
if (! $conn) { echo "DB connection failed\n"; exit(1); }

function reverse_geocode($lat, $lon) {
    $url = 'https://nominatim.openstreetmap.org/reverse?format=jsonv2&addressdetails=1&lat=' . rawurlencode($lat) . '&lon=' . rawurlencode($lon);
    $opts = [
        "http" => [
            "method" => "GET",
            "header" => "User-Agent: EmployeeAttendanceSystem/1.0\r\n",
            "timeout" => 10,
        ]
    ];
    $context = stream_context_create($opts);
    $body = @file_get_contents($url, false, $context);
    if (! $body) return '';
    $data = json_decode($body, true);
    if (! $data || empty($data['address'])) return '';
    $address = $data['address'];
    $building = $data['name'] ?? $address['hospital'] ?? $address['amenity'] ?? $address['building'] ?? '';
    $road = $address['road'] ?? '';
    $city = $address['city'] ?? $address['town'] ?? $address['village'] ?? $address['county'] ?? '';
    $country = $address['country'] ?? '';
    $parts = array_filter([$building, $road, $city, $country]);
    return implode(', ', $parts);
}

// Select rows where either address column contains a nominatim URL
$limit = 100; // safe batch size
$sql = "SELECT attendance_id, checkin_address, checkout_address FROM attendance WHERE (checkin_address LIKE '%nominatim.openstreetmap.org/reverse%' OR checkout_address LIKE '%nominatim.openstreetmap.org/reverse%') LIMIT :limit";
$stmt = $conn->prepare($sql);
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($rows)) {
    echo "No rows to backfill.\n";
    exit(0);
}

$updateSql = "UPDATE attendance SET checkin_address = :checkin, checkout_address = :checkout WHERE attendance_id = :id";
$upStmt = $conn->prepare($updateSql);

foreach ($rows as $r) {
    $id = $r['attendance_id'];
    $checkin = $r['checkin_address'];
    $checkout = $r['checkout_address'];

    if (str_contains($checkin, 'nominatim.openstreetmap.org/reverse')) {
        $parts = parse_url($checkin);
        $newCheckin = $checkin;
        if (! empty($parts['query'])) {
            parse_str($parts['query'], $q);
            if (! empty($q['lat']) && ! empty($q['lon'])) {
                $name = reverse_geocode($q['lat'], $q['lon']);
                if ($name !== '') {
                    $newCheckin = $name;
                }
            }
        }
    } else {
        $newCheckin = $checkin;
    }

    if (str_contains($checkout, 'nominatim.openstreetmap.org/reverse')) {
        $parts = parse_url($checkout);
        $newCheckout = $checkout;
        if (! empty($parts['query'])) {
            parse_str($parts['query'], $q);
            if (! empty($q['lat']) && ! empty($q['lon'])) {
                $name = reverse_geocode($q['lat'], $q['lon']);
                if ($name !== '') {
                    $newCheckout = $name;
                }
            }
        }
    } else {
        $newCheckout = $checkout;
    }

    $upStmt->execute([':checkin' => $newCheckin, ':checkout' => $newCheckout, ':id' => $id]);
    echo "Updated attendance_id={$id}\n";
    // be polite to Nominatim: sleep 1 second between requests
    sleep(1);
}

echo "Backfill complete. Processed " . count($rows) . " rows.\n";
