<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['sm_favorites']) || !is_array($_SESSION['sm_favorites'])) {
	$_SESSION['sm_favorites'] = [];
}

echo json_encode(['status' => 'success', 'count' => count($_SESSION['sm_favorites'])]);

?>