<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config/connect.php';
// Use the connection configured in config/connect.php (no hardcoded DB name)
$out = [];
$sql = "SELECT c.id, c.name, c.slug, COUNT(p.id) AS product_count
        FROM categories c
        LEFT JOIN products p ON p.category_id = c.id AND p.status = 'approved'
        GROUP BY c.id, c.name, c.slug
        ORDER BY c.name ASC";
if ($stmt = $conn->prepare($sql)) {
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            // ensure count is integer
            $row['product_count'] = isset($row['product_count']) ? (int)$row['product_count'] : 0;
            $out[] = $row;
        }
        echo json_encode(['status' => 'success', 'categories' => $out]);
        exit;
    } else {
        echo json_encode(['status' => 'error', 'message' => $stmt->error, 'categories' => []]);
        exit;
    }
} else {
    echo json_encode(['status' => 'error', 'message' => $conn->error, 'categories' => []]);
    exit;
}

?>
