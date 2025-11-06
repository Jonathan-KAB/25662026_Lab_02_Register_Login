<?php
session_start();
require_once __DIR__ . '/../controllers/product_controller.php';
require_once __DIR__ . '/../settings/db_class.php';

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

        /* Page Title */
        .page-title {
            text-align: center;
            margin-bottom: 30px;
        }

        .page-title h1 {
            font-size: 32px;
            color: #333;
            margin-bottom: 10px;
        }

        .page-title p {
            color: #666;
            font-size: 16px;
        }

        /* Search and Filters */
        .search-filter-section {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }

        .search-box {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }

        .search-box input[type="text"] {
            flex: 1;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }

        .search-box button {
            padding: 12px 30px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            transition: background-color 0.3s;
        }

        .search-box button:hover {
            background-color: #0056b3;
        }

        .filters {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }

        .filter-group {
            flex: 1;
            min-width: 200px;
        }

        .filter-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
            color: #555;
        }

        .filter-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            background-color: white;
        }

        .clear-filters {
            padding: 10px 20px;
            background-color: #6c757d;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            align-self: flex-end;
        }

        .clear-filters:hover {
            background-color: #5a6268;
        }

        /* Products Grid */
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }

        .product-card {
            background-color: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transition: transform 0.3s, box-shadow 0.3s;
            display: flex;
            flex-direction: column;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 16px rgba(0,0,0,0.15);
        }

        .product-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            background-color: #f0f0f0;
        }

        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .product-info {
            padding: 15px;
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .product-category {
            font-size: 12px;
            color: #007bff;
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        .product-title {
            font-size: 16px;
            font-weight: 600;
            color: #333;
            margin-bottom: 10px;
            text-decoration: none;
            display: block;
        }

        .product-title:hover {
            color: #007bff;
        }

        .product-brand {
            font-size: 13px;
            color: #666;
            margin-bottom: 10px;
        }

        .product-price {
            font-size: 20px;
            font-weight: bold;
            color: #28a745;
            margin-bottom: 15px;
        }

        .add-to-cart-btn {
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            transition: background-color 0.3s;
            margin-top: auto;
        }

        .add-to-cart-btn:hover {
            background-color: #0056b3;
        }

        /* No Products Message */
        .no-products {
            text-align: center;
            padding: 60px 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .no-products h3 {
            font-size: 24px;
            color: #666;
            margin-bottom: 10px;
        }

        .no-products p {
            color: #999;
        }

        /* Pagination */
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            margin-top: 40px;
        }

        .pagination a,
        .pagination span {
            padding: 10px 15px;
            background-color: #fff;
            color: #007bff;
            text-decoration: none;
            border-radius: 4px;
            border: 1px solid #ddd;
            transition: all 0.3s;
        }

        .pagination a:hover {
            background-color: #007bff;
            color: white;
            border-color: #007bff;
        }

        .pagination .current {
            background-color: #007bff;
            color: white;
            border-color: #007bff;
        }

        .pagination .disabled {
            opacity: 0.5;
            cursor: not-allowed;
            pointer-events: none;
        }

        /* Results Count */
        .results-count {
            text-align: center;
            color: #666;
            margin-bottom: 20px;
            font-size: 14px;
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
        <!-- Page Title -->
        <div class="page-title">
            <h1>All Products</h1>
            <p>Discover our wide range of products</p>
        </div>

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
                        <div class="product-image">
                            <?php if (!empty($product['product_image'])): ?>
                                <img src="../<?= htmlspecialchars($product['product_image']) ?>" 
                                     alt="<?= htmlspecialchars($product['product_title']) ?>">
                            <?php else: ?>
                                <img src="../uploads/placeholder.jpg" 
                                     alt="Product placeholder"
                                     onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22200%22 height=%22200%22%3E%3Crect fill=%22%23ddd%22 width=%22200%22 height=%22200%22/%3E%3Ctext fill=%22%23999%22 x=%2250%25%22 y=%2250%25%22 text-anchor=%22middle%22 dy=%22.3em%22%3ENo Image%3C/text%3E%3C/svg%3E'">
                            <?php endif; ?>
                        </div>
                        <div class="product-info">
                            <div class="product-category"><?= htmlspecialchars($product['cat_name'] ?? 'Uncategorized') ?></div>
                            <a href="single_product.php?id=<?= $product['product_id'] ?>" class="product-title">
                                <?= htmlspecialchars($product['product_title']) ?>
                            </a>
                            <div class="product-brand">Brand: <?= htmlspecialchars($product['brand_name'] ?? 'Unknown') ?></div>
                            <div class="product-price">GH₵ <?= number_format($product['product_price'], 2) ?></div>
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

    <script>
        // Add to cart functionality (placeholder for now)
        function addToCart(productId) {
            alert('Add to cart functionality will be implemented in the next lab.\nProduct ID: ' + productId);
            // TODO: Implement actual add to cart functionality in future lab
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
