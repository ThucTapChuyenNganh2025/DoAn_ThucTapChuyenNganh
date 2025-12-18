<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Phải gửi POST.']);
    exit;
}

require_once __DIR__ . '/../config/connect.php';

$product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;

if ($product_id <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Thiếu product_id.']);
    exit;
}

// Kiểm tra user đã đăng nhập chưa
$user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;

if ($user_id <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Bạn cần đăng nhập để yêu thích sản phẩm.', 'require_login' => true]);
    exit;
}

// Kiểm tra xem đã yêu thích chưa
$check = $conn->prepare('SELECT id FROM favorites WHERE user_id = ? AND product_id = ? LIMIT 1');
$check->bind_param('ii', $user_id, $product_id);
$check->execute();
$check->store_result();
$exists = $check->num_rows > 0;
$check->close();

if ($exists) {
    // Đã có -> xóa (bỏ yêu thích)
    $stmt = $conn->prepare('DELETE FROM favorites WHERE user_id = ? AND product_id = ?');
    $stmt->bind_param('ii', $user_id, $product_id);
    $stmt->execute();
    $stmt->close();
    
    // Đếm số yêu thích còn lại
    $cstmt = $conn->prepare('SELECT COUNT(*) FROM favorites WHERE user_id = ?');
    $cstmt->bind_param('i', $user_id);
    $cstmt->execute();
    $cstmt->bind_result($count);
    $cstmt->fetch();
    $cstmt->close();
    
    echo json_encode(['status' => 'success', 'action' => 'removed', 'message' => 'Đã bỏ yêu thích.', 'count' => (int)$count]);
    exit;
} else {
    // Chưa có -> thêm yêu thích
    $stmt = $conn->prepare('INSERT INTO favorites (user_id, product_id) VALUES (?, ?)');
    $stmt->bind_param('ii', $user_id, $product_id);
    $ok = $stmt->execute();
    $stmt->close();
    
    // Đếm số yêu thích
    $cstmt = $conn->prepare('SELECT COUNT(*) FROM favorites WHERE user_id = ?');
    $cstmt->bind_param('i', $user_id);
    $cstmt->execute();
    $cstmt->bind_result($count);
    $cstmt->fetch();
    $cstmt->close();
    
    if ($ok) {
        echo json_encode(['status' => 'success', 'action' => 'added', 'message' => 'Đã thêm vào yêu thích.', 'count' => (int)$count]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Không thể thêm yêu thích.']);
    }
    exit;
}
?>