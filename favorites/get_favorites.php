<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['sm_favorites']) || !is_array($_SESSION['sm_favorites'])) {
	$_SESSION['sm_favorites'] = [];
}

$favorites = $_SESSION['sm_favorites'];

// For this simple demo, return minimal product info placeholders
$items = [];
foreach ($favorites as $pid) {
	$items[] = [
		'id' => $pid,
		'name' => 'Sản phẩm ' . htmlspecialchars($pid),
		'title' => 'Sản phẩm ' . htmlspecialchars($pid),
		'price' => '0',
		'image' => '',
	];
}

echo json_encode(['status' => 'success', 'favorites' => $items]);

?>