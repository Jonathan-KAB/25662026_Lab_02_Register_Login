<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once '../settings/core.php';
// Redirect if not logged in or not admin
if (!isLoggedIn() || !isAdmin()) {
    header("Location: ../login/login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Brand Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/app.css" rel="stylesheet">
    <style>
        .menu-tray {
            position: fixed;
            top: 16px;
            right: 16px;
            background: rgba(255,255,255,0.95);
            border: 1px solid #e6e6e6;
            border-radius: 8px;
            padding: 6px 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.06);
            z-index: 1000;
        }
        .menu-tray a { margin-left: 8px; }
    </style>
</head>
<body>
    <div class="menu-tray">
        <a href="../index.php" class="btn btn-sm btn-outline-info">Home</a>
        <a href="category.php" class="btn btn-sm btn-outline-primary">Categories</a>
        <a href="brand.php" class="btn btn-sm btn-outline-secondary">Brands</a>
        <a href="product.php" class="btn btn-sm btn-outline-primary">Products</a>
        <a href="../login/logout.php" class="btn btn-sm btn-outline-danger">Logout</a>
    </div>

    <div class="container" style="padding-top:80px;">
        <div class="text-center mb-4">
            <h1>Brand Management</h1>
            <p class="text-muted">Admin only view.</p>
        </div>

        <div class="card mb-4">
                <div class="card-header">Add New Brand</div>
                <div class="card-body">
                    <form id="add-brand-form">
                        <div class="mb-3">
                            <label for="brand_name" class="form-label">Brand Name</label>
                            <input type="text" class="form-control" id="brand_name" name="brand_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="brand_cat" class="form-label">Category</label>
                            <select id="brand_cat" name="brand_cat" class="form-select" required>
                                <option value="">Loading categories...</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Add Brand</button>
                    </form>
                </div>
            </div>

        <div class="card">
            <div class="card-header">Brands</div>
            <div class="card-body">
                <div id="brands-container" class="row g-3">
                    <!-- Brands grouped by category will render here -->
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../js/brand.js"></script>
</body>
</html>
