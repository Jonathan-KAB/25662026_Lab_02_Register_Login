<?php
// Get reviews for a product
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../settings/db_class.php';
header('Content-Type: application/json');

// Check if product_id is provided
if (!isset($_GET['product_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Product ID is required']);
    exit;
}

$product_id = (int)$_GET['product_id'];

if ($product_id <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid product ID']);
    exit;
}

try {
    $db = new db_connection();
    $db->db_connect();
    
    // Get approved reviews for this product
    $sql = "SELECT pr.*, 
            c.customer_name,
            c.customer_email,
            DATE_FORMAT(pr.created_at, '%M %d, %Y') as review_date
            FROM product_reviews pr
            JOIN customer c ON pr.customer_id = c.customer_id
            WHERE pr.product_id = $product_id 
            AND pr.status = 'approved'
            ORDER BY pr.created_at DESC";
    
    $reviews = $db->db_fetch_all($sql);
    
    if ($reviews === false) {
        $reviews = [];
    }
    
    // Calculate rating breakdown
    $rating_breakdown = [
        5 => 0,
        4 => 0,
        3 => 0,
        2 => 0,
        1 => 0
    ];
    
    $total_reviews = count($reviews);
    $verified_count = 0;
    
    foreach ($reviews as $review) {
        if (isset($review['rating']) && $review['rating'] >= 1 && $review['rating'] <= 5) {
            $rating_breakdown[(int)$review['rating']]++;
        }
        if ($review['verified_purchase']) {
            $verified_count++;
        }
    }
    
    echo json_encode([
        'status' => 'success',
        'data' => [
            'reviews' => $reviews,
            'total' => $total_reviews,
            'verified_count' => $verified_count,
            'rating_breakdown' => $rating_breakdown
        ]
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}