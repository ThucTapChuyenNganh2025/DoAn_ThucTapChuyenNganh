<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Phải gửi POST.']);
    exit;
}

require_once __DIR__ . '/../config/connect.php';

$user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;

if ($user_id <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Bạn cần đăng nhập.']);
    exit;
}

// Xóa tất cả favorites của user
$stmt = $conn->prepare('DELETE FROM favorites WHERE user_id = ?');
$stmt->bind_param('i', $user_id);
$ok = $stmt->execute();
$deleted = $stmt->affected_rows;
$stmt->close();

if ($ok) {
    echo json_encode(['status' => 'success', 'message' => 'Đã xóa tất cả yêu thích.', 'deleted' => $deleted]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Không thể xóa.']);
}
?>
