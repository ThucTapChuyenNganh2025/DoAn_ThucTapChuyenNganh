<?php
/**
 * API Lấy tin nhắn của một conversation
 * GET: product_id, other_user_id (người còn lại trong cuộc hội thoại)
 * Optional: last_id (chỉ lấy tin nhắn mới hơn ID này)
 */
session_start();
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config/connect.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Bạn cần đăng nhập', 'require_login' => true]);
    exit;
}

$user_id = (int)$_SESSION['user_id'];
$product_id = isset($_GET['product_id']) ? (int)$_GET['product_id'] : 0;
$other_user_id = isset($_GET['other_user_id']) ? (int)$_GET['other_user_id'] : 0;
$last_id = isset($_GET['last_id']) ? (int)$_GET['last_id'] : 0;

if ($product_id <= 0 || $other_user_id <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Thông tin không hợp lệ']);
    exit;
}

// Lấy thông tin sản phẩm
$prod_sql = "SELECT seller_id, title FROM products WHERE id = ?";
$prod_stmt = $conn->prepare($prod_sql);
$prod_stmt->bind_param('i', $product_id);
$prod_stmt->execute();
$prod_res = $prod_stmt->get_result();

if ($prod_res->num_rows === 0) {
    echo json_encode(['status' => 'error', 'message' => 'Sản phẩm không tồn tại']);
    exit;
}

$product = $prod_res->fetch_assoc();
$product_seller_id = (int)$product['seller_id'];
$prod_stmt->close();

// Xác định buyer và seller
if ($user_id === $product_seller_id) {
    $buyer_id = $other_user_id;
    $seller_id = $user_id;
} else {
    $buyer_id = $user_id;
    $seller_id = $product_seller_id;
}

// Tìm conversation
$conv_sql = "SELECT id, buyer_unread, seller_unread FROM conversations WHERE product_id = ? AND buyer_id = ? AND seller_id = ?";
$conv_stmt = $conn->prepare($conv_sql);
$conv_stmt->bind_param('iii', $product_id, $buyer_id, $seller_id);
$conv_stmt->execute();
$conv_res = $conv_stmt->get_result();

if ($conv_res->num_rows === 0) {
    // Chưa có conversation -> trả về mảng rỗng
    echo json_encode([
        'status' => 'success',
        'messages' => [],
        'conversation_id' => null,
        'product_title' => $product['title']
    ]);
    exit;
}

$conversation = $conv_res->fetch_assoc();
$conversation_id = (int)$conversation['id'];
$conv_stmt->close();

// Lấy tin nhắn
$msg_sql = "SELECT m.id, m.message, m.sender_id, m.is_read, m.created_at, u.name AS sender_name
            FROM messages m
            LEFT JOIN users u ON m.sender_id = u.id
            WHERE m.conversation_id = ?";

$params = [$conversation_id];
$types = 'i';

if ($last_id > 0) {
    $msg_sql .= " AND m.id > ?";
    $params[] = $last_id;
    $types .= 'i';
}

$msg_sql .= " ORDER BY m.created_at ASC, m.id ASC LIMIT 100";

$msg_stmt = $conn->prepare($msg_sql);
$msg_stmt->bind_param($types, ...$params);
$msg_stmt->execute();
$msg_res = $msg_stmt->get_result();

$messages = [];
while ($row = $msg_res->fetch_assoc()) {
    $messages[] = [
        'id' => (int)$row['id'],
        'message' => $row['message'],
        'sender_id' => (int)$row['sender_id'],
        'sender_name' => $row['sender_name'],
        'is_read' => (bool)$row['is_read'],
        'created_at' => $row['created_at'],
        'is_mine' => ((int)$row['sender_id'] === $user_id)
    ];
}
$msg_stmt->close();

// Đánh dấu tin nhắn đã đọc (tin nhắn từ người khác gửi đến)
if (!empty($messages)) {
    $mark_read_sql = "UPDATE messages SET is_read = 1 WHERE conversation_id = ? AND sender_id != ? AND is_read = 0";
    $mark_stmt = $conn->prepare($mark_read_sql);
    $mark_stmt->bind_param('ii', $conversation_id, $user_id);
    $mark_stmt->execute();
    $mark_stmt->close();
    
    // Reset unread count
    if ($user_id === $buyer_id) {
        $reset_sql = "UPDATE conversations SET buyer_unread = 0 WHERE id = ?";
    } else {
        $reset_sql = "UPDATE conversations SET seller_unread = 0 WHERE id = ?";
    }
    $reset_stmt = $conn->prepare($reset_sql);
    $reset_stmt->bind_param('i', $conversation_id);
    $reset_stmt->execute();
    $reset_stmt->close();
}

echo json_encode([
    'status' => 'success',
    'messages' => $messages,
    'conversation_id' => $conversation_id,
    'product_title' => $product['title']
]);
