<?php
/**
 * Security Functions for SQL Injection Prevention
 * This file contains secure database operations and input validation functions
 */

class SecureDB {
    private $conn;
    
    public function __construct($connection) {
        $this->conn = $connection;
    }
    
    /**
     * Secure SELECT query with prepared statements
     * @param string $query SQL query with placeholders (?)
     * @param array $params Parameters to bind
     * @param string $types Parameter types (s=string, i=integer, d=double, b=blob)
     * @return mysqli_result|false
     */
    public function select($query, $params = [], $types = '') {
        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            error_log("Prepare failed: " . $this->conn->error);
            return false;
        }
        
        if (!empty($params)) {
            if (empty($types)) {
                // Auto-detect types if not provided
                $types = str_repeat('s', count($params));
            }
            $stmt->bind_param($types, ...$params);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        
        return $result;
    }
    
    /**
     * Secure INSERT query with prepared statements
     * @param string $query SQL query with placeholders (?)
     * @param array $params Parameters to bind
     * @param string $types Parameter types
     * @return bool
     */
    public function insert($query, $params = [], $types = '') {
        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            error_log("Prepare failed: " . $this->conn->error);
            return false;
        }
        
        if (!empty($params)) {
            if (empty($types)) {
                $types = str_repeat('s', count($params));
            }
            $stmt->bind_param($types, ...$params);
        }
        
        $result = $stmt->execute();
        $stmt->close();
        
        return $result;
    }
    
    /**
     * Secure UPDATE query with prepared statements
     * @param string $query SQL query with placeholders (?)
     * @param array $params Parameters to bind
     * @param string $types Parameter types
     * @return bool
     */
    public function update($query, $params = [], $types = '') {
        return $this->insert($query, $params, $types); // Same logic as insert
    }
    
    /**
     * Secure DELETE query with prepared statements
     * @param string $query SQL query with placeholders (?)
     * @param array $params Parameters to bind
     * @param string $types Parameter types
     * @return bool
     */
    public function delete($query, $params = [], $types = '') {
        return $this->insert($query, $params, $types); // Same logic as insert
    }
    
    /**
     * Get the last inserted ID
     * @return int
     */
    public function getLastInsertId() {
        return $this->conn->insert_id;
    }
    
    /**
     * Get affected rows count
     * @return int
     */
    public function getAffectedRows() {
        return $this->conn->affected_rows;
    }
}

/**
 * Input validation and sanitization functions
 */
class InputValidator {
    
    /**
     * Validate and sanitize email
     * @param string $email
     * @return string|false
     */
    public static function validateEmail($email) {
        $email = filter_var(trim($email), FILTER_VALIDATE_EMAIL);
        return $email !== false ? $email : false;
    }
    
    /**
     * Validate and sanitize integer
     * @param mixed $value
     * @param int $min
     * @param int $max
     * @return int|false
     */
    public static function validateInt($value, $min = null, $max = null) {
        $options = [];
        if ($min !== null || $max !== null) {
            $options['options'] = [];
            if ($min !== null) $options['options']['min_range'] = $min;
            if ($max !== null) $options['options']['max_range'] = $max;
        }
        
        return filter_var($value, FILTER_VALIDATE_INT, $options);
    }
    
    /**
     * Validate and sanitize float
     * @param mixed $value
     * @param float $min
     * @param float $max
     * @return float|false
     */
    public static function validateFloat($value, $min = null, $max = null) {
        $options = [];
        if ($min !== null || $max !== null) {
            $options['options'] = [];
            if ($min !== null) $options['options']['min_range'] = $min;
            if ($max !== null) $options['options']['max_range'] = $max;
        }
        
        return filter_var($value, FILTER_VALIDATE_FLOAT, $options);
    }
    
    /**
     * Sanitize string input
     * @param string $input
     * @param int $maxLength
     * @return string
     */
    public static function sanitizeString($input, $maxLength = null) {
        $sanitized = htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
        if ($maxLength && strlen($sanitized) > $maxLength) {
            $sanitized = substr($sanitized, 0, $maxLength);
        }
        return $sanitized;
    }
    
    /**
     * Validate phone number
     * @param string $phone
     * @return string|false
     */
    public static function validatePhone($phone) {
        $phone = preg_replace('/[^0-9+\-\s\(\)]/', '', $phone);
        if (strlen($phone) >= 10 && strlen($phone) <= 15) {
            return $phone;
        }
        return false;
    }
    
    /**
     * Validate and sanitize search terms
     * @param string $searchTerm
     * @return string|false
     */
    public static function validateSearchTerm($searchTerm) {
        $searchTerm = trim($searchTerm);
        if (strlen($searchTerm) < 2 || strlen($searchTerm) > 100) {
            return false;
        }
        
        // Remove potentially dangerous characters but keep alphanumeric and basic punctuation
        $searchTerm = preg_replace('/[^a-zA-Z0-9\s\-_\.]/', '', $searchTerm);
        return $searchTerm;
    }
}

// Initialize secure database instance
global $secureDB;
$secureDB = new SecureDB($conn);

/**
 * Legacy function wrapper for backward compatibility
 * Use this to gradually migrate existing code
 */
function secure_query($query, $params = [], $types = '') {
    global $secureDB;
    return $secureDB->select($query, $params, $types);
}

/**
 * CSRF Protection functions
 */
class CSRFProtection {
    
    /**
     * Generate CSRF token
     * @return string
     */
    public static function generateToken() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        
        return $_SESSION['csrf_token'];
    }
    
    /**
     * Validate CSRF token
     * @param string $token
     * @return bool
     */
    public static function validateToken($token) {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
    
    /**
     * Generate CSRF hidden input field
     * @return string
     */
    public static function getTokenField() {
        $token = self::generateToken();
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token) . '">';
    }
}

?>
