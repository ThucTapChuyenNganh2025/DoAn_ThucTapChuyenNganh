<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config/connect.php';

$session_key = session_id();
$stmt = $conn->prepare('SELECT COUNT(*) FROM favorites WHERE session_key = ?');
$stmt->bind_param('s', $session_key);
$stmt->execute();
$stmt->bind_result($count);
$stmt->fetch();
$stmt->close();

echo json_encode(['status' => 'success', 'count' => (int)$count]);

?>