<?php

require_once '../classes/customer_class.php';

function register_user_ctr($name, $email, $password, $phone_number, $country, $city, $role)
{
    $user = new User();
    $user_id = $user->createUser($name, $email, $password, $phone_number, $country, $city, $role);
    if ($user_id) {
        return $user_id;
    }
    return false;
}

function get_user_by_email_ctr($email)
{
    $user = new User();
    return $user->getUserByEmail($email);
}

function email_exists_ctr($email)
{
    $user = new User();
    $existing_user = $user->getUserByEmail($email);
    return $existing_user !== false;
}

?>