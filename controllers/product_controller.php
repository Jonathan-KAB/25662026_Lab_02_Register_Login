<?php
require_once __DIR__ . '/../classes/product_class.php';

function add_product_ctr($data) {
    $p = new Product();
    return $p->addProduct($data);
}

function update_product_ctr($product_id, $data) {
    $p = new Product();
    return $p->updateProduct($product_id, $data);
}

function fetch_all_products_ctr() {
    $p = new Product();
    return $p->fetchAllProducts();
}

function get_product_ctr($product_id) {
    $p = new Product();
    return $p->getProductById($product_id);
}
