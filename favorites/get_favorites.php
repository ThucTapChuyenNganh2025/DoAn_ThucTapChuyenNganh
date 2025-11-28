<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config/connect.php';
@mysqli_select_db($conn, 'choto');

$session_key = session_id();
$user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : null;

// ensure table exists (best-effort)
@mysqli_query($conn, "CREATE TABLE IF NOT EXISTS `favorites` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `session_key` VARCHAR(128) DEFAULT NULL,
  `user_id` INT DEFAULT NULL,
  `product_id` VARCHAR(191) NOT NULL,
  `title` TEXT,
  `price` VARCHAR(64),
  `image` VARCHAR(255),
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

// Return a stable unique favorite per normalized product_id and include a representative favorite row id (min id)
if ($user_id) {
  // Merge both user-bound and session-bound rows to avoid duplicates after login
  $stmt = $conn->prepare('SELECT TRIM(product_id) AS pid, MIN(id) AS favorite_id, MAX(title) AS title, MAX(price) AS price, MAX(image) AS image
              FROM favorites WHERE user_id = ? OR session_key = ?
              GROUP BY pid
              ORDER BY MIN(created_at) DESC');
  $stmt->bind_param('is', $user_id, $session_key);
} else {
  $stmt = $conn->prepare('SELECT TRIM(product_id) AS pid, MIN(id) AS favorite_id, MAX(title) AS title, MAX(price) AS price, MAX(image) AS image
              FROM favorites WHERE session_key = ?
              GROUP BY pid
              ORDER BY MIN(created_at) DESC');
  $stmt->bind_param('s', $session_key);
}

$items = [];
if ($stmt && $stmt->execute()) {
  $res = $stmt->get_result();
  if ($res) {
    while ($row = $res->fetch_assoc()) {
      $items[] = [
        'product_id' => $row['pid'],
        'favorite_id' => $row['favorite_id'],
        'title' => $row['title'],
        'price' => $row['price'],
        'image' => $row['image'],
      ];
    }
  }
}
if ($stmt) { $stmt->close(); }

echo json_encode(['status' => 'success', 'favorites' => $items]);

?>