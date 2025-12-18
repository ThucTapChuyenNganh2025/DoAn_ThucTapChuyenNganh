<?php
/**
 * API Gửi báo cáo bài đăng
 * POST: product_id, reason
 */
session_start();
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config/connect.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Bạn cần đăng nhập để báo cáo', 'require_login' => true]);
    exit;
}

// Chỉ chấp nhận POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
    exit;
}

$user_id = (int)$_SESSION['user_id'];
$product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
$reason = isset($_POST['reason']) ? trim($_POST['reason']) : '';

// Validate
if ($product_id <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Sản phẩm không hợp lệ']);
    exit;
}

if (empty($reason) || mb_strlen($reason, 'UTF-8') < 10) {
    echo json_encode(['status' => 'error', 'message' => 'Vui lòng nhập lý do báo cáo (ít nhất 10 ký tự)']);
    exit;
}

// Kiểm tra sản phẩm tồn tại
$prod_sql = "SELECT id, seller_id FROM products WHERE id = ?";
$prod_stmt = $conn->prepare($prod_sql);
$prod_stmt->bind_param('i', $product_id);
$prod_stmt->execute();
$prod_res = $prod_stmt->get_result();

if ($prod_res->num_rows === 0) {
    echo json_encode(['status' => 'error', 'message' => 'Sản phẩm không tồn tại']);
    exit;
}

$product = $prod_res->fetch_assoc();
$prod_stmt->close();

// Không cho phép tự báo cáo sản phẩm của mình
if ($user_id === (int)$product['seller_id']) {
    echo json_encode(['status' => 'error', 'message' => 'Bạn không thể báo cáo sản phẩm của mình']);
    exit;
}

// Kiểm tra đã báo cáo sản phẩm này chưa
$check_sql = "SELECT id FROM reports WHERE product_id = ? AND reporter_id = ?";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param('ii', $product_id, $user_id);
$check_stmt->execute();
$check_res = $check_stmt->get_result();

if ($check_res->num_rows > 0) {
    echo json_encode(['status' => 'error', 'message' => 'Bạn đã báo cáo sản phẩm này rồi']);
    $check_stmt->close();
    exit;
}
$check_stmt->close();

// Tạo báo cáo
$insert_sql = "INSERT INTO reports (product_id, reporter_id, reason, status) VALUES (?, ?, ?, 'pending')";
$insert_stmt = $conn->prepare($insert_sql);
$insert_stmt->bind_param('iis', $product_id, $user_id, $reason);

if ($insert_stmt->execute()) {
    echo json_encode([
        'status' => 'success',
        'message' => 'Cảm ơn bạn đã báo cáo. Chúng tôi sẽ xem xét sớm nhất!',
        'report_id' => $insert_stmt->insert_id
    ]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Lỗi gửi báo cáo']);
}

$insert_stmt->close();
$conn->close();
