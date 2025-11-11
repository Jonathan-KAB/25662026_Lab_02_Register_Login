<!-- Navigation Menu Tray -->
<div class="menu-tray">
    <button class="menu-toggle" onclick="toggleMenu()">☰ Menu</button>
    <div class="menu-items" id="menuItems">
        <?php
        $currentPage = basename($_SERVER['PHP_SELF']);
        ?>
        
        <a href="../index.php" class="btn btn-sm btn-outline-secondary">Home</a>
        <a href="category.php" class="btn btn-sm <?= $currentPage === 'category.php' ? 'btn-primary' : 'btn-outline-secondary' ?>">Categories</a>
        <a href="brand.php" class="btn btn-sm <?= $currentPage === 'brand.php' ? 'btn-primary' : 'btn-outline-secondary' ?>">Brands</a>
        <a href="product.php" class="btn btn-sm <?= $currentPage === 'product.php' ? 'btn-primary' : 'btn-outline-secondary' ?>">Products</a>
        <a href="orders.php" class="btn btn-sm <?= $currentPage === 'orders.php' ? 'btn-primary' : 'btn-outline-secondary' ?>">Orders</a>
        <a href="../view/profile.php" class="btn btn-sm btn-outline-secondary">Profile</a>
        <a href="../login/logout.php" class="btn btn-sm btn-outline-danger">Logout</a>
    </div>
</div>

<script>
    function toggleMenu() {
        const menuItems = document.getElementById('menuItems');
        const menuToggle = document.querySelector('.menu-toggle');
        menuItems.classList.toggle('active');
        
        // Update button text
        if (menuItems.classList.contains('active')) {
            menuToggle.innerHTML = '✕ Close';
        } else {
            menuToggle.innerHTML = '☰ Menu';
        }
    }
    
    // Close menu when clicking outside
    document.addEventListener('click', function(event) {
        const menuTray = document.querySelector('.menu-tray');
        const menuToggle = document.querySelector('.menu-toggle');
        if (menuTray && !menuTray.contains(event.target)) {
            const menuItems = document.getElementById('menuItems');
            if (menuItems && menuItems.classList.contains('active')) {
                menuItems.classList.remove('active');
                if (menuToggle) {
                    menuToggle.innerHTML = '☰ Menu';
                }
            }
        }
    });
</script>
