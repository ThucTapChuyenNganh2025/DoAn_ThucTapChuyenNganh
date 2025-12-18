<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config/connect.php';
@mysqli_select_db($conn, 'webchotot');

$q = isset($_GET['q']) ? trim($_GET['q']) : '';
$cat = isset($_GET['category']) ? trim($_GET['category']) : '';

// ensure posts table exists (best-effort)
@mysqli_query($conn, "CREATE TABLE IF NOT EXISTS `posts` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `session_key` VARCHAR(128) DEFAULT NULL,
  `user_id` INT DEFAULT NULL,
  `title` TEXT,
  `category` VARCHAR(128),
  `price` VARCHAR(64),
  `image` VARCHAR(255),
  `description` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

$sql = 'SELECT id, title, category, price, image, description FROM posts WHERE 1=1 ';
$params = [];
if ($q !== '') {
    $sql .= ' AND (title LIKE ? OR description LIKE ?)';
    $like = '%' . $q . '%';
    $params[] = $like; $params[] = $like;
}
if ($cat !== '') {
    $sql .= ' AND category = ?';
    $params[] = $cat;
}
$sql .= ' ORDER BY created_at DESC LIMIT 100';

$stmt = $conn->prepare($sql);
if ($stmt) {
    if (count($params)) {
        // build types
        $types = str_repeat('s', count($params));
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $res = $stmt->get_result();
    $out = [];
    while ($row = $res->fetch_assoc()) {
        $out[] = $row;
    }
    echo json_encode(['status'=>'success','results'=>$out]);
    $stmt->close();
    exit;
} else {
    echo json_encode(['status'=>'error','message'=>'Lỗi câu truy vấn']);
    exit;
}

?>
