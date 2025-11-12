<?php
/**
 * Empty Cart Action
 * Handles deleting all items from the cart
 */

require_once __DIR__ . '/../settings/core.php';
require_once __DIR__ . '/../controllers/cart_controller.php';

header('Content-Type: application/json');

$response = ['status' => 'error', 'message' => 'Invalid request'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response['message'] = 'Invalid request method';
    echo json_encode($response);
    exit;
}

// Get IP address
$ipAddress = $_SERVER['REMOTE_ADDR'];

// Get customer ID if logged in
$customerId = isset($_SESSION['customer_id']) ? (int)$_SESSION['customer_id'] : null;

// Empty the cart
$result = empty_cart_ctr($ipAddress, $customerId);

if ($result) {
    $response = [
        'status' => 'success',
        'message' => 'Cart emptied successfully',
        'cart_count' => 0
    ];
} else {
    $response['message'] = 'Failed to empty cart';
}

echo json_encode($response);
exit;
