<?php
/**
 * Process Checkout Action
 * Handles the complete checkout workflow
 * Moves cart items to orders, orderdetails, and payment tables
 */

require_once __DIR__ . '/../settings/core.php';
require_once __DIR__ . '/../controllers/cart_controller.php';
require_once __DIR__ . '/../controllers/order_controller.php';
require_once __DIR__ . '/../controllers/product_controller.php';

header('Content-Type: application/json');

$response = ['status' => 'error', 'message' => 'Invalid request'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response['message'] = 'Invalid request method';
    echo json_encode($response);
    exit;
}

// Check if user is logged in
if (!isset($_SESSION['customer_id'])) {
    $response = [
        'status' => 'error',
        'message' => 'Please login to checkout',
        'redirect' => '../login/login.php'
    ];
    echo json_encode($response);
    exit;
}

$customerId = (int)$_SESSION['customer_id'];
$ipAddress = $_SERVER['REMOTE_ADDR'];

// Step 1: Get cart items
$cartItems = get_cart_items_ctr($ipAddress, $customerId);

if (empty($cartItems)) {
    $response['message'] = 'Your cart is empty';
    echo json_encode($response);
    exit;
}

// Step 2: Calculate total amount
$totalAmount = 0;
foreach ($cartItems as $item) {
    $totalAmount += ($item['product_price'] * $item['qty']);
}

// Step 3: Generate unique invoice number (timestamp + random)
$invoiceNo = time() . rand(1000, 9999);

// Step 4: Create order in orders table and get order ID
$orderId = create_order_ctr($customerId, $invoiceNo);

if (!$orderId) {
    $response['message'] = 'Failed to create order';
    echo json_encode($response);
    exit;
}

// Step 5: Add order details (each cart item)
$allDetailsAdded = true;
foreach ($cartItems as $item) {
    $detailAdded = add_order_details_ctr($orderId, $item['p_id'], $item['qty']);
    if (!$detailAdded) {
        $allDetailsAdded = false;
        break;
    }
}

if (!$allDetailsAdded) {
    $response['message'] = 'Failed to add order details';
    echo json_encode($response);
    exit;
}

// Step 6: Record simulated payment
$paymentRecorded = record_payment_ctr($customerId, $orderId, $totalAmount, 'GHS');

if (!$paymentRecorded) {
    $response['message'] = 'Failed to record payment';
    echo json_encode($response);
    exit;
}

// Step 7: Empty the cart
$cartEmptied = empty_cart_ctr($ipAddress, $customerId);

if (!$cartEmptied) {
    // Log warning but don't fail the checkout
    error_log("Warning: Cart not emptied after checkout for customer $customerId");
}

// Step 8: Return success response
$response = [
    'status' => 'success',
    'message' => 'Order placed successfully!',
    'order_id' => $orderId,
    'invoice_no' => $invoiceNo,
    'total_amount' => number_format($totalAmount, 2),
    'currency' => 'GHS'
];

echo json_encode($response);
exit;
