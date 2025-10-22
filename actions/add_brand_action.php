<?php
require_once __DIR__ . '/../classes/brand_class.php';
session_start();

header('Content-Type: application/json');
$response = ['status' => 'error', 'message' => 'Invalid request'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $brand_name = isset($_POST['brand_name']) ? trim($_POST['brand_name']) : '';
    $brand_cat = isset($_POST['brand_cat']) ? (int)$_POST['brand_cat'] : 0;
    $user_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;

    if ($brand_name === '' || $brand_cat <= 0 || $user_id <= 0) {
        $response['message'] = 'Missing parameters or not logged in';
        echo json_encode($response);
        exit;
    }

    $brand = new Brand();
    $res = $brand->addBrand($brand_name, $brand_cat, $user_id);
    if ($res === true) {
        $response = ['status' => 'success', 'message' => 'Brand added'];
    } elseif ($res === 'duplicate') {
        $response['message'] = 'Brand already exists for this category and user';
    } else {
        // try to include mysqli error for debugging (will be empty if not available)
        $dbErr = '';
        if (isset($brand->db) && $brand->db) {
            $dbErr = mysqli_error($brand->db);
        }
        $response['message'] = 'Database error while adding brand';
        if ($dbErr) $response['debug'] = $dbErr;
    }
}

echo json_encode($response);
