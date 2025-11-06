<?php
/**
 * Test script to update product images
 */

require_once __DIR__ . '/settings/db_class.php';

// Create database connection
$db = new db_connection();

if (!$db->db_connect()) {
    die("Database connection failed");
}

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
    
    $productIds = array_slice(array_column($result, 'product_id'), 0, 2);
    
    foreach ($productIds as $productId) {
        $updateQuery = "UPDATE products SET product_image = 'uploads/BS_3.png' WHERE product_id = $productId";
        
        if ($db->db_query($updateQuery)) {
            echo "✓ Updated product ID $productId with BS_3.png<br>";
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
?>
