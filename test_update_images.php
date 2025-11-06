<?php
/**
 * Test script to update product images
 */

require_once __DIR__ . '/settings/db_class.php';
require_once __DIR__ . '/settings/upload_config.php';

// Create database connection
$db = new db_connection();

if (!$db->db_connect()) {
    die("Database connection failed");
}

// Determine the correct image path based on environment
$imagePath = UPLOAD_WEB_PATH . '/BS_3.png';

// First, let's see what products we have
echo "<h2>Current Products:</h2>";
$query = "SELECT product_id, product_title, product_image FROM products";
$result = $db->db_fetch_all($query);

if ($result) {
    echo "<table border='1'>";
    echo "<tr><th>Product ID</th><th>Title</th><th>Current Image</th></tr>";
    foreach ($result as $product) {
        echo "<tr>";
        echo "<td>" . $product['product_id'] . "</td>";
        echo "<td>" . $product['product_title'] . "</td>";
        echo "<td>" . ($product['product_image'] ?? 'NULL') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Update the first two products with BS_3.png
    echo "<h2>Updating Products:</h2>";
    echo "<p>Using image path: <strong>" . htmlspecialchars($imagePath) . "</strong></p>";
    
    $productIds = array_slice(array_column($result, 'product_id'), 0, 2);
    
    foreach ($productIds as $productId) {
        $updateQuery = "UPDATE products SET product_image = '" . mysqli_real_escape_string($db->db, $imagePath) . "' WHERE product_id = $productId";
        
        if ($db->db_query($updateQuery)) {
            echo "✓ Updated product ID $productId with $imagePath<br>";
        } else {
            echo "✗ Failed to update product ID $productId<br>";
        }
    }
    
    // Show updated products
    echo "<h2>Updated Products:</h2>";
    $result2 = $db->db_fetch_all($query);
    
    if ($result2) {
        echo "<table border='1'>";
        echo "<tr><th>Product ID</th><th>Title</th><th>Current Image</th></tr>";
        foreach ($result2 as $product) {
            echo "<tr>";
            echo "<td>" . $product['product_id'] . "</td>";
            echo "<td>" . $product['product_title'] . "</td>";
            echo "<td>" . ($product['product_image'] ?? 'NULL') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
} else {
    echo "No products found";
}

echo "<h2>Environment Info:</h2>";
echo "<p>Upload Base Path: " . UPLOAD_BASE_PATH . "</p>";
echo "<p>Upload Web Path: " . UPLOAD_WEB_PATH . "</p>";
echo "<p>Current Directory: " . __DIR__ . "</p>";
?>
