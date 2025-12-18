<?php
/**
 * API Gửi đánh giá sản phẩm/người bán
 * POST: product_id, rating (1-5), comment (optional)
 */
session_start();
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config/connect.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Bạn cần đăng nhập để đánh giá', 'require_login' => true]);
    exit;
}

// Chỉ chấp nhận POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
    exit;
}

$user_id = (int)$_SESSION['user_id'];
$product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
$rating = isset($_POST['rating']) ? (int)$_POST['rating'] : 0;
$comment = isset($_POST['comment']) ? trim($_POST['comment']) : '';

// Validate
if ($product_id <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Sản phẩm không hợp lệ']);
    exit;
}

if ($rating < 1 || $rating > 5) {
    echo json_encode(['status' => 'error', 'message' => 'Đánh giá phải từ 1-5 sao']);
    exit;
}

// Lấy thông tin sản phẩm
$prod_sql = "SELECT seller_id FROM products WHERE id = ?";
$prod_stmt = $conn->prepare($prod_sql);
$prod_stmt->bind_param('i', $product_id);
$prod_stmt->execute();
$prod_res = $prod_stmt->get_result();

if ($prod_res->num_rows === 0) {
    echo json_encode(['status' => 'error', 'message' => 'Sản phẩm không tồn tại']);
    exit;
}

$product = $prod_res->fetch_assoc();
$seller_id = (int)$product['seller_id'];
$prod_stmt->close();

// Không cho phép tự đánh giá
if ($user_id === $seller_id) {
    echo json_encode(['status' => 'error', 'message' => 'Bạn không thể tự đánh giá sản phẩm của mình']);
    exit;
}

// Kiểm tra đã đánh giá chưa
$check_sql = "SELECT id FROM ratings WHERE product_id = ? AND rater_user_id = ?";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param('ii', $product_id, $user_id);
$check_stmt->execute();
$check_res = $check_stmt->get_result();

if ($check_res->num_rows > 0) {
    // Cập nhật đánh giá cũ
    $existing = $check_res->fetch_assoc();
    $update_sql = "UPDATE ratings SET rating = ?, comment = ?, created_at = CURRENT_TIMESTAMP WHERE id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param('isi', $rating, $comment, $existing['id']);
    
    if ($update_stmt->execute()) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Đã cập nhật đánh giá',
            'action' => 'updated'
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Lỗi cập nhật đánh giá']);
    }
    $update_stmt->close();
} else {
    // Tạo đánh giá mới
    $insert_sql = "INSERT INTO ratings (product_id, rated_user_id, rater_user_id, rating, comment) VALUES (?, ?, ?, ?, ?)";
    $insert_stmt = $conn->prepare($insert_sql);
    $insert_stmt->bind_param('iiiis', $product_id, $seller_id, $user_id, $rating, $comment);
    
    if ($insert_stmt->execute()) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Đã gửi đánh giá thành công',
            'action' => 'created',
            'rating_id' => $insert_stmt->insert_id
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Lỗi gửi đánh giá']);
    }
    $insert_stmt->close();
}

$check_stmt->close();
$conn->close();
