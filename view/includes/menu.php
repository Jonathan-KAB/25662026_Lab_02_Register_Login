<!-- Navigation Menu Tray -->
<div class="menu-tray">
    <button class="menu-toggle" onclick="toggleMenu()">☰ Menu</button>
    <div class="menu-items" id="menuItems">
        <?php
        $currentPage = basename($_SERVER['PHP_SELF']);
        $isHomePage = ($currentPage === 'index.php');
        $homePrefix = $isHomePage ? '' : '../';
        $viewPrefix = $isHomePage ? 'view/' : '';
        
        // Debug - comment out after checking
        echo "<!-- Debug: isLoggedIn = " . (isLoggedIn() ? 'true' : 'false') . " -->";
        echo "<!-- Debug: user_id = " . ($_SESSION['user_id'] ?? 'not set') . " -->";
        echo "<!-- Debug: customer_id = " . ($_SESSION['customer_id'] ?? 'not set') . " -->";
        ?>
        
        <a href="<?= $homePrefix ?>index.php" class="btn btn-sm <?= $currentPage === 'index.php' ? 'btn-primary' : 'btn-outline-secondary' ?>">Home</a>
        <a href="<?= $viewPrefix ?>all_product.php" class="btn btn-sm <?= $currentPage === 'all_product.php' ? 'btn-primary' : 'btn-outline-secondary' ?>">All Products</a>
        <a href="<?= $viewPrefix ?>cart.php" class="btn btn-sm btn-outline-secondary">
            Cart <?php if (isset($cartCount) && $cartCount > 0): ?><span class="cart-badge" id="cart-count"><?= $cartCount ?></span><?php endif; ?>
        </a>
        
        <?php if (isLoggedIn()): ?>
            <?php 
            $userRole = $_SESSION['user_role'] ?? 1;
            if ($userRole == 2): ?>
                <a href="<?= $homePrefix ?>admin/category.php" class="btn btn-sm btn-outline-secondary">Admin</a>
            <?php elseif ($userRole == 3): ?>
                <a href="<?= $viewPrefix ?>seller_dashboard.php" class="btn btn-sm btn-outline-secondary">Seller Dashboard</a>
            <?php else: ?>
                <a href="<?= $viewPrefix ?>dashboard.php" class="btn btn-sm btn-outline-secondary">Dashboard</a>
            <?php endif; ?>
            <a href="<?= $homePrefix ?>login/logout.php" class="btn btn-sm btn-outline-danger">Logout</a>
        <?php else: ?>
            <a href="<?= $homePrefix ?>login/login.php" class="btn btn-sm btn-outline-secondary">Login</a>
            <a href="<?= $homePrefix ?>login/register.php" class="btn btn-sm btn-outline-primary">Register</a>
        <?php endif; ?>
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
