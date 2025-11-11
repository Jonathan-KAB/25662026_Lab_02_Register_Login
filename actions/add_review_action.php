<?php
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

// Auth check - Must be logged in
if (!isLoggedIn()) {
    $response['message'] = 'You must be logged in to submit a review';
    echo json_encode($response);
    exit;
}

// Basic parameter validation
$product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
$rating = isset($_POST['rating']) ? (int)$_POST['rating'] : 0;
$review_text = isset($_POST['review_text']) ? trim($_POST['review_text']) : '';

if (!$product_id || !$rating || $rating < 1 || $rating > 5) {
    $response['message'] = 'Invalid product ID or rating (must be 1-5)';
    echo json_encode($response);
    exit;
}

if (strlen($review_text) < 10) {
    $response['message'] = 'Review must be at least 10 characters long';
    echo json_encode($response);
    exit;
}

// Check DB connectivity
$db = new db_connection();
if (!$db->db_connect()) {
    $response['message'] = 'Database connection failed';
    echo json_encode($response);
    exit;
}

$customer_id = $_SESSION['customer_id'];
$review_title = isset($_POST['review_title']) ? trim($_POST['review_title']) : '';

// Check if product exists
$check_product = $db->db_fetch_one("SELECT product_id FROM products WHERE product_id = $product_id");
if (!$check_product) {
    $response['message'] = 'Product not found';
    echo json_encode($response);
    exit;
}

// Check if user already reviewed this product
$existing = $db->db_fetch_one("SELECT review_id FROM product_reviews WHERE product_id = $product_id AND customer_id = $customer_id");
if ($existing) {
    $response['message'] = 'You have already reviewed this product';
    echo json_encode($response);
    exit;
}

// Check if this is a verified purchase
$verified_purchase = $db->db_fetch_one("
    SELECT 1 FROM orders o 
    JOIN orderdetails od ON o.order_id = od.order_id 
    WHERE o.customer_id = $customer_id 
    AND od.product_id = $product_id 
    AND o.order_status != 'cancelled'
    LIMIT 1
");

$verified = $verified_purchase ? 1 : 0;

// Insert review
$sql = "INSERT INTO product_reviews (product_id, customer_id, rating, review_title, review_text, verified_purchase, status) 
        VALUES ($product_id, $customer_id, $rating, " . $db->db_conn()->real_escape_string($review_title) . ", " . 
        $db->db_conn()->real_escape_string($review_text) . ", $verified, 'approved')";

$result = $db->db_query($sql);

if ($result) {
    $review_id = $db->db_conn()->insert_id;
    
    // Update product rating average and count
    $update_sql = "UPDATE products p SET 
        rating_average = (SELECT AVG(rating) FROM product_reviews WHERE product_id = $product_id AND status = 'approved'),
        rating_count = (SELECT COUNT(*) FROM product_reviews WHERE product_id = $product_id AND status = 'approved')
        WHERE product_id = $product_id";
    $db->db_query($update_sql);
    
    $response = [
        'status' => 'success', 
        'message' => 'Review submitted successfully',
        'review_id' => (int)$review_id
    ];
} else {
    $response['message'] = 'Failed to submit review. Please try again.';
    $log = "[".date('c')."] ADD_REVIEW FAILED: " . $db->db_conn()->error . "\n";
    @file_put_contents(__DIR__.'/../logs/review_errors.log', $log, FILE_APPEND);
}

echo json_encode($response);
