<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config/connect.php';

// Get all categories
$sql = "SELECT id, name FROM categories ORDER BY name ASC";
$result = $conn->query($sql);

if (!$result) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Query failed']);
    exit;
}

$categories = [];
while ($row = $result->fetch_assoc()) {
    $categories[] = $row;
}

http_response_code(200);
echo json_encode([
    'status' => 'success',
    'data' => $categories
], JSON_UNESCAPED_UNICODE);

$conn->close();
?>
