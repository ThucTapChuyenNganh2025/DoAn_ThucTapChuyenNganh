<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Phải gửi POST.']);
    exit;
}

require_once __DIR__ . '/../config/connect.php';

$title = isset($_POST['title']) ? trim((string)$_POST['title']) : '';
$category = isset($_POST['category']) ? trim((string)$_POST['category']) : '';
$price = isset($_POST['price']) ? trim((string)$_POST['price']) : '';
$image = isset($_POST['image']) ? trim((string)$_POST['image']) : '';
$description = isset($_POST['description']) ? trim((string)$_POST['description']) : '';

if ($title === '' || $price === '') {
    echo json_encode(['status' => 'error', 'message' => 'Thiếu tiêu đề hoặc giá.']);
    exit;
}

// Create posts table if not exists
$create = "CREATE TABLE IF NOT EXISTS `posts` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `session_key` VARCHAR(128) DEFAULT NULL,
  `title` TEXT,
  `category` VARCHAR(128),
  `price` VARCHAR(64),
  `image` VARCHAR(255),
  `description` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
@mysqli_query($conn, $create);

$session_key = session_id();
$stmt = $conn->prepare('INSERT INTO posts (session_key, title, category, price, image, description) VALUES (?, ?, ?, ?, ?, ?)');
$stmt->bind_param('ssssss', $session_key, $title, $category, $price, $image, $description);
$ok = $stmt->execute();
$stmt->close();

if ($ok) {
    echo json_encode(['status' => 'success', 'message' => 'Đăng tin thành công.']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Lưu tin thất bại.']);
}

?>
