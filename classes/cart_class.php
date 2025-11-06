<?php
/**
 * Cart Class
 * Handles all cart-related database operations
 */

require_once __DIR__ . '/../settings/db_class.php';

class Cart extends db_connection
{
    /**
     * Add item to cart
     */
    public function addToCart($productId, $ipAddress, $customerId, $quantity = 1)
    {
        if (!$this->db_connect()) {
            return false;
        }
        
        // Escape inputs
        $productId = (int)$productId;
        $ipAddress = mysqli_real_escape_string($this->db, $ipAddress);
        $customerId = $customerId ? (int)$customerId : 'NULL';
        $quantity = (int)$quantity;
        
        // Check if item already exists in cart
        $existingItem = $this->getCartItem($productId, $ipAddress, $customerId == 'NULL' ? null : $customerId);
        
        if ($existingItem) {
            // Update quantity if item exists
            $newQty = $existingItem['qty'] + $quantity;
            if ($customerId === 'NULL') {
                $sql = "UPDATE `cart` SET `qty` = $newQty WHERE `p_id` = $productId AND `ip_add` = '$ipAddress' AND `c_id` IS NULL";
            } else {
                $sql = "UPDATE `cart` SET `qty` = $newQty WHERE `p_id` = $productId AND `c_id` = $customerId";
            }
            return $this->db_write_query($sql);
        } else {
            // Insert new item
            $sql = "INSERT INTO `cart` (`p_id`, `ip_add`, `c_id`, `qty`) VALUES ($productId, '$ipAddress', $customerId, $quantity)";
            return $this->db_write_query($sql);
        }
    }

    /**
     * Get specific cart item
     */
    public function getCartItem($productId, $ipAddress, $customerId = null)
    {
        if (!$this->db_connect()) {
            return false;
        }
        
        $productId = (int)$productId;
        $ipAddress = mysqli_real_escape_string($this->db, $ipAddress);
        
        if ($customerId) {
            $customerId = (int)$customerId;
            $sql = "SELECT * FROM `cart` WHERE `p_id` = $productId AND `c_id` = $customerId";
        } else {
            $sql = "SELECT * FROM `cart` WHERE `p_id` = $productId AND `ip_add` = '$ipAddress' AND `c_id` IS NULL";
        }
        
        return $this->db_fetch_one($sql);
    }

    /**
     * Get all cart items for a user/IP
     */
    public function getCartItems($ipAddress, $customerId = null)
    {
        if (!$this->db_connect()) {
            return [];
        }
        
        $ipAddress = mysqli_real_escape_string($this->db, $ipAddress);
        
        if ($customerId) {
            $customerId = (int)$customerId;
            $sql = "SELECT c.*, p.product_title, p.product_price, p.product_image, p.product_desc,
                           b.brand_name, cat.cat_name
                    FROM `cart` c
                    JOIN `products` p ON c.p_id = p.product_id
                    LEFT JOIN `brands` b ON p.product_brand = b.brand_id
                    LEFT JOIN `categories` cat ON p.product_cat = cat.cat_id
                    WHERE c.c_id = $customerId
                    ORDER BY p.product_title";
        } else {
            $sql = "SELECT c.*, p.product_title, p.product_price, p.product_image, p.product_desc,
                           b.brand_name, cat.cat_name
                    FROM `cart` c
                    JOIN `products` p ON c.p_id = p.product_id
                    LEFT JOIN `brands` b ON p.product_brand = b.brand_id
                    LEFT JOIN `categories` cat ON p.product_cat = cat.cat_id
                    WHERE c.ip_add = '$ipAddress' AND c.c_id IS NULL
                    ORDER BY p.product_title";
        }
        
        return $this->db_fetch_all($sql);
    }

    /**
     * Update cart item quantity
     */
    public function updateCartQuantity($productId, $ipAddress, $customerId, $quantity)
    {
        if (!$this->db_connect()) {
            return false;
        }
        
        $productId = (int)$productId;
        $ipAddress = mysqli_real_escape_string($this->db, $ipAddress);
        $quantity = (int)$quantity;
        
        if ($quantity <= 0) {
            return $this->removeFromCart($productId, $ipAddress, $customerId);
        }
        
        if ($customerId) {
            $customerId = (int)$customerId;
            $sql = "UPDATE `cart` SET `qty` = $quantity WHERE `p_id` = $productId AND `c_id` = $customerId";
        } else {
            $sql = "UPDATE `cart` SET `qty` = $quantity WHERE `p_id` = $productId AND `ip_add` = '$ipAddress' AND `c_id` IS NULL";
        }
        
        return $this->db_write_query($sql);
    }

    /**
     * Remove item from cart
     */
    public function removeFromCart($productId, $ipAddress, $customerId = null)
    {
        if (!$this->db_connect()) {
            return false;
        }
        
        $productId = (int)$productId;
        $ipAddress = mysqli_real_escape_string($this->db, $ipAddress);
        
        if ($customerId) {
            $customerId = (int)$customerId;
            $sql = "DELETE FROM `cart` WHERE `p_id` = $productId AND `c_id` = $customerId";
        } else {
            $sql = "DELETE FROM `cart` WHERE `p_id` = $productId AND `ip_add` = '$ipAddress' AND `c_id` IS NULL";
        }
        
        return $this->db_write_query($sql);
    }

    /**
     * Clear entire cart
     */
    public function clearCart($ipAddress, $customerId = null)
    {
        if (!$this->db_connect()) {
            return false;
        }
        
        $ipAddress = mysqli_real_escape_string($this->db, $ipAddress);
        
        if ($customerId) {
            $customerId = (int)$customerId;
            $sql = "DELETE FROM `cart` WHERE `c_id` = $customerId";
        } else {
            $sql = "DELETE FROM `cart` WHERE `ip_add` = '$ipAddress' AND `c_id` IS NULL";
        }
        
        return $this->db_write_query($sql);
    }

    /**
     * Get cart count
     */
    public function getCartCount($ipAddress, $customerId = null)
    {
        if (!$this->db_connect()) {
            return 0;
        }
        
        $ipAddress = mysqli_real_escape_string($this->db, $ipAddress);
        
        if ($customerId) {
            $customerId = (int)$customerId;
            $sql = "SELECT SUM(qty) as total FROM `cart` WHERE `c_id` = $customerId";
        } else {
            $sql = "SELECT SUM(qty) as total FROM `cart` WHERE `ip_add` = '$ipAddress' AND `c_id` IS NULL";
        }
        
        $result = $this->db_fetch_one($sql);
        return $result ? (int)$result['total'] : 0;
    }

    /**
     * Get cart total price
     */
    public function getCartTotal($ipAddress, $customerId = null)
    {
        if (!$this->db_connect()) {
            return 0.00;
        }
        
        $ipAddress = mysqli_real_escape_string($this->db, $ipAddress);
        
        if ($customerId) {
            $customerId = (int)$customerId;
            $sql = "SELECT SUM(c.qty * p.product_price) as total
                    FROM `cart` c
                    JOIN `products` p ON c.p_id = p.product_id
                    WHERE c.c_id = $customerId";
        } else {
            $sql = "SELECT SUM(c.qty * p.product_price) as total
                    FROM `cart` c
                    JOIN `products` p ON c.p_id = p.product_id
                    WHERE c.ip_add = '$ipAddress' AND c.c_id IS NULL";
        }
        
        $result = $this->db_fetch_one($sql);
        return $result ? (float)$result['total'] : 0.00;
    }

    /**
     * Merge guest cart to user cart on login
     */
    public function mergeGuestCart($ipAddress, $customerId)
    {
        if (!$this->db_connect()) {
            return false;
        }
        
        // Get guest cart items
        $guestItems = $this->getCartItems($ipAddress, null);
        
        if (empty($guestItems)) {
            return true;
        }
        
        // For each guest item, add to user cart
        foreach ($guestItems as $item) {
            $this->addToCart($item['p_id'], $ipAddress, $customerId, $item['qty']);
        }
        
        // Clear guest cart
        $ipAddress = mysqli_real_escape_string($this->db, $ipAddress);
        $sql = "DELETE FROM `cart` WHERE `ip_add` = '$ipAddress' AND `c_id` IS NULL";
        return $this->db_write_query($sql);
    }
}
?>
