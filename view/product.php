<?php
// public product listing view
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Products</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
	<link href="../css/app.css" rel="stylesheet">
	<style>
		.product-image{height:150px;object-fit:cover;width:100%;}
	</style>
</head>
<body>

	<?php
	// Menu tray: view is in `view/` so settings are one level up
	require_once __DIR__ . '/../settings/core.php';
	?>

	<div class="menu-tray">
		<span class="me-2">Menu:</span>
		<?php if (!isLoggedIn()): ?>
			<a href="../login/register.php" class="btn btn-sm btn-outline-primary">Register</a>
			<a href="../login/login.php" class="btn btn-sm btn-outline-secondary">Login</a>
		<?php else: ?>
			<?php if (isAdmin()): ?>
				<a href="../admin/category.php" class="btn btn-sm btn-outline-secondary">Category</a>
				<a href="../admin/brand.php" class="btn btn-sm btn-outline-secondary">Brand</a>
				<a href="../admin/product.php" class="btn btn-sm btn-outline-secondary">Products</a>
			<?php endif; ?>
			<a href="../login/logout.php" class="btn btn-sm btn-outline-danger">Logout</a>
		<?php endif; ?>
	</div>

	<div class="container" style="padding-top:120px; max-width:1100px;">
		<div class="text-center mb-4">
			<h1>Products</h1>
			<p class="text-muted">Browse products by category and brand</p>
		</div>

		<div id="products" class="row g-3"></div>
	</div>

	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
	<script>
	$(function(){
		$.getJSON('../actions/fetch_product_action.php', function(data){
			var html = '';
			if (Array.isArray(data) && data.length) {
				data.forEach(function(p){
					// Use local inline SVG placeholder when no image is provided (avoids external DNS lookups)
					var placeholderSvg = 'data:image/svg+xml;utf8,' + encodeURIComponent('<svg xmlns="http://www.w3.org/2000/svg" width="600" height="300" viewBox="0 0 600 300"><rect width="100%" height="100%" fill="#f3f4f6"/><g fill="#9ca3af"><text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" font-family="Arial, Helvetica, sans-serif" font-size="20">No Image</text></g></svg>');
					var img = p.product_image ? ('../' + p.product_image) : placeholderSvg;
					// Safely build a short description (escape HTML and truncate)
					var rawDesc = p.product_desc || p.description || '';
					var escapedDesc = $('<div>').text(rawDesc).html();
					var shortDesc = escapedDesc.length > 140 ? escapedDesc.substr(0,137) + '...' : escapedDesc;
					html += `
								<div class="col-md-4">
									<div class="card mb-3 product-card">
										<div class="position-relative">
											<span class="product-badge">${p.brand_name || ''}</span>
											<span class="product-like">❤</span>
											<img src="${img}" class="card-img-top" alt="${p.product_title || ''}">
										</div>
										<div class="card-body">
											<div class="product-meta mb-1">${p.cat_name || ''}</div>
											<h5 class="product-title">${p.product_title || ''}</h5>
											<p class="card-text">${shortDesc}</p>
											<div class="d-flex justify-content-between align-items-center mt-2">
												<div class="product-meta">Views: ${p.views || 0}</div>
												<div class="price">GHC ${(parseFloat(p.product_price) || 0).toFixed(2)}</div>
											</div>
										</div>
									</div>
								</div>
								`;
				});
			} else {
				html = '<div class="col-12 text-muted">No products found.</div>';
			}
			$('#products').html(html);
		}).fail(function(){
			$('#products').html('<div class="col-12 text-danger">Failed to load products.</div>');
		});
	});
	</script>
</body>
</html>
