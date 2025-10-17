<?php
require_once __DIR__ . '/../controllers/product_controller.php';
require_once __DIR__ . '/../settings/core.php';
require_once __DIR__ . '/../settings/db_class.php';
session_start();
header('Content-Type: application/json');
$response = ['status' => 'error', 'message' => 'Invalid request'];

// Only accept POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode($response);
    exit;
}

// Auth check
if (!isLoggedIn() || !isAdmin()) {
    $response['message'] = 'Not authorized';
    echo json_encode($response);
    exit;
}

// Basic parameter validation
$cat = isset($_POST['product_cat']) ? (int)$_POST['product_cat'] : 0;
$brand = isset($_POST['product_brand']) ? (int)$_POST['product_brand'] : 0;
$title = isset($_POST['product_title']) ? trim($_POST['product_title']) : '';
if (!$cat || !$brand || $title === '') {
    $response['message'] = 'Missing required fields: product_cat, product_brand, product_title';
    echo json_encode($response);
    exit;
}

// Ensure controller function exists
if (!function_exists('add_product_ctr')) {
    $response['message'] = 'Server error: product controller is missing';
    echo json_encode($response);
    exit;
}

// Check DB connectivity quickly
$db = new db_connection();
if (!$db->db_connect()) {
    $response['message'] = 'Database connection failed';
    echo json_encode($response);
    exit;
}

$data = [];
$data['product_cat'] = $cat;
$data['product_brand'] = $brand;
$data['product_title'] = $title;
$data['product_price'] = isset($_POST['product_price']) ? (float)$_POST['product_price'] : 0;
$data['product_desc'] = isset($_POST['product_desc']) ? trim($_POST['product_desc']) : '';
$data['product_keywords'] = isset($_POST['product_keywords']) ? trim($_POST['product_keywords']) : '';

// attempt insert and include DB error when available
$id = add_product_ctr($data);
    if ($id) {
        $response = ['status' => 'success', 'product_id' => (int)$id];
    } else {
        // try to return a DB error if Product class exposes it
        if (class_exists('Product')) {
            $p = new Product();
            $err = $p->getLastError();
            $lastq = $p->getLastQuery();
            $response['message'] = 'Failed to add product (insert returned false). DB error: ' . $err;

            // write debug log (server-side) to help diagnosis
            $log = "[".date('c')."] ADD_PRODUCT FAILED: DB error: $err\nPAYLOAD: ".json_encode($data)."\nQUERY: $lastq\n\n";
            @file_put_contents(__DIR__.'/../logs/add_product_errors.log', $log, FILE_APPEND);
        } else {
            $response['message'] = 'Failed to add product (insert returned false)';
        }
    }

echo json_encode($response);
