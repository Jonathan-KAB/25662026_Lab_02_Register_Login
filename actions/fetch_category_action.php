<?php
require_once '../settings/db_class.php';

class Category extends db_connection {
    public function fetchAllCategories() {
        if (!$this->db_connect()) {
            return [];
        }
        $sql = "SELECT * FROM categories ORDER BY cat_id DESC";
        return $this->db_fetch_all($sql);
    }
}

header('Content-Type: application/json');
$category = new Category();
$categories = $category->fetchAllCategories();
echo json_encode($categories);