<?php
session_start();
require_once __DIR__ . '/../controllers/product_controller.php';

// Get product ID from URL
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($product_id <= 0) {
    header('Location: all_product.php');
    exit;
}

// Fetch product details
$product = view_single_product_ctr($product_id);

if (!$product) {
    header('Location: all_product.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($product['product_title']) ?> - E-Commerce Store</title>
    <link rel="stylesheet" href="../css/app.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
            color: #333;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        /* Header */
        .header {
            background-color: #fff;
            padding: 20px 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }

        .header-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #333;
            text-decoration: none;
        }

        .nav-links {
            display: flex;
            gap: 20px;
        }

        .nav-links a {
            text-decoration: none;
            color: #666;
            font-weight: 500;
            transition: color 0.3s;
        }

        .nav-links a:hover {
            color: #007bff;
        }

        /* Breadcrumb */
        .breadcrumb {
            margin-bottom: 20px;
            font-size: 14px;
        }

        .breadcrumb a {
            color: #007bff;
            text-decoration: none;
        }

        .breadcrumb a:hover {
            text-decoration: underline;
        }

        .breadcrumb span {
            color: #666;
            margin: 0 5px;
        }

        /* Product Detail Container */
        .product-detail {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            padding: 40px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
        }

        /* Product Image Section */
        .product-image-section {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .main-product-image {
            width: 100%;
            max-width: 500px;
            height: 500px;
            object-fit: cover;
            border-radius: 8px;
            background-color: #f0f0f0;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .main-product-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 8px;
        }

        /* Product Info Section */
        .product-info-section {
            display: flex;
            flex-direction: column;
        }

        .product-category-badge {
            display: inline-block;
            padding: 5px 15px;
            background-color: #007bff;
            color: white;
            border-radius: 20px;
            font-size: 12px;
            text-transform: uppercase;
            margin-bottom: 15px;
            width: fit-content;
        }

        .product-title {
            font-size: 32px;
            font-weight: bold;
            color: #333;
            margin-bottom: 15px;
            line-height: 1.3;
        }

        .product-brand {
            font-size: 16px;
            color: #666;
            margin-bottom: 20px;
        }

        .product-brand strong {
            color: #333;
        }

        .product-price {
            font-size: 36px;
            font-weight: bold;
            color: #28a745;
            margin-bottom: 20px;
        }

        .divider {
            height: 1px;
            background-color: #e0e0e0;
            margin: 20px 0;
        }

        .product-description-section {
            margin-bottom: 30px;
        }

        .section-title {
            font-size: 18px;
            font-weight: 600;
            color: #333;
            margin-bottom: 10px;
        }

        .product-description {
            font-size: 15px;
            color: #666;
            line-height: 1.6;
        }

        .product-keywords {
            margin-bottom: 30px;
        }

        .keywords-list {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .keyword-tag {
            padding: 5px 12px;
            background-color: #f0f0f0;
            color: #555;
            border-radius: 15px;
            font-size: 13px;
        }

        .product-id-info {
            font-size: 13px;
            color: #999;
            margin-bottom: 20px;
        }

        /* Action Buttons */
        .product-actions {
            display: flex;
            gap: 15px;
            margin-top: auto;
        }

        .add-to-cart-btn,
        .back-btn {
            padding: 15px 30px;
            font-size: 16px;
            font-weight: 600;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            text-align: center;
            display: inline-block;
        }

        .add-to-cart-btn {
            flex: 1;
            background-color: #007bff;
            color: white;
        }

        .add-to-cart-btn:hover {
            background-color: #0056b3;
        }

        .back-btn {
            background-color: #6c757d;
            color: white;
        }

        .back-btn:hover {
            background-color: #5a6268;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .product-detail {
                grid-template-columns: 1fr;
                padding: 20px;
            }

            .product-title {
                font-size: 24px;
            }

            .product-price {
                font-size: 28px;
            }

            .main-product-image {
                height: 300px;
            }

            .product-actions {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="header-content">
            <a href="../index.php" class="logo">E-Commerce Store</a>
            <nav class="nav-links">
                <a href="../index.php">Home</a>
                <a href="all_product.php">All Products</a>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="../login/logout.php">Logout</a>
                <?php else: ?>
                    <a href="../login/login.php">Login</a>
                    <a href="../login/register.php">Register</a>
                <?php endif; ?>
            </nav>
        </div>
    </div>

    <div class="container">
        <!-- Breadcrumb -->
        <div class="breadcrumb">
            <a href="../index.php">Home</a>
            <span>›</span>
            <a href="all_product.php">All Products</a>
            <span>›</span>
            <span><?= htmlspecialchars($product['product_title']) ?></span>
        </div>

        <!-- Product Detail -->
        <div class="product-detail">
            <!-- Product Image Section -->
            <div class="product-image-section">
                <div class="main-product-image">
                    <?php if (!empty($product['product_image'])): ?>
                        <?php 
                        // Handle both /uploads and uploads paths
                        $imagePath = $product['product_image'];
                        if (strpos($imagePath, '/') === 0) {
                            // Absolute path from root (school server)
                            echo '<img src="' . htmlspecialchars($imagePath) . '" alt="' . htmlspecialchars($product['product_title']) . '">';
                        } else {
                            // Relative path (local XAMPP)
                            echo '<img src="../' . htmlspecialchars($imagePath) . '" alt="' . htmlspecialchars($product['product_title']) . '">';
                        }
                        ?>
                    <?php else: ?>
                        <img src="../uploads/placeholder.jpg" 
                             alt="Product placeholder"
                             onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22500%22 height=%22500%22%3E%3Crect fill=%22%23ddd%22 width=%22500%22 height=%22500%22/%3E%3Ctext fill=%22%23999%22 x=%2250%25%22 y=%2250%25%22 text-anchor=%22middle%22 dy=%22.3em%22 font-size=%2224%22%3ENo Image Available%3C/text%3E%3C/svg%3E'">
                    <?php endif; ?>
                </div>
            </div>

            <!-- Product Info Section -->
            <div class="product-info-section">
                <div class="product-category-badge">
                    <?= htmlspecialchars($product['cat_name'] ?? 'Uncategorized') ?>
                </div>

                <h1 class="product-title"><?= htmlspecialchars($product['product_title']) ?></h1>

                <div class="product-brand">
                    <strong>Brand:</strong> <?= htmlspecialchars($product['brand_name'] ?? 'Unknown') ?>
                </div>

                <div class="product-price">GH₵ <?= number_format($product['product_price'], 2) ?></div>

                <div class="product-id-info">Product ID: #<?= $product['product_id'] ?></div>

                <div class="divider"></div>

                <!-- Product Description -->
                <?php if (!empty($product['product_desc'])): ?>
                    <div class="product-description-section">
                        <h2 class="section-title">Product Description</h2>
                        <p class="product-description"><?= nl2br(htmlspecialchars($product['product_desc'])) ?></p>
                    </div>
                <?php endif; ?>

                <!-- Product Keywords -->
                <?php if (!empty($product['product_keywords'])): ?>
                    <div class="product-keywords">
                        <h2 class="section-title">Tags</h2>
                        <div class="keywords-list">
                            <?php 
                            $keywords = explode(',', $product['product_keywords']);
                            foreach ($keywords as $keyword): 
                                $keyword = trim($keyword);
                                if (!empty($keyword)):
                            ?>
                                <span class="keyword-tag"><?= htmlspecialchars($keyword) ?></span>
                            <?php 
                                endif;
                            endforeach; 
                            ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Action Buttons -->
                <div class="product-actions">
                    <button class="add-to-cart-btn" onclick="addToCart(<?= $product['product_id'] ?>)">
                        Add to Cart
                    </button>
                    <a href="all_product.php" class="back-btn">Back to Products</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Load jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <script>
        // Add to cart functionality
        function addToCart(productId) {
            $.ajax({
                url: '../actions/add_to_cart_action.php',
                method: 'POST',
                data: {
                    product_id: productId,
                    quantity: 1
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        const btn = document.querySelector('.add-to-cart-btn');
                        const originalText = btn.textContent;
                        btn.textContent = 'Added to Cart!';
                        btn.disabled = true;
                        
                        setTimeout(function() {
                            btn.textContent = originalText;
                            btn.disabled = false;
                        }, 2000);
                        
                        // Update cart count if badge exists
                        if ($('#cart-count').length) {
                            $('#cart-count').text(response.cart_count).show();
                        }
                    } else {
                        alert(response.message || 'Failed to add item to cart');
                    }
                },
                error: function() {
                    alert('Error adding item to cart');
                }
            });
        }
    </script>
</body>
</html>
