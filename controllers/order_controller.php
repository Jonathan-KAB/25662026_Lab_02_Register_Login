<?php
require_once __DIR__ . '/../classes/order_class.php';

/**
 * Get all orders (admin)
 */
function get_all_orders_ctr()
{
    $order = new Order();
    return $order->getAllOrders();
}

/**
 * Get orders by customer
 */
function get_orders_by_customer_ctr($customer_id)
{
    $order = new Order();
    return $order->getOrdersByCustomer($customer_id);
}

/**
 * Get single order by ID
 */
function get_order_by_id_ctr($order_id)
{
    $order = new Order();
    return $order->getOrderById($order_id);
}

/**
 * Get order items
 */
function get_order_items_ctr($order_id)
{
    $order = new Order();
    return $order->getOrderItems($order_id);
}

/**
 * Update order status
 */
function update_order_status_ctr($order_id, $status)
{
    $order = new Order();
    return $order->updateOrderStatus($order_id, $status);
}

/**
 * Create new order
 */
function create_order_ctr($customer_id, $invoice_no)
{
    $order = new Order();
    return $order->createOrder($customer_id, $invoice_no);
}

/**
 * Add order detail
 */
function add_order_detail_ctr($order_id, $product_id, $qty)
{
    $order = new Order();
    return $order->addOrderDetail($order_id, $product_id, $qty);
}

/**
 * Add payment
 */
function add_payment_ctr($customer_id, $order_id, $amount, $currency = 'GHS')
{
    $order = new Order();
    return $order->addPayment($customer_id, $order_id, $amount, $currency);
}

/**
 * Get orders by seller
 */
function get_orders_by_seller_ctr($seller_id)
{
    $order = new Order();
    return $order->getOrdersBySeller($seller_id);
}

