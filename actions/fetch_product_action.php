<?php
// Enable verbose errors during debugging - remove or lower in production
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../controllers/product_controller.php';
header('Content-Type: application/json');

// If id passed, return a single product
if (isset($_GET['id'])) {
	$id = (int)$_GET['id'];
	if ($id <= 0) {
		echo json_encode(['status' => 'error', 'message' => 'Invalid id']);
		exit;
	}
	if (!function_exists('get_product_ctr')) {
		echo json_encode(['status' => 'error', 'message' => 'Server misconfiguration: controller missing']);
		exit;
	}
	$p = get_product_ctr($id);
	if ($p) echo json_encode(['status' => 'success', 'product' => $p]);
	else echo json_encode(['status' => 'error', 'message' => 'Not found']);
	exit;
}

// Otherwise return all products
if (!function_exists('fetch_all_products_ctr')) {
	echo json_encode(['status' => 'error', 'message' => 'Server misconfiguration: controller missing']);
	exit;
}

$products = fetch_all_products_ctr();
if ($products === false) $products = [];
echo json_encode($products);