<?php
require_once '../settings/db_class.php';

class Category extends db_connection {
    public function deleteCategory($cat_id) {
        if (!$this->db_connect()) {
            return false;
        }
        $cat_id = (int)$cat_id;
        $sql = "DELETE FROM categories WHERE cat_id = $cat_id";
        return $this->db_write_query($sql);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cat_id'])) {
    $category = new Category();
    if ($category->deleteCategory($_POST['cat_id'])) {
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