<?php
/**
 * Update Order Action
 * Handles updating order status
 */

session_start();
require_once __DIR__ . '/../settings/core.php';
require_once __DIR__ . '/../settings/db_class.php';

header('Content-Type: application/json');

$response = ['status' => 'error', 'message' => 'Invalid request'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response['message'] = 'Invalid request method';
    echo json_encode($response);
    exit;
}

// Only admin can update order status
if (!isLoggedIn() || !isAdmin()) {
    $response['message'] = 'Not authorized';
    echo json_encode($response);
    exit;
}

$orderId = isset($_POST['order_id']) ? (int)$_POST['order_id'] : 0;
$status = isset($_POST['order_status']) ? trim($_POST['order_status']) : '';

if ($orderId <= 0 || empty($status)) {
    $response['message'] = 'Order ID and status are required';
    echo json_encode($response);
    exit;
}

// Validate status
$validStatuses = ['pending', 'confirmed', 'shipped', 'delivered', 'cancelled'];
if (!in_array($status, $validStatuses)) {
    $response['message'] = 'Invalid order status';
    echo json_encode($response);
    exit;
}

$db = new db_connection();
if (!$db->db_connect()) {
    $response['message'] = 'Database connection failed';
    echo json_encode($response);
    exit;
}

$sql = "UPDATE orders SET order_status = ? WHERE order_id = ?";
$result = $db->db_query($sql, [$status, $orderId]);

if ($result) {
    $response = [
        'status' => 'success',
        'message' => 'Order status updated successfully'
    ];
} else {
    $response['message'] = 'Failed to update order status';
}

echo json_encode($response);
exit;
?>
