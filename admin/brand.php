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
    <title>Brand Management - SeamLink Admin</title>
    <link href="../css/app.css" rel="stylesheet">
</head>
<body>
    <?php include __DIR__ . '/includes/admin_menu.php'; ?>

    <div class="page-header">
        <div class="container">
            <h1>Brand Management</h1>
            <p>Manage your product brands</p>
        </div>
    </div>

    <div class="container" style="margin-top: 40px; margin-bottom: 60px;">
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
                <div id="brands-container">
                    <!-- Brands grouped by category will render here -->
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../js/brand.js"></script>
</body>
</html>
