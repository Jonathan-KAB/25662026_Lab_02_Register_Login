<?php
require_once __DIR__ . '/../classes/brand_class.php';
session_start();

header('Content-Type: application/json');

$user_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;
if ($user_id <= 0) {
	echo json_encode([]);
	exit;
}

$brand = new Brand();
$items = $brand->fetchBrandsByUser($user_id);
if ($items === false) {
	$err = (isset($brand->db) && $brand->db) ? mysqli_error($brand->db) : '';
	echo json_encode(['status' => 'error', 'message' => 'DB fetch failed', 'debug' => $err]);
} else {
	echo json_encode($items);
}