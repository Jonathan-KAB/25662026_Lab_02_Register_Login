<?php
require_once __DIR__ . '/../settings/db_class.php';

class Order extends db_connection
{
    /**
     * Get all orders with customer details
     */
    public function getAllOrders()
    {
        $sql = "SELECT o.*, c.customer_name, c.customer_email, 
                COALESCE(p.amt, 0) as order_total
                FROM orders o
                LEFT JOIN customer c ON o.customer_id = c.customer_id
                LEFT JOIN payment p ON o.order_id = p.order_id
                ORDER BY o.order_date DESC, o.order_id DESC";
        
        return $this->db_fetch_all($sql);
    }

    /**
     * Get orders by customer ID
     */
    public function getOrdersByCustomer($customer_id)
    {
        $customer_id = (int)$customer_id;
        $sql = "SELECT o.*, COALESCE(p.amt, 0) as order_total
                FROM orders o
                LEFT JOIN payment p ON o.order_id = p.order_id
                WHERE o.customer_id = $customer_id
                ORDER BY o.order_date DESC, o.order_id DESC";
        
        return $this->db_fetch_all($sql);
    }

    /**
     * Get single order with details
     */
    public function getOrderById($order_id)
    {
        $order_id = (int)$order_id;
        $sql = "SELECT o.*, c.customer_name, c.customer_email, c.customer_contact,
                c.customer_city, c.customer_country,
                COALESCE(p.amt, 0) as order_total, p.currency, p.payment_date
                FROM orders o
                LEFT JOIN customer c ON o.customer_id = c.customer_id
                LEFT JOIN payment p ON o.order_id = p.order_id
                WHERE o.order_id = $order_id";
        
        return $this->db_fetch_one($sql);
    }

    /**
     * Get order items
     */
    public function getOrderItems($order_id)
    {
        $order_id = (int)$order_id;
        $sql = "SELECT od.*, p.product_title, p.product_price, p.product_image
                FROM orderdetails od
                LEFT JOIN products p ON od.product_id = p.product_id
                WHERE od.order_id = $order_id";
        
        return $this->db_fetch_all($sql);
    }

    /**
     * Update order status
     */
    public function updateOrderStatus($order_id, $status)
    {
        if (!$this->db_connect()) {
            return false;
        }
        
        $order_id = (int)$order_id;
        $status = mysqli_real_escape_string($this->db, $status);
        $sql = "UPDATE orders SET order_status = '$status' WHERE order_id = $order_id";
        return $this->db_write_query($sql);
    }

    /**
     * Create a new order
     */
    public function createOrder($customer_id, $invoice_no)
    {
        $customer_id = (int)$customer_id;
        $invoice_no = (int)$invoice_no;
        $sql = "INSERT INTO orders (customer_id, invoice_no, order_date, order_status) 
                VALUES ($customer_id, $invoice_no, CURDATE(), 'pending')";
        
        return $this->db_write_query($sql);
    }

    /**
     * Add order detail (item)
     */
    public function addOrderDetail($order_id, $product_id, $qty)
    {
        $order_id = (int)$order_id;
        $product_id = (int)$product_id;
        $qty = (int)$qty;
        $sql = "INSERT INTO orderdetails (order_id, product_id, qty) 
                VALUES ($order_id, $product_id, $qty)";
        
        return $this->db_write_query($sql);
    }

    /**
     * Add payment
     */
    public function addPayment($customer_id, $order_id, $amount, $currency = 'GHS')
    {
        if (!$this->db_connect()) {
            return false;
        }
        
        $customer_id = (int)$customer_id;
        $order_id = (int)$order_id;
        $amount = (float)$amount;
        $currency = mysqli_real_escape_string($this->db, $currency);
        
        $sql = "INSERT INTO payment (amt, customer_id, order_id, currency, payment_date) 
                VALUES ($amount, $customer_id, $order_id, '$currency', CURDATE())";
        
        return $this->db_write_query($sql);
    }

    /**
     * Get orders by seller (vendor)
     */
    public function getOrdersBySeller($seller_id)
    {
        $seller_id = (int)$seller_id;
        $sql = "SELECT DISTINCT o.*, c.customer_name, c.customer_email,
                COALESCE(p.amt, 0) as order_total
                FROM orders o
                LEFT JOIN customer c ON o.customer_id = c.customer_id
                LEFT JOIN payment p ON o.order_id = p.order_id
                LEFT JOIN orderdetails od ON o.order_id = od.order_id
                LEFT JOIN products pr ON od.product_id = pr.product_id
                WHERE pr.seller_id = $seller_id
                ORDER BY o.order_date DESC, o.order_id DESC";
        
        return $this->db_fetch_all($sql);
    }

    /**
     * Get the last inserted order ID
     */
    public function getLastOrderId()
    {
        return $this->last_insert_id();
    }
}
