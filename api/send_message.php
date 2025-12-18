<?php
/**
 * API Gửi tin nhắn
 * POST: product_id, receiver_id, message
 */
session_start();
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config/connect.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Bạn cần đăng nhập để gửi tin nhắn', 'require_login' => true]);
    exit;
}

// Lấy dữ liệu
$product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
$receiver_id = isset($_POST['receiver_id']) ? (int)$_POST['receiver_id'] : 0;
$message = isset($_POST['message']) ? trim($_POST['message']) : '';
$sender_id = (int)$_SESSION['user_id'];

// Validate
if ($product_id <= 0 || $receiver_id <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Thông tin không hợp lệ']);
    exit;
}

if (empty($message)) {
    echo json_encode(['status' => 'error', 'message' => 'Tin nhắn không được để trống']);
    exit;
}

if ($sender_id === $receiver_id) {
    echo json_encode(['status' => 'error', 'message' => 'Không thể gửi tin nhắn cho chính mình']);
    exit;
}

// Giới hạn độ dài tin nhắn
if (mb_strlen($message, 'UTF-8') > 2000) {
    echo json_encode(['status' => 'error', 'message' => 'Tin nhắn quá dài (tối đa 2000 ký tự)']);
    exit;
}

// Lấy thông tin sản phẩm để xác định buyer/seller
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
$product_seller_id = (int)$product['seller_id'];
$prod_stmt->close();

// Xác định buyer và seller trong conversation
// Nếu sender là seller của sản phẩm -> receiver là buyer
// Nếu sender không phải seller -> sender là buyer
if ($sender_id === $product_seller_id) {
    $buyer_id = $receiver_id;
    $seller_id = $sender_id;
} else {
    $buyer_id = $sender_id;
    $seller_id = $product_seller_id;
}

// Tìm hoặc tạo conversation
$conv_sql = "SELECT id FROM conversations WHERE product_id = ? AND buyer_id = ? AND seller_id = ?";
$conv_stmt = $conn->prepare($conv_sql);
$conv_stmt->bind_param('iii', $product_id, $buyer_id, $seller_id);
$conv_stmt->execute();
$conv_res = $conv_stmt->get_result();

if ($conv_res->num_rows > 0) {
    $conversation = $conv_res->fetch_assoc();
    $conversation_id = (int)$conversation['id'];
} else {
    // Tạo conversation mới
    $create_conv_sql = "INSERT INTO conversations (product_id, buyer_id, seller_id) VALUES (?, ?, ?)";
    $create_stmt = $conn->prepare($create_conv_sql);
    $create_stmt->bind_param('iii', $product_id, $buyer_id, $seller_id);
    
    if (!$create_stmt->execute()) {
        echo json_encode(['status' => 'error', 'message' => 'Không thể tạo cuộc hội thoại']);
        exit;
    }
    $conversation_id = (int)$conn->insert_id;
    $create_stmt->close();
}
$conv_stmt->close();

// Thêm tin nhắn
$msg_sql = "INSERT INTO messages (conversation_id, sender_id, message) VALUES (?, ?, ?)";
$msg_stmt = $conn->prepare($msg_sql);
$msg_stmt->bind_param('iis', $conversation_id, $sender_id, $message);

if (!$msg_stmt->execute()) {
    echo json_encode(['status' => 'error', 'message' => 'Không thể gửi tin nhắn']);
    exit;
}

$message_id = (int)$conn->insert_id;
$msg_stmt->close();

// Cập nhật conversation: last_message_id và unread count
if ($sender_id === $buyer_id) {
    // Buyer gửi -> tăng seller_unread
    $update_conv_sql = "UPDATE conversations SET last_message_id = ?, seller_unread = seller_unread + 1, updated_at = NOW() WHERE id = ?";
} else {
    // Seller gửi -> tăng buyer_unread
    $update_conv_sql = "UPDATE conversations SET last_message_id = ?, buyer_unread = buyer_unread + 1, updated_at = NOW() WHERE id = ?";
}

$update_stmt = $conn->prepare($update_conv_sql);
$update_stmt->bind_param('ii', $message_id, $conversation_id);
$update_stmt->execute();
$update_stmt->close();

// Lấy thông tin tin nhắn vừa gửi để trả về
$get_msg_sql = "SELECT m.id, m.message, m.sender_id, m.created_at, u.name AS sender_name 
                FROM messages m 
                LEFT JOIN users u ON m.sender_id = u.id 
                WHERE m.id = ?";
$get_stmt = $conn->prepare($get_msg_sql);
$get_stmt->bind_param('i', $message_id);
$get_stmt->execute();
$msg_result = $get_stmt->get_result()->fetch_assoc();
$get_stmt->close();

echo json_encode([
    'status' => 'success',
    'message' => 'Đã gửi tin nhắn',
    'data' => [
        'id' => $msg_result['id'],
        'message' => $msg_result['message'],
        'sender_id' => $msg_result['sender_id'],
        'sender_name' => $msg_result['sender_name'],
        'created_at' => $msg_result['created_at'],
        'conversation_id' => $conversation_id,
        'is_mine' => true
    ]
]);
