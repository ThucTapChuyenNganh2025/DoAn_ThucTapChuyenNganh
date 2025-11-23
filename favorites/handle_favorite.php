<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	echo json_encode(['status' => 'error', 'message' => 'Phải gửi POST.']);
	exit;
}

$product_id = isset($_POST['product_id']) ? trim((string)$_POST['product_id']) : '';
$product_id = (string)$product_id;
$action_request = isset($_POST['action']) ? trim((string)$_POST['action']) : '';
if ($product_id === '') {
	echo json_encode(['status' => 'error', 'message' => 'Thiếu product_id.']);
	exit;
}

if (!isset($_SESSION['sm_favorites']) || !is_array($_SESSION['sm_favorites'])) {
	$_SESSION['sm_favorites'] = [];
}

$idx = array_search($product_id, $_SESSION['sm_favorites'], true);

// If explicit action requested
if ($action_request === 'remove') {
	if ($idx !== false) {
		unset($_SESSION['sm_favorites'][$idx]);
		$_SESSION['sm_favorites'] = array_values($_SESSION['sm_favorites']);
	}
	$action = 'removed';
	$message = 'Đã bỏ yêu thích.';
} else {
	if ($idx === false) {
		// add
		$_SESSION['sm_favorites'][] = $product_id;
		$action = 'added';
		$message = 'Đã thêm vào yêu thích.';
	} else {
		// toggle remove
		unset($_SESSION['sm_favorites'][$idx]);
		$_SESSION['sm_favorites'] = array_values($_SESSION['sm_favorites']);
		$action = 'removed';
		$message = 'Đã bỏ yêu thích.';
	}
}

echo json_encode([
	'status' => 'success',
	'action' => $action,
	'message' => $message,
	'count' => count($_SESSION['sm_favorites']),
]);

?>