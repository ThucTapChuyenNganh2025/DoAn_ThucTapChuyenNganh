<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config/connect.php';

$user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;

if ($user_id <= 0) {
    echo json_encode(['status' => 'success', 'favorites' => []]);
    exit;
}

// Lấy danh sách sản phẩm đã yêu thích kèm thông tin sản phẩm
$sql = "SELECT f.id AS favorite_id, f.product_id, p.title, p.price, p.currency,
               (SELECT filename FROM product_images pi WHERE pi.product_id = p.id ORDER BY pi.sort_order ASC LIMIT 1) AS image
        FROM favorites f
        LEFT JOIN products p ON f.product_id = p.id
        WHERE f.user_id = ?
        ORDER BY f.created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$res = $stmt->get_result();

$items = [];
while ($row = $res->fetch_assoc()) {
    // Xử lý đường dẫn ảnh - filename có thể đã có 'uploads/' hoặc chưa
    $img = $row['image'];
    if ($img) {
        // Nếu đã có 'uploads/' thì giữ nguyên, chưa có thì thêm vào
        if (strpos($img, 'uploads/') !== 0) {
            $img = 'uploads/' . $img;
        }
    } else {
        $img = 'images/default-product.jpg';
    }
    
    $items[] = [
        'product_id' => $row['product_id'],
        'favorite_id' => $row['favorite_id'],
        'title' => $row['title'],
        'price' => $row['price'],
        'currency' => $row['currency'],
        'image' => $img,
    ];
}
$stmt->close();

echo json_encode(['status' => 'success', 'favorites' => $items]);
?>