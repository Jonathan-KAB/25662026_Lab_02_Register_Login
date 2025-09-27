<?php
require_once '../settings/db_class.php';

class Category extends db_connection {
    public function addCategory($cat_name) {
        if (!$this->db_connect()) {
            return false;
        }
        $cat_name = mysqli_real_escape_string($this->db, $cat_name);
        $sql = "INSERT INTO categories (cat_name) VALUES ('$cat_name')";
        return $this->db_write_query($sql);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['category_name'])) {
    $category = new Category();
    if ($category->addCategory($_POST['category_name'])) {
        http_response_code(200);
        exit();
    } else {
        http_response_code(500);
        exit();
    }
} else {
    http_response_code(400);
    exit();
}