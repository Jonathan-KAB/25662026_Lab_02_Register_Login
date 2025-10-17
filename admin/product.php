<?php
require_once '../settings/core.php';
if (!isLoggedIn() || !isAdmin()) {
    header('Location: ../login/login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Product Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/app.css" rel="stylesheet">
    <style>.menu-tray{position:fixed;top:16px;right:16px;z-index:1000}</style>
</head>
<body>
    <div class="menu-tray">
        <a href="brand.php" class="btn btn-sm btn-outline-primary">Brands</a>
        <a href="category.php" class="btn btn-sm btn-outline-primary">Categories</a>
        <a href="../login/logout.php" class="btn btn-sm btn-outline-danger">Logout</a>
    </div>

    <div class="container" style="padding-top:80px;">
        <div class="text-center mb-4">
            <h1>Product Management</h1>
            <p class="text-muted">Add or edit products</p>
        </div>

        <div class="card mb-4">
            <div class="card-header">Add / Edit Product</div>
            <div class="card-body">
                <form id="product-form" enctype="multipart/form-data">
                    <input type="hidden" id="product_id" name="product_id" value="0">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Category</label>
                            <select id="product_cat" name="product_cat" class="form-select" required>
                                <option value="">Loading...</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Brand</label>
                            <select id="product_brand" name="product_brand" class="form-select" required>
                                <option value="">Loading...</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Title</label>
                        <input id="product_title" name="product_title" class="form-control" required>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Price</label>
                            <input id="product_price" name="product_price" type="number" step="0.01" class="form-control" required>
                        </div>
                        <div class="col-md-8 mb-3">
                            <label class="form-label">Keywords</label>
                            <input id="product_keywords" name="product_keywords" class="form-control">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea id="product_desc" name="product_desc" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Image</label>
                        <input id="product_image" name="image" type="file" accept="image/*" class="form-control">
                        <div class="form-text">Upload an image after creating a product (image upload handled separately).</div>
                        <div id="image-preview" class="mt-2"></div>
                    </div>
                    <button class="btn btn-primary" id="save-product">Save Product</button>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">Products</div>
            <div class="card-body">
                <div id="products-container" class="row g-3"></div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../js/product.js"></script>
</body>
</html>
 
