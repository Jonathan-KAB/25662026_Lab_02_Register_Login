<?php
require_once __DIR__ . '/../classes/brand_class.php';
session_start();

header('Content-Type: application/json');
$response = ['status' => 'error', 'message' => 'Invalid request'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $brand_id = isset($_POST['brand_id']) ? (int)$_POST['brand_id'] : 0;
    $user_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;

    if ($brand_id <= 0 || $user_id <= 0) {
        $response['message'] = 'Missing brand_id or not logged in';
        echo json_encode($response);
        exit;
    }

    $brand = new Brand();
    $ok = $brand->deleteBrand($brand_id, $user_id);
    if ($ok) {
        $response = ['status' => 'success', 'message' => 'Brand deleted'];
    } else {
        $response['message'] = 'Failed to delete brand (not found or not owned by user)';
    }
}

echo json_encode($response);