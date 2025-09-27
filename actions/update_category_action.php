<?php
require_once '../settings/db_class.php';

class Category extends db_connection {
    public function updateCategory($cat_id, $cat_name) {
        if (!$this->db_connect()) {
            return false;
        }
        $cat_id = (int)$cat_id;
        $cat_name = mysqli_real_escape_string($this->db, $cat_name);
        $sql = "UPDATE categories SET cat_name = '$cat_name' WHERE cat_id = $cat_id";
        return $this->db_write_query($sql);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cat_id'], $_POST['cat_name'])) {
    $category = new Category();
    if ($category->updateCategory($_POST['cat_id'], $_POST['cat_name'])) {
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