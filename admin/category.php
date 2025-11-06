
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
    <title>Category Management</title>
    <link href="../css/app.css" rel="stylesheet">
</head>
<body>
    <div class="menu-tray">
        <a href="../index.php" class="btn btn-sm btn-outline-info">Home</a>
        <a href="category.php" class="btn btn-sm btn-outline-secondary">Categories</a>
        <a href="brand.php" class="btn btn-sm btn-outline-primary">Brands</a>
        <a href="product.php" class="btn btn-sm btn-outline-primary">Products</a>
        <a href="../login/logout.php" class="btn btn-sm btn-outline-danger">Logout</a>
    </div>

    <div class="container" style="padding-top:100px;">
        <div class="text-center mb-4">
            <h1>Category Management</h1>
            <p class="text-muted">Organize your products into categories</p>
        </div>

        <!-- Category Create Form -->
        <div class="card mb-4">
            <div class="card-header">Add New Category</div>
            <div class="card-body">
                <form id="add-category-form">
                    <div class="mb-3">
                        <label for="category_name" class="form-label">Category Name</label>
                        <input type="text" class="form-control" id="category_name" name="category_name" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Add Category</button>
                </form>
            </div>
        </div>

        <!-- Category List (Read, Update, Delete) -->
        <div class="card">
            <div class="card-header">Categories</div>
            <div class="card-body">
                <table class="table" id="category-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Categories will be loaded here by JS -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../js/category.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
