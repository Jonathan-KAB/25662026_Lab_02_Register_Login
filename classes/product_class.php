<?php
require_once __DIR__ . '/../settings/db_class.php';

class Product extends db_connection
{
    public $last_query = '';
    private function esc($val)
    {
        if (!$this->db_connect()) return '';
        return mysqli_real_escape_string($this->db, (string)$val);
    }

    /**
     * Insert a new product. $data expects keys: product_cat, product_brand, product_title, product_price, product_desc, product_keywords
     * Returns inserted product_id on success, false on failure.
     */
    public function addProduct(array $data)
    {
        $cat = isset($data['product_cat']) ? (int)$data['product_cat'] : 0;
        $brand = isset($data['product_brand']) ? (int)$data['product_brand'] : 0;
    $title = isset($data['product_title']) ? $this->esc($data['product_title']) : '';
        $price = isset($data['product_price']) ? (float)$data['product_price'] : 0.0;
    $desc = isset($data['product_desc']) ? $this->esc($data['product_desc']) : '';
    $keywords = isset($data['product_keywords']) ? $this->esc($data['product_keywords']) : '';

       $sql = "INSERT INTO products (product_cat, product_brand, product_title, product_price, product_desc, product_keywords) "
           . "VALUES ($cat, $brand, '$title', $price, '$desc', '$keywords')";
       $this->last_query = $sql;

        if ($this->db_write_query($sql)) {
            return $this->last_insert_id();
        }
        return false;
    }

    /**
     * Update an existing product. $data may contain same keys as addProduct.
     */
    public function updateProduct($product_id, array $data)
    {
        $product_id = (int)$product_id;
        $sets = [];
    if (isset($data['product_cat'])) $sets[] = 'product_cat = ' . (int)$data['product_cat'];
    if (isset($data['product_brand'])) $sets[] = 'product_brand = ' . (int)$data['product_brand'];
    if (isset($data['product_title'])) $sets[] = "product_title = '" . $this->esc($data['product_title']) . "'";
    if (isset($data['product_price'])) $sets[] = 'product_price = ' . (float)$data['product_price'];
    if (isset($data['product_desc'])) $sets[] = "product_desc = '" . $this->esc($data['product_desc']) . "'";
    if (isset($data['product_keywords'])) $sets[] = "product_keywords = '" . $this->esc($data['product_keywords']) . "'";
    if (isset($data['product_image'])) $sets[] = "product_image = '" . $this->esc($data['product_image']) . "'";

        if (empty($sets)) return false;

        $sql = "UPDATE products SET " . implode(', ', $sets) . " WHERE product_id = $product_id";
        return $this->db_write_query($sql);
    }

    /**
     * Fetch all products with category and brand names attached.
     * Returns array on success, false on DB error.
     */
    public function fetchAllProducts()
    {
        $sql = "SELECT p.*, c.cat_name, b.brand_name FROM products p 
                LEFT JOIN categories c ON p.product_cat = c.cat_id 
                LEFT JOIN brands b ON p.product_brand = b.brand_id
                ORDER BY p.product_id DESC";
        return $this->db_fetch_all($sql);
    }

    /**
     * Get a single product by id
     */
    public function getProductById($product_id)
    {
        $product_id = (int)$product_id;
        $sql = "SELECT p.*, c.cat_name, b.brand_name FROM products p 
                LEFT JOIN categories c ON p.product_cat = c.cat_id 
                LEFT JOIN brands b ON p.product_brand = b.brand_id
                WHERE p.product_id = $product_id LIMIT 1";
        return $this->db_fetch_one($sql);
    }
    /**
     * Return last DB error message (for debugging)
     */
    public function getLastError()
    {
        if ($this->db == null) {
            $this->db_connect();
        }
        return $this->db ? mysqli_error($this->db) : 'no connection';
    }

    public function getLastQuery()
    {
        return $this->last_query;
    }

}

?>