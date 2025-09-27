<?php

require_once '../settings/db_class.php';

/**
 * User class for handling customer operations
 */
class User extends db_connection
{
    private $user_id;
    private $name;
    private $email;
    private $password;
    private $role;
    private $country;
    private $city;
    private $date_created;
    private $phone_number;
    private $customer_image;

    public function __construct($user_id = null)
    {
        // Don't call parent constructor - just connect when needed
        if ($user_id) {
            $this->user_id = $user_id;
            $this->loadUser();
        }
    }

    /**
     * Load user data from database
     * @param int $user_id
     * @return bool
     */
    private function loadUser($user_id = null)
    {
        if ($user_id) {
            $this->user_id = $user_id;
        }
        if (!$this->user_id) {
            return false;
        }

        // Use the db_connection methods instead of direct PDO
        $sql = "SELECT * FROM customer WHERE customer_id = " . (int)$this->user_id;
        $result = $this->db_fetch_one($sql);
        
        if ($result) {
            $this->name = $result['customer_name'];
            $this->email = $result['customer_email'];
            $this->role = $result['user_role'];
            $this->country = $result['customer_country'];
            $this->city = $result['customer_city'];
            $this->phone_number = $result['customer_contact'];
            $this->customer_image = isset($result['customer_image']) ? $result['customer_image'] : null;
            return true;
        }
        return false;
    }

    /**
     * Create a new user
     * @param string $name
     * @param string $email
     * @param string $password
     * @param string $phone_number
     * @param string $country
     * @param string $city
     * @param int $role
     * @return int|false User ID on success, false on failure
     */
    public function createUser($name, $email, $password, $phone_number, $country, $city, $role)
    {
        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Use mysqli_real_escape_string directly since we don't have escape_string method
        if (!$this->db_connect()) {
            return false;
        }
        
        $name = mysqli_real_escape_string($this->db, $name);
        $email = mysqli_real_escape_string($this->db, $email);
        $phone_number = mysqli_real_escape_string($this->db, $phone_number);
        $country = mysqli_real_escape_string($this->db, $country);
        $city = mysqli_real_escape_string($this->db, $city);
        $role = (int)$role;
        
        $sql = "INSERT INTO customer (customer_name, customer_email, customer_pass, customer_contact, customer_country, customer_city, user_role) 
                VALUES ('$name', '$email', '$hashed_password', '$phone_number', '$country', '$city', $role)";
        
        if ($this->db_write_query($sql)) {
            return $this->last_insert_id();
        }
        return false;
    }

    /**
     * Get user by email
     * @param string $email
     * @return array|false
     */
    public function getUserByEmail($email)
    {
        if (!$this->db_connect()) {
            return false;
        }
        $email = mysqli_real_escape_string($this->db, $email);
        $sql = "SELECT * FROM customer WHERE customer_email = '$email'";
        return $this->db_fetch_one($sql);
    }

    /**
     * Get user by ID
     * @param int $user_id
     * @return array|false
     */
    public function getUserById($user_id)
    {
        $user_id = (int)$user_id;
        $sql = "SELECT * FROM customer WHERE customer_id = $user_id";
        return $this->db_fetch_one($sql);
    }

    /**
     * Authenticate user login
     * @param string $email
     * @param string $password
     * @return array|false
     */
    public function authenticateUser($email, $password)
    {
        $user = $this->getUserByEmail($email);
        
        if ($user && password_verify($password, $user['customer_pass'])) {
            // Remove password from returned data for security
            unset($user['customer_pass']);
            return $user;
        }
        
        return false;
    }

    /**
     * Update user profile
     * @param int $user_id
     * @param string $name
     * @param string $email
     * @param string $phone_number
     * @param string $country
     * @param string $city
     * @return bool
     */
    public function updateUserProfile($user_id, $name, $email, $phone_number, $country, $city)
    {
        $user_id = (int)$user_id;
        $name = $this->escape_string($name);
        $email = $this->escape_string($email);
        $phone_number = $this->escape_string($phone_number);
        $country = $this->escape_string($country);
        $city = $this->escape_string($city);
        
        $sql = "UPDATE customer 
                SET customer_name = '$name', 
                    customer_email = '$email', 
                    customer_contact = '$phone_number',
                    customer_country = '$country',
                    customer_city = '$city'
                WHERE customer_id = $user_id";
        
        return $this->db_write_query($sql);
    }

    /**
     * Update user password
     * @param int $user_id
     * @param string $current_password
     * @param string $new_password
     * @return bool
     */
    public function updateUserPassword($user_id, $current_password, $new_password)
    {
        // First verify the current password
        $user = $this->getUserById($user_id);
        if (!$user || !password_verify($current_password, $user['customer_pass'])) {
            return false;
        }
        
        // Hash the new password
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $user_id = (int)$user_id;
        
        $sql = "UPDATE customer SET customer_pass = '$hashed_password' WHERE customer_id = $user_id";
        return $this->db_write_query($sql);
    }

    /**
     * Update user image
     * @param int $user_id
     * @param string $image_path
     * @return bool
     */
    public function updateUserImage($user_id, $image_path)
    {
        $user_id = (int)$user_id;
        $image_path = $this->escape_string($image_path);
        
        $sql = "UPDATE customer SET customer_image = '$image_path' WHERE customer_id = $user_id";
        return $this->db_write_query($sql);
    }

    /**
     * Delete user
     * @param int $user_id
     * @return bool
     */
    public function deleteUser($user_id)
    {
        $user_id = (int)$user_id;
        $sql = "DELETE FROM customer WHERE customer_id = $user_id";
        return $this->db_write_query($sql);
    }

    /**
     * Get users by role
     * @param int $role
     * @return array|false
     */
    public function getUsersByRole($role)
    {
        $role = (int)$role;
        $sql = "SELECT * FROM customer WHERE user_role = $role ORDER BY customer_name ASC";
        return $this->db_fetch_all($sql);
    }

    /**
     * Get users by country
     * @param string $country
     * @return array|false
     */
    public function getUsersByCountry($country)
    {
        $country = $this->escape_string($country);
        $sql = "SELECT * FROM customer WHERE customer_country = '$country' ORDER BY customer_name ASC";
        return $this->db_fetch_all($sql);
    }

    /**
     * Get users by city
     * @param string $city
     * @return array|false
     */
    public function getUsersByCity($city)
    {
        $city = $this->escape_string($city);
        $sql = "SELECT * FROM customer WHERE customer_city = '$city' ORDER BY customer_name ASC";
        return $this->db_fetch_all($sql);
    }

    /**
     * Search users by name, email, city, or country
     * @param string $search_term
     * @return array|false
     */
    public function searchUsers($search_term)
    {
        $search_term = $this->escape_string($search_term);
        $sql = "SELECT * FROM customer 
                WHERE customer_name LIKE '%$search_term%' 
                   OR customer_email LIKE '%$search_term%' 
                   OR customer_city LIKE '%$search_term%'
                   OR customer_country LIKE '%$search_term%'
                ORDER BY customer_name ASC";
        return $this->db_fetch_all($sql);
    }

    /**
     * Get total count of users
     * @return int|false
     */
    public function getTotalUsers()
    {
        $sql = "SELECT COUNT(*) as total FROM customer";
        $result = $this->db_fetch_one($sql);
        return $result ? (int)$result['total'] : false;
    }

    /**
     * Get users with pagination
     * @param int $offset
     * @param int $limit
     * @return array|false
     */
    public function getUsersPaginated($offset, $limit)
    {
        $offset = (int)$offset;
        $limit = (int)$limit;
        
        $sql = "SELECT * FROM customer ORDER BY customer_id DESC LIMIT $offset, $limit";
        return $this->db_fetch_all($sql);
    }

    /**
     * Get user statistics
     * @return array|false
     */
    public function getUserStats()
    {
        $sql = "SELECT 
                    COUNT(*) as total_users,
                    SUM(CASE WHEN user_role = 1 THEN 1 ELSE 0 END) as customers,
                    SUM(CASE WHEN user_role = 2 THEN 1 ELSE 0 END) as restaurant_owners,
                    COUNT(DISTINCT customer_country) as countries,
                    COUNT(DISTINCT customer_city) as cities
                FROM customer";
        return $this->db_fetch_one($sql);
    }

    // Getter methods
    public function getUserId() { return $this->user_id; }
    public function getName() { return $this->name; }
    public function getEmail() { return $this->email; }
    public function getRole() { return $this->role; }
    public function getCountry() { return $this->country; }
    public function getCity() { return $this->city; }
    public function getPhoneNumber() { return $this->phone_number; }
    public function getCustomerImage() { return $this->customer_image; }
    public function getDateCreated() { return $this->date_created; }

    // Setter methods
    public function setName($name) { $this->name = $name; }
    public function setEmail($email) { $this->email = $email; }
    public function setCountry($country) { $this->country = $country; }
    public function setCity($city) { $this->city = $city; }
    public function setPhoneNumber($phone_number) { $this->phone_number = $phone_number; }
}

?>