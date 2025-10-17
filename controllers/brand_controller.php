<?php

require_once __DIR__ . '/../classes/brand_class.php';

/**
 * Controller: add a brand
 * @return bool true on success, false on failure
 */
function add_brand_ctr($brand_name, $brand_cat, $created_by)
{
    $b = new Brand();
    return $b->addBrand($brand_name, $brand_cat, $created_by);
}

/**
 * Controller: fetch brands for a specific user
 * @return array list of brands (each as assoc array) or empty array
 */
function fetch_brands_by_user_ctr($user_id)
{
    $b = new Brand();
    return $b->fetchBrandsByUser($user_id);
}

/**
 * Controller: update a brand (only if owned by the user)
 * @return bool
 */
function update_brand_ctr($brand_id, $brand_name, $created_by)
{
    $b = new Brand();
    return $b->updateBrand($brand_id, $brand_name, $created_by);
}

/**
 * Controller: delete a brand (only if owned by the user)
 * @return bool
 */
function delete_brand_ctr($brand_id, $created_by)
{
    $b = new Brand();
    return $b->deleteBrand($brand_id, $created_by);
}

?>