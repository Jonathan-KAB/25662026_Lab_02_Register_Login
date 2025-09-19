<?php

require_once '../classes/customer_class.php';

// added country and city parameters
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

// ADD THIS FUNCTION - it's missing but your action file needs it
function email_exists_ctr($email)
{
    $user = new User();
    $existing_user = $user->getUserByEmail($email);
    return $existing_user !== false;
}

// ADD THIS FUNCTION - for login authentication
function authenticate_user_ctr($email, $password)
{
    $user = new User();
    return $user->authenticateUser($email, $password);
}

?>