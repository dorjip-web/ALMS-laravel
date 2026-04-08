<?php
$pdo = new PDO('mysql:host=127.0.0.1;dbname=attendance_db;charset=utf8mb4', 'root', "Dorji@NTMH1798");
$stmt = $pdo->query("SELECT * FROM attendance WHERE attendance_date = '2026-04-09'");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($rows, JSON_PRETTY_PRINT);
