<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config/connect.php';
// ensure we use the expected database name
@mysqli_select_db($conn, 'webchotot');

$res = $conn->query("SELECT id, province, district FROM locations ORDER BY province ASC, district ASC LIMIT 1000");
$out = [];
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $out[] = $row;
    }
}

echo json_encode(['status' => 'success', 'locations' => $out]);
exit;

?>
