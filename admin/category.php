
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
    <title>Category Management - SeamLink Admin</title>
    <link href="../css/app.css" rel="stylesheet">
</head>
<body>
    <?php include __DIR__ . '/includes/admin_menu.php'; ?>

    <div class="page-header">
        <div class="container">
            <h1>Category Management</h1>
            <p>Organize your products into categories</p>
        </div>
    </div>

    <div class="container" style="margin-top: 40px; margin-bottom: 60px;">
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
