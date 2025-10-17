<?php
require_once __DIR__ . '/../controllers/product_controller.php';
require_once __DIR__ . '/../settings/core.php';
session_start();
header('Content-Type: application/json');
$response = ['status' => 'error', 'message' => 'Invalid request'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isLoggedIn() || !isAdmin()) {
        $response['message'] = 'Not authorized';
        echo json_encode($response);
        exit;
    }

    if (!isset($_POST['product_id']) || !isset($_FILES['image'])) {
        $response['message'] = 'Missing product id or image';
        echo json_encode($response);
        exit;
    }

    $product_id = (int)$_POST['product_id'];
    $user_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;
    if ($product_id <= 0 || $user_id <= 0) {
        $response['message'] = 'Invalid ids';
        echo json_encode($response);
        exit;
    }

    $uploadsDir = __DIR__ . '/../uploads';
    // ensure uploads dir exists
    if (!is_dir($uploadsDir)) {
        $response['message'] = 'Uploads directory missing on server';
        echo json_encode($response);
        exit;
    }

    // create user/product subfolders
    $userDir = $uploadsDir . '/u' . $user_id;
    $prodDir = $userDir . '/p' . $product_id;
    if (!is_dir($userDir)) mkdir($userDir, 0755, true);
    if (!is_dir($prodDir)) mkdir($prodDir, 0755, true);

    $file = $_FILES['image'];
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $response['message'] = 'Upload error';
        echo json_encode($response);
        exit;
    }

    $name = basename($file['name']);
    // sanitize name
    $ext = pathinfo($name, PATHINFO_EXTENSION);
    $allowed = ['jpg','jpeg','png','gif'];
    if (!in_array(strtolower($ext), $allowed)) {
        $response['message'] = 'Invalid file type';
        echo json_encode($response);
        exit;
    }

    $newName = 'img_' . time() . '_' . rand(1000,9999) . '.' . $ext;
    $destPath = $prodDir . '/' . $newName;

    if (!move_uploaded_file($file['tmp_name'], $destPath)) {
        $response['message'] = 'Failed to move uploaded file';
        echo json_encode($response);
        exit;
    }

    // store relative path for DB (relative to project root)
    $relPath = 'uploads/u' . $user_id . '/p' . $product_id . '/' . $newName;

    // update product image field
    $res = update_product_ctr($product_id, ['product_image' => $relPath]);
    if ($res) {
        $response = ['status' => 'success', 'path' => $relPath];
    } else {
        $response['message'] = 'Failed to update product image in DB';
    }
}

echo json_encode($response);
echo json_encode($response);