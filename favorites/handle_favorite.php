<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	echo json_encode(['status' => 'error', 'message' => 'Phải gửi POST.']);
	exit;
}

require_once __DIR__ . '/../config/connect.php';

// prefer choto DB when present
@mysqli_select_db($conn, 'choto');

$product_id = isset($_POST['product_id']) ? trim((string)$_POST['product_id']) : '';
$title = isset($_POST['title']) ? trim((string)$_POST['title']) : '';
$price = isset($_POST['price']) ? trim((string)$_POST['price']) : '';
$image = isset($_POST['image']) ? trim((string)$_POST['image']) : '';
$action_request = isset($_POST['action']) ? trim((string)$_POST['action']) : '';

if ($product_id === '') {
	echo json_encode(['status' => 'error', 'message' => 'Thiếu product_id.']);
	exit;
}

$session_key = session_id();
$user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : null;

// ensure favorites table exists (best-effort)
$create_sql = "CREATE TABLE IF NOT EXISTS `favorites` (
	`id` INT AUTO_INCREMENT PRIMARY KEY,
	`session_key` VARCHAR(128) DEFAULT NULL,
	`user_id` INT DEFAULT NULL,
	`product_id` VARCHAR(191) NOT NULL,
	`title` TEXT,
	`price` VARCHAR(64),
	`image` VARCHAR(255),
	`created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	UNIQUE KEY `uniq_session_product` (`session_key`, `product_id`),
	UNIQUE KEY `uniq_user_product` (`user_id`, `product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
@mysqli_query($conn, $create_sql);

if ($action_request === 'remove') {
	if ($user_id) {
		$stmt = $conn->prepare('DELETE FROM favorites WHERE user_id = ? AND product_id = ?');
		$stmt->bind_param('is', $user_id, $product_id);
	} else {
		$stmt = $conn->prepare('DELETE FROM favorites WHERE session_key = ? AND product_id = ?');
		$stmt->bind_param('ss', $session_key, $product_id);
	}
	$stmt->execute();
	$stmt->close();

	if ($user_id) {
		$cstmt = $conn->prepare('SELECT COUNT(*) FROM favorites WHERE user_id = ?');
		$cstmt->bind_param('i', $user_id);
	} else {
		$cstmt = $conn->prepare('SELECT COUNT(*) FROM favorites WHERE session_key = ?');
		$cstmt->bind_param('s', $session_key);
	}
	$cstmt->execute();
	$cstmt->bind_result($count);
	$cstmt->fetch();
	$cstmt->close();

	echo json_encode(['status' => 'success', 'action' => 'removed', 'message' => 'Đã bỏ yêu thích.', 'count' => (int)$count]);
	exit;
}

// toggle
$check = null;
if ($user_id) {
	$check = $conn->prepare('SELECT id FROM favorites WHERE user_id = ? AND product_id = ? LIMIT 1');
	$check->bind_param('is', $user_id, $product_id);
} else {
	$check = $conn->prepare('SELECT id FROM favorites WHERE session_key = ? AND product_id = ? LIMIT 1');
	$check->bind_param('ss', $session_key, $product_id);
}
$check->execute();
$check->store_result();
$exists = $check->num_rows > 0;
$check->close();

if ($exists) {
	if ($user_id) {
		$stmt = $conn->prepare('DELETE FROM favorites WHERE user_id = ? AND product_id = ?');
		$stmt->bind_param('is', $user_id, $product_id);
	} else {
		$stmt = $conn->prepare('DELETE FROM favorites WHERE session_key = ? AND product_id = ?');
		$stmt->bind_param('ss', $session_key, $product_id);
	}
	$stmt->execute();
	$stmt->close();

	if ($user_id) {
		$cstmt = $conn->prepare('SELECT COUNT(*) FROM favorites WHERE user_id = ?');
		$cstmt->bind_param('i', $user_id);
	} else {
		$cstmt = $conn->prepare('SELECT COUNT(*) FROM favorites WHERE session_key = ?');
		$cstmt->bind_param('s', $session_key);
	}
	$cstmt->execute();
	$cstmt->bind_result($count);
	$cstmt->fetch();
	$cstmt->close();

	echo json_encode(['status' => 'success', 'action' => 'removed', 'message' => 'Đã bỏ yêu thích.', 'count' => (int)$count]);
	exit;
} else {
	if ($user_id) {
		// Clean up any session-bound duplicate for this product when user is logged in
		$cleanup = $conn->prepare('DELETE FROM favorites WHERE session_key = ? AND product_id = ?');
		$cleanup->bind_param('ss', $session_key, $product_id);
		$cleanup->execute();
		$cleanup->close();
		$ist = $conn->prepare('INSERT INTO favorites (user_id, session_key, product_id, title, price, image) VALUES (?, ?, ?, ?, ?, ?)');
		$ist->bind_param('isssss', $user_id, $session_key, $product_id, $title, $price, $image);
	} else {
		$ist = $conn->prepare('INSERT INTO favorites (session_key, product_id, title, price, image) VALUES (?, ?, ?, ?, ?)');
		$ist->bind_param('sssss', $session_key, $product_id, $title, $price, $image);
	}
	$ok = $ist->execute();
	$ist->close();

	if ($user_id) {
		$cstmt = $conn->prepare('SELECT COUNT(*) FROM favorites WHERE user_id = ?');
		$cstmt->bind_param('i', $user_id);
	} else {
		$cstmt = $conn->prepare('SELECT COUNT(*) FROM favorites WHERE session_key = ?');
		$cstmt->bind_param('s', $session_key);
	}
	$cstmt->execute();
	$cstmt->bind_result($count);
	$cstmt->fetch();
	$cstmt->close();

	if ($ok) {
		echo json_encode(['status' => 'success', 'action' => 'added', 'message' => 'Đã thêm vào yêu thích.', 'count' => (int)$count]);
		exit;
	} else {
		echo json_encode(['status' => 'error', 'message' => 'Không thể thêm yêu thích.']);
		exit;
	}
}

?>