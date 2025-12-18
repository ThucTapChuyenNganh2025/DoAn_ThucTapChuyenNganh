<?php
/**
 * API Lấy danh sách cuộc hội thoại của user
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

// Lấy tất cả conversations mà user tham gia (là buyer hoặc seller)
$sql = "
SELECT 
    c.id AS conversation_id,
    c.product_id,
    c.buyer_id,
    c.seller_id,
    c.buyer_unread,
    c.seller_unread,
    c.updated_at,
    p.title AS product_title,
    (SELECT filename FROM product_images pi2 WHERE pi2.product_id = p.id ORDER BY pi2.sort_order ASC LIMIT 1) AS product_image,
    buyer.name AS buyer_name,
    seller.name AS seller_name,
    m.message AS last_message,
    m.sender_id AS last_sender_id,
    m.created_at AS last_message_time
FROM conversations c
LEFT JOIN products p ON c.product_id = p.id
LEFT JOIN users buyer ON c.buyer_id = buyer.id
LEFT JOIN users seller ON c.seller_id = seller.id
LEFT JOIN messages m ON c.last_message_id = m.id
WHERE c.buyer_id = ? OR c.seller_id = ?
ORDER BY c.updated_at DESC
LIMIT 50
";

$stmt = $conn->prepare($sql);
$stmt->bind_param('ii', $user_id, $user_id);
$stmt->execute();
$res = $stmt->get_result();

$conversations = [];
while ($row = $res->fetch_assoc()) {
    $is_buyer = ((int)$row['buyer_id'] === $user_id);
    $other_user_id = $is_buyer ? (int)$row['seller_id'] : (int)$row['buyer_id'];
    $other_user_name = $is_buyer ? $row['seller_name'] : $row['buyer_name'];
    $unread_count = $is_buyer ? (int)$row['buyer_unread'] : (int)$row['seller_unread'];
    
    // Xử lý ảnh sản phẩm
    $product_image = 'images/default-product.jpg';
    if (!empty($row['product_image'])) {
        $img_path = __DIR__ . '/../uploads/' . $row['product_image'];
        if (file_exists($img_path)) {
            $product_image = 'uploads/' . $row['product_image'];
        }
    }
    
    $conversations[] = [
        'conversation_id' => (int)$row['conversation_id'],
        'product_id' => (int)$row['product_id'],
        'product_title' => $row['product_title'],
        'product_image' => $product_image,
        'other_user_id' => $other_user_id,
        'other_user_name' => $other_user_name,
        'is_buyer' => $is_buyer,
        'unread_count' => $unread_count,
        'last_message' => $row['last_message'],
        'last_message_time' => $row['last_message_time'],
        'is_last_mine' => ((int)$row['last_sender_id'] === $user_id)
    ];
}
$stmt->close();

// Đếm tổng số tin chưa đọc
$total_sql = "SELECT 
    SUM(CASE WHEN buyer_id = ? THEN buyer_unread ELSE 0 END) +
    SUM(CASE WHEN seller_id = ? THEN seller_unread ELSE 0 END) AS total_unread
FROM conversations WHERE buyer_id = ? OR seller_id = ?";
$total_stmt = $conn->prepare($total_sql);
$total_stmt->bind_param('iiii', $user_id, $user_id, $user_id, $user_id);
$total_stmt->execute();
$total_res = $total_stmt->get_result()->fetch_assoc();
$total_unread = (int)($total_res['total_unread'] ?? 0);
$total_stmt->close();

echo json_encode([
    'status' => 'success',
    'conversations' => $conversations,
    'total_unread' => $total_unread
]);
