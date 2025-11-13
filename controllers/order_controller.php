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
 * Add order details (alias for add_order_detail_ctr)
 */
function add_order_details_ctr($order_id, $product_id, $qty)
{
    return add_order_detail_ctr($order_id, $product_id, $qty);
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
 * Record payment (alias for add_payment_ctr)
 */
function record_payment_ctr($customer_id, $order_id, $amount, $currency = 'GHS')
{
    return add_payment_ctr($customer_id, $order_id, $amount, $currency);
}

/**
 * Add payment with Paystack details
 */
function add_payment_with_paystack_ctr($customer_id, $order_id, $amount, $currency = 'GHS', $transaction_ref = null, $payment_method = 'paystack', $authorization_code = null, $payment_channel = null)
{
    $order = new Order();
    return $order->addPaymentWithPaystack($customer_id, $order_id, $amount, $currency, $transaction_ref, $payment_method, $authorization_code, $payment_channel);
}

/**
 * Get payment by transaction reference
 */
function get_payment_by_transaction_ref_ctr($transaction_ref)
{
    $order = new Order();
    return $order->getPaymentByTransactionRef($transaction_ref);
}

/**
 * Update payment with Paystack response
 */
function update_payment_with_paystack_ctr($pay_id, $transaction_ref, $authorization_code = null, $payment_channel = null)
{
    $order = new Order();
    return $order->updatePaymentWithPaystack($pay_id, $transaction_ref, $authorization_code, $payment_channel);
}

/**
 * Get order by transaction reference
 */
function get_order_by_transaction_ref_ctr($transaction_ref)
{
    $order = new Order();
    return $order->getOrderByTransactionRef($transaction_ref);
}

/**
 * Get orders by seller
 */
function get_orders_by_seller_ctr($seller_id)
{
    $order = new Order();
    return $order->getOrdersBySeller($seller_id);
}

