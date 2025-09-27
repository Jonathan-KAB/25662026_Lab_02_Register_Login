<?php

header('Content-Type: application/json');

session_start();

$response = array();

// Check if already logged in
if (isset($_SESSION['user_id'])) {
    $response['status'] = 'error';
    $response['message'] = 'You are already logged in';
    echo json_encode($response);
    exit();
}

require_once '../controllers/customer_controller.php';

$email = $_POST['email'];
$password = $_POST['password'];

// Debug: Check if user exists
$existing_user = get_user_by_email_ctr($email);
if (!$existing_user) {
    $response['status'] = 'error';
    $response['message'] = 'No account found with this email address';
    echo json_encode($response);
    exit();
}

// Try to authenticate
$user = authenticate_user_ctr($email, $password);


if ($user) {
    $_SESSION['user_id'] = $user['customer_id'];
    $_SESSION['user_name'] = $user['customer_name'];
    $_SESSION['user_email'] = $user['customer_email'];
    $_SESSION['user_role'] = $user['user_role'];

    $response['status'] = 'success';
    $response['message'] = 'Login successful';
    // If admin, send redirect URL (relative path for subfolder support)
    if ($user['user_role'] == 2) {
        $response['redirect'] = '../admin/category.php';
    }
} else {
    $response['status'] = 'error';
    $response['message'] = 'Invalid password';
}

echo json_encode($response);

?>