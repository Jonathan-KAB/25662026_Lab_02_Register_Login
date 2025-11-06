<?php
session_start();
require_once __DIR__ . '/../controllers/product_controller.php';
require_once __DIR__ . '/../controllers/cart_controller.php';
require_once __DIR__ . '/../settings/db_class.php';
require_once __DIR__ . '/../settings/core.php';

// Get cart count
$ipAddress = $_SERVER['REMOTE_ADDR'];
$customerId = isset($_SESSION['customer_id']) ? (int)$_SESSION['customer_id'] : null;
$cartCount = get_cart_count_ctr($ipAddress, $customerId);

// Pagination settings
$limit = 12; // Products per page
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $limit;

// Get filter parameters
$category_filter = isset($_GET['category']) ? (int)$_GET['category'] : 0;
$brand_filter = isset($_GET['brand']) ? (int)$_GET['brand'] : 0;
$search_query = isset($_GET['search']) ? trim($_GET['search']) : '';

// Fetch products based on filters
if (!empty($search_query)) {
    $products = search_products_ctr($search_query, $limit, $offset);
    $total_products = count_search_results_ctr($search_query);
} elseif ($category_filter > 0) {
    $products = filter_products_by_category_ctr($category_filter, $limit, $offset);
    $total_products = count_products_by_category_ctr($category_filter);
} elseif ($brand_filter > 0) {
    $products = filter_products_by_brand_ctr($brand_filter, $limit, $offset);
    $total_products = count_products_by_brand_ctr($brand_filter);
} else {
    $products = view_all_products_ctr($limit, $offset);
    $total_products = count_all_products_ctr();
}

$total_pages = ceil($total_products / $limit);

// Get all categories and brands for filters
$db = new db_connection();
$db->db_connect();
$categories = $db->db_fetch_all("SELECT cat_id, cat_name FROM categories ORDER BY cat_name ASC");
$brands = $db->db_fetch_all("SELECT brand_id, brand_name FROM brands ORDER BY brand_name ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Products - E-Commerce Store</title>
    <link rel="stylesheet" href="../css/app.css">
</head>
<body>
    <!-- Navigation Menu Tray -->
    <div class="menu-tray">
        <a href="../index.php" class="btn btn-sm btn-outline-secondary">Home</a>
        <a href="all_product.php" class="btn btn-sm btn-primary">All Products</a>
        <a href="cart.php" class="btn btn-sm btn-outline-secondary">
            Cart <?php if ($cartCount > 0): ?><span class="cart-badge" id="cart-count"><?= $cartCount ?></span><?php endif; ?>
        </a>
        <?php if (isLoggedIn()): ?>
            <?php if (isAdmin()): ?>
                <a href="../admin/category.php" class="btn btn-sm btn-outline-secondary">Admin</a>
            <?php endif; ?>
            <a href="../login/logout.php" class="btn btn-sm btn-outline-danger">Logout</a>
        <?php else: ?>
            <a href="../login/login.php" class="btn btn-sm btn-outline-secondary">Login</a>
            <a href="../login/register.php" class="btn btn-sm btn-outline-primary">Register</a>
        <?php endif; ?>
    </div>

    <!-- Page Header -->
    <div class="page-header">
        <div class="container">
            <h1>All Products</h1>
            <p>Discover our wide range of products</p>
        </div>
    </div>

    <div class="container">
        <!-- Search and Filter Section -->
        <div class="search-filter-section">
            <form method="GET" action="" id="searchForm">
                <!-- Search Box -->
                <div class="search-box">
                    <input 
                        type="text" 
                        name="search" 
                        id="searchInput" 
                        placeholder="Search products by name, description, or keywords..." 
                        value="<?= htmlspecialchars($search_query) ?>"
                    >
                    <button type="submit">Search</button>
                </div>

                <!-- Filters -->
                <div class="filters">
                    <div class="filter-group">
                        <label for="categoryFilter">Filter by Category</label>
                        <select name="category" id="categoryFilter" onchange="this.form.submit()">
                            <option value="">All Categories</option>
                            <?php if ($categories): ?>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat['cat_id'] ?>" <?= $category_filter == $cat['cat_id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($cat['cat_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label for="brandFilter">Filter by Brand</label>
                        <select name="brand" id="brandFilter" onchange="this.form.submit()">
                            <option value="">All Brands</option>
                            <?php if ($brands): ?>
                                <?php foreach ($brands as $brand): ?>
                                    <option value="<?= $brand['brand_id'] ?>" <?= $brand_filter == $brand['brand_id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($brand['brand_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <button type="button" class="clear-filters" onclick="window.location.href='all_product.php'">
                        Clear Filters
                    </button>
                </div>
            </form>
        </div>

        <!-- Results Count -->
        <?php if ($total_products > 0): ?>
            <div class="results-count">
                Showing <?= (($page - 1) * $limit) + 1 ?> - <?= min($page * $limit, $total_products) ?> of <?= $total_products ?> products
            </div>
        <?php endif; ?>

        <!-- Products Grid -->
        <?php if ($products && count($products) > 0): ?>
            <div class="products-grid">
                <?php foreach ($products as $product): ?>
                    <div class="product-card">
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
                            <div class="product-image-placeholder">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                        <?php endif; ?>
                        <div class="card-body">
                            <div class="product-category"><?= htmlspecialchars($product['cat_name'] ?? 'Uncategorized') ?></div>
                            <a href="single_product.php?id=<?= $product['product_id'] ?>" class="product-title">
                                <?= htmlspecialchars($product['product_title']) ?>
                            </a>
                            <div class="product-brand">Brand: <?= htmlspecialchars($product['brand_name'] ?? 'Unknown') ?></div>
                            <div class="price">GH₵ <?= number_format($product['product_price'], 2) ?></div>
                            <button class="add-to-cart-btn" onclick="addToCart(<?= $product['product_id'] ?>)">
                                Add to Cart
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
                <div class="pagination">
                    <?php
                    // Build query string for pagination
                    $query_params = [];
                    if (!empty($search_query)) $query_params['search'] = $search_query;
                    if ($category_filter > 0) $query_params['category'] = $category_filter;
                    if ($brand_filter > 0) $query_params['brand'] = $brand_filter;
                    
                    $base_url = 'all_product.php?' . http_build_query($query_params);
                    $separator = empty($query_params) ? '' : '&';
                    ?>
                    
                    <?php if ($page > 1): ?>
                        <a href="<?= $base_url . $separator ?>page=<?= $page - 1 ?>">« Previous</a>
                    <?php else: ?>
                        <span class="disabled">« Previous</span>
                    <?php endif; ?>

                    <?php
                    $start_page = max(1, $page - 2);
                    $end_page = min($total_pages, $page + 2);
                    
                    for ($i = $start_page; $i <= $end_page; $i++):
                    ?>
                        <?php if ($i == $page): ?>
                            <span class="current"><?= $i ?></span>
                        <?php else: ?>
                            <a href="<?= $base_url . $separator ?>page=<?= $i ?>"><?= $i ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>

                    <?php if ($page < $total_pages): ?>
                        <a href="<?= $base_url . $separator ?>page=<?= $page + 1 ?>">Next »</a>
                    <?php else: ?>
                        <span class="disabled">Next »</span>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="no-products">
                <h3>No Products Found</h3>
                <p>Try adjusting your search or filter criteria</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Load jQuery first -->
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
                        alert('Item added to cart successfully!');
                        // Update cart count if you have a cart badge
                        if ($('#cart-count').length) {
                            $('#cart-count').text(response.cart_count).show();
                        } else {
                            // Create badge if it doesn't exist
                            $('a[href="cart.php"]').append('<span class="cart-badge" id="cart-count">' + response.cart_count + '</span>');
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

        // Optional: Add loading state to buttons
        document.querySelectorAll('.add-to-cart-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                this.textContent = 'Adding...';
                setTimeout(() => {
                    this.textContent = 'Add to Cart';
                }, 1000);
            });
        });
    </script>
</body>
</html>
