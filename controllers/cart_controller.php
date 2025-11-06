<?php
/**
 * Cart Controller
 * Business logic layer for cart operations
 */

require_once __DIR__ . '/../classes/cart_class.php';

/**
 * Add item to cart
 */
function add_to_cart_ctr($productId, $ipAddress, $customerId = null, $quantity = 1)
{
    $cart = new Cart();
    return $cart->addToCart($productId, $ipAddress, $customerId, $quantity);
}

/**
 * Get cart items
 */
function get_cart_items_ctr($ipAddress, $customerId = null)
{
    $cart = new Cart();
    return $cart->getCartItems($ipAddress, $customerId);
}

/**
 * Update cart quantity
 */
function update_cart_quantity_ctr($productId, $ipAddress, $customerId, $quantity)
{
    $cart = new Cart();
    return $cart->updateCartQuantity($productId, $ipAddress, $customerId, $quantity);
}

/**
 * Remove from cart
 */
function remove_from_cart_ctr($productId, $ipAddress, $customerId = null)
{
    $cart = new Cart();
    return $cart->removeFromCart($productId, $ipAddress, $customerId);
}

/**
 * Clear cart
 */
function clear_cart_ctr($ipAddress, $customerId = null)
{
    $cart = new Cart();
    return $cart->clearCart($ipAddress, $customerId);
}

/**
 * Get cart count
 */
function get_cart_count_ctr($ipAddress, $customerId = null)
{
    $cart = new Cart();
    return $cart->getCartCount($ipAddress, $customerId);
}

/**
 * Get cart total
 */
function get_cart_total_ctr($ipAddress, $customerId = null)
{
    $cart = new Cart();
    return $cart->getCartTotal($ipAddress, $customerId);
}

/**
 * Merge guest cart to user cart
 */
function merge_guest_cart_ctr($ipAddress, $customerId)
{
    $cart = new Cart();
    return $cart->mergeGuestCart($ipAddress, $customerId);
}
?>