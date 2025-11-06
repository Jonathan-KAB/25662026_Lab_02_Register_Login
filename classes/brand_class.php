<?php

require_once __DIR__ . '/../settings/db_class.php';

class Brand extends db_connection
{
    public function __construct()
    {
        $this->db_connect();
    }
    // Add a brand with category and created_by
    public function addBrand($brand_name, $brand_cat, $created_by)
    {
        if (!$this->db_connect()) {
            return false;
        }
        $brand_name = mysqli_real_escape_string($this->db, trim($brand_name));
        $brand_cat = (int)$brand_cat;
        $created_by = (int)$created_by;

        // enforce uniqueness: brand name + category + user
        $check_sql = "SELECT COUNT(*) as cnt FROM brands WHERE brand_name = '$brand_name' AND brand_cat = $brand_cat AND created_by = $created_by";
        $row = $this->db_fetch_one($check_sql);
        if ($row && isset($row['cnt']) && (int)$row['cnt'] > 0) {
            return 'duplicate';
        }

        $sql = "INSERT INTO brands (brand_name, brand_cat, created_by) VALUES ('$brand_name', $brand_cat, $created_by)";
        $ok = $this->db_write_query($sql);
        if ($ok) return true;
        return false;
    }

    // Fetch brands for a user, joined with category name
    public function fetchBrandsByUser($user_id)
    {
        if (!$this->db_connect()) {
            return [];
        }
        $user_id = (int)$user_id;
        $sql = "SELECT b.brand_id, b.brand_name, b.brand_cat, c.cat_name FROM brands b LEFT JOIN categories c ON b.brand_cat = c.cat_id WHERE b.created_by = $user_id ORDER BY c.cat_name, b.brand_name";
        return $this->db_fetch_all($sql);
    }

    public function updateBrand($brand_id, $brand_name, $created_by)
    {
        if (!$this->db_connect()) {
            return false;
        }
        $brand_id = (int)$brand_id;
        $created_by = (int)$created_by;
        $brand_name = mysqli_real_escape_string($this->db, trim($brand_name));

        $sql = "UPDATE brands SET brand_name = '$brand_name' WHERE brand_id = $brand_id AND created_by = $created_by";
        return $this->db_write_query($sql);
    }

    public function deleteBrand($brand_id, $created_by)
    {
        if (!$this->db_connect()) {
            return false;
        }
        $brand_id = (int)$brand_id;
        $created_by = (int)$created_by;
        $sql = "DELETE FROM brands WHERE brand_id = $brand_id AND created_by = $created_by";
        return $this->db_write_query($sql);
    }

    /**
     * Update brand image
     * @param int $brand_id
     * @param string $image_path
     * @return bool
     */
    public function updateBrandImage($brand_id, $image_path)
    {
        if (!$this->db_connect()) {
            return false;
        }
        $brand_id = (int)$brand_id;
        $image_path = mysqli_real_escape_string($this->db, trim($image_path));
        
        $sql = "UPDATE brands SET brand_image = '$image_path' WHERE brand_id = $brand_id";
        return $this->db_write_query($sql);
    }

    /**
     * Get brand with image
     * @param int $brand_id
     * @return array|false
     */
    public function getBrandById($brand_id)
    {
        if (!$this->db_connect()) {
            return false;
        }
        $brand_id = (int)$brand_id;
        $sql = "SELECT b.*, c.cat_name FROM brands b 
                LEFT JOIN categories c ON b.brand_cat = c.cat_id 
                WHERE b.brand_id = $brand_id";
        return $this->db_fetch_one($sql);
    }
}
