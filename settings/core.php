<?php
// Configure session settings
ini_set('session.cookie_path', '/');
ini_set('session.cookie_domain', '');
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Debug session
error_log("Core.php - Session started. ID: " . session_id());
error_log("Core.php - Session data: " . print_r($_SESSION, true));

//for header redirection
ob_start();

//funtion to check for login
function isLoggedIn(){
    if (!isset($_SESSION['user_id'])) {
        return false;
    }
    else{
        return true;
    }
}

function isAdmin(){
    if (isLoggedIn()){
        return $_SESSION['user_role'] == 2;
    }
}


//function to get user ID


//function to check for role (admin, customer, etc)




?>