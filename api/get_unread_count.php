<?php
/**
 * API Lấy số tin nhắn chưa đọc của user
 */
session_start();
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config/connect.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'count' => 0, 'require_login' => true]);
    exit;
}

$user_id = (int)$_SESSION['user_id'];

// Đếm tổng số tin chưa đọc
$sql = "SELECT 
    SUM(CASE WHEN buyer_id = ? THEN buyer_unread ELSE 0 END) +
    SUM(CASE WHEN seller_id = ? THEN seller_unread ELSE 0 END) AS total_unread
FROM conversations 
WHERE buyer_id = ? OR seller_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param('iiii', $user_id, $user_id, $user_id, $user_id);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();
$total_unread = (int)($result['total_unread'] ?? 0);
$stmt->close();

// Lấy tin nhắn mới nhất chưa đọc (để hiển thị preview)
$latest_msg = null;
$latest_sql = "
SELECT 
    m.message,
    m.created_at,
    u.name AS sender_name,
    p.title AS product_title,
    c.product_id,
    CASE WHEN c.buyer_id = ? THEN c.seller_id ELSE c.buyer_id END AS other_user_id
FROM messages m
JOIN conversations c ON m.conversation_id = c.id
JOIN users u ON m.sender_id = u.id
JOIN products p ON c.product_id = p.id
WHERE m.sender_id != ?
  AND m.is_read = 0
  AND (c.buyer_id = ? OR c.seller_id = ?)
ORDER BY m.created_at DESC
LIMIT 1
";

$latest_stmt = $conn->prepare($latest_sql);
$latest_stmt->bind_param('iiii', $user_id, $user_id, $user_id, $user_id);
$latest_stmt->execute();
$latest_res = $latest_stmt->get_result();

if ($latest_res->num_rows > 0) {
    $row = $latest_res->fetch_assoc();
    $latest_msg = [
        'sender_name' => $row['sender_name'],
        'message' => mb_substr($row['message'], 0, 50, 'UTF-8') . (mb_strlen($row['message'], 'UTF-8') > 50 ? '...' : ''),
        'product_title' => $row['product_title'],
        'product_id' => $row['product_id'],
        'other_user_id' => $row['other_user_id'],
        'created_at' => $row['created_at']
    ];
}
$latest_stmt->close();

echo json_encode([
    'status' => 'success',
    'count' => $total_unread,
    'latest' => $latest_msg
]);
