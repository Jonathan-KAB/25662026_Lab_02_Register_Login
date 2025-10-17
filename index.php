<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Home</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
	<link href="css/app.css" rel="stylesheet">
	
</head>
<body>

	<div class="menu-tray">
		<span class="me-2">Menu:</span>
		<?php
		require_once 'settings/core.php';
		if (!isLoggedIn()): ?>
			<a href="login/register.php" class="btn btn-sm btn-outline-primary">Register</a>
			<a href="login/login.php" class="btn btn-sm btn-outline-secondary">Login</a>
		<?php else: ?>
			<?php if (isAdmin()): ?>
				<a href="admin/category.php" class="btn btn-sm btn-outline-secondary">Category</a>
				<a href="admin/brand.php" class="btn btn-sm btn-outline-secondary">Brand</a>
			<?php endif; ?>
			<a href="login/logout.php" class="btn btn-sm btn-outline-danger">Logout</a>
		<?php endif; ?>
	</div>

	<div class="container" style="padding-top:120px;">
		<div class="text-center">
			<h1>Welcome</h1>
			<p class="text-muted">Use the menu in the top-right to Register or Login.</p>
		</div>
	</div>

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
