<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Home - E-Commerce Store</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
	<link href="css/app.css" rel="stylesheet">
	<style>
		.hero-section {
			background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
			color: white;
			padding: 60px 20px;
			border-radius: 10px;
			margin-bottom: 40px;
			text-align: center;
		}
		.hero-section h1 {
			font-size: 48px;
			font-weight: bold;
			margin-bottom: 20px;
		}
		.hero-section p {
			font-size: 20px;
			margin-bottom: 30px;
		}
		.hero-btn {
			padding: 12px 30px;
			font-size: 18px;
			border-radius: 5px;
		}
		.search-section {
			background: white;
			padding: 30px;
			border-radius: 10px;
			box-shadow: 0 2px 10px rgba(0,0,0,0.1);
			margin-bottom: 40px;
		}
		.search-box-home {
			display: flex;
			gap: 10px;
			max-width: 600px;
			margin: 0 auto;
		}
		.search-box-home input {
			flex: 1;
			padding: 12px 20px;
			border: 2px solid #ddd;
			border-radius: 5px;
			font-size: 16px;
		}
		.search-box-home button {
			padding: 12px 30px;
			background-color: #007bff;
			color: white;
			border: none;
			border-radius: 5px;
			cursor: pointer;
			font-weight: 600;
		}
		.quick-links {
			display: grid;
			grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
			gap: 20px;
			margin-top: 40px;
		}
		.quick-link-card {
			background: white;
			padding: 30px;
			border-radius: 10px;
			box-shadow: 0 2px 10px rgba(0,0,0,0.1);
			text-align: center;
			transition: transform 0.3s;
		}
		.quick-link-card:hover {
			transform: translateY(-5px);
		}
		.quick-link-card h3 {
			margin-bottom: 15px;
			color: #333;
		}
		.quick-link-card p {
			color: #666;
			margin-bottom: 20px;
		}
	</style>
</head>
<body>

	<div class="menu-tray">
		<span class="me-2">Menu:</span>
		<?php
		require_once 'settings/core.php';
		require_once 'settings/db_class.php';
		?>
		<a href="index.php" class="btn btn-sm btn-primary">Home</a>
		<a href="view/all_product.php" class="btn btn-sm btn-outline-info">All Products</a>
		<?php if (!isLoggedIn()): ?>
			<a href="login/register.php" class="btn btn-sm btn-outline-primary">Register</a>
			<a href="login/login.php" class="btn btn-sm btn-outline-secondary">Login</a>
		<?php else: ?>
			<?php if (isAdmin()): ?>
				<a href="admin/category.php" class="btn btn-sm btn-outline-secondary">Category</a>
				<a href="admin/brand.php" class="btn btn-sm btn-outline-secondary">Brand</a>
				<a href="admin/product.php" class="btn btn-sm btn-outline-secondary">Products</a>
			<?php endif; ?>
			<a href="login/logout.php" class="btn btn-sm btn-outline-danger">Logout</a>
		<?php endif; ?>
	</div>

	<div class="container" style="padding-top:120px;">
		<!-- Hero Section -->
		<div class="hero-section">
			<h1>Welcome to Our Store</h1>
			<p>Discover amazing products at great prices</p>
			<a href="view/all_product.php" class="btn btn-light hero-btn">Shop Now</a>
		</div>

		<!-- Search Section -->
		<div class="search-section">
			<h2 class="text-center mb-4">Search Products</h2>
			<form action="view/product_search_result.php" method="GET">
				<div class="search-box-home">
					<input 
						type="text" 
						name="search" 
						placeholder="Search for products by name, description, or keywords..." 
						required
					>
					<button type="submit">Search</button>
				</div>
			</form>
		</div>

		<!-- Filter by Category and Brand -->
		<div class="quick-links">
			<div class="quick-link-card">
				<h3>📦 All Products</h3>
				<p>Browse our complete collection of products</p>
				<a href="view/all_product.php" class="btn btn-primary">View All</a>
			</div>

			<div class="quick-link-card">
				<h3>🏷️ Categories</h3>
				<p>Shop by product category</p>
				<form action="view/all_product.php" method="GET" style="max-width: 250px; margin: 0 auto;">
					<select name="category" class="form-select mb-3" onchange="this.form.submit()">
						<option value="">Select Category</option>
						<?php
						$db = new db_connection();
						if ($db->db_connect()) {
							$categories = $db->db_fetch_all("SELECT cat_id, cat_name FROM categories ORDER BY cat_name ASC");
							if ($categories) {
								foreach ($categories as $cat) {
									echo '<option value="' . $cat['cat_id'] . '">' . htmlspecialchars($cat['cat_name']) . '</option>';
								}
							}
						}
						?>
					</select>
					<noscript><button type="submit" class="btn btn-primary">Go</button></noscript>
				</form>
			</div>

			<div class="quick-link-card">
				<h3>🔖 Brands</h3>
				<p>Shop by your favorite brands</p>
				<form action="view/all_product.php" method="GET" style="max-width: 250px; margin: 0 auto;">
					<select name="brand" class="form-select mb-3" onchange="this.form.submit()">
						<option value="">Select Brand</option>
						<?php
						if ($db->db_connect()) {
							$brands = $db->db_fetch_all("SELECT brand_id, brand_name FROM brands ORDER BY brand_name ASC");
							if ($brands) {
								foreach ($brands as $brand) {
									echo '<option value="' . $brand['brand_id'] . '">' . htmlspecialchars($brand['brand_name']) . '</option>';
								}
							}
						}
						?>
					</select>
					<noscript><button type="submit" class="btn btn-primary">Go</button></noscript>
				</form>
			</div>
		</div>

		<!-- Additional Info -->
		<div class="text-center mt-5">
			<?php if (!isLoggedIn()): ?>
				<p class="text-muted">
					<a href="login/register.php">Register</a> or 
					<a href="login/login.php">Login</a> to start shopping
				</p>
			<?php else: ?>
				<p class="text-success">
					Welcome back! Start browsing our products.
				</p>
			<?php endif; ?>
		</div>
	</div>

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
