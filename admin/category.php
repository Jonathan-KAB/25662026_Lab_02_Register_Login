
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
        <span class="me-2">Menu:</span>
        <a href="../login/logout.php" class="btn btn-sm btn-outline-danger">Logout</a>
    </div>

    <div class="container" style="padding-top:120px; max-width:600px;">
        <div class="text-center mb-4">
            <h1>Category Management</h1>
            <p class="text-muted">Admin only view.</p>
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
