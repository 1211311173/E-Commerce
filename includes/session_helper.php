<?php
/**
 * Session Helper Functions
 * Provides safe and consistent session handling across the application
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Safely get a session value with optional default
 * @param string $key The session key to retrieve
 * @param mixed $default Default value if session key doesn't exist
 * @return mixed The session value or default
 */
function getSessionValue($key, $default = null) {
    return isset($_SESSION[$key]) ? $_SESSION[$key] : $default;
}

/**
 * Safely set a session value
 * @param string $key The session key to set
 * @param mixed $value The value to set
 * @return void
 */
function setSessionValue($key, $value) {
    $_SESSION[$key] = $value;
}

/**
 * Initialize website settings in session
 * @param mysqli $conn Database connection
 * @return void
 */
function initializeWebsiteSettings($conn) {
    $sql = "SELECT * FROM settings";
    $result = $conn->query($sql);
    
    if ($result && $row = $result->fetch_assoc()) {
        setSessionValue('web-name', $row['website_name'] ?? 'E-Commerce Store');
        setSessionValue('web-img', $row['website_logo'] ?? 'default-logo.png');
        setSessionValue('web-footer', $row['website_footer'] ?? '© 2024 E-Commerce Store. All rights reserved.');
    }
}

/**
 * Check if user is logged in
 * @return bool
 */
function isLoggedIn() {
    return isset($_SESSION['id']);
}

/**
 * Check if user is admin
 * @return bool
 */
function isAdmin() {
    return isset($_SESSION['customer_role']) && $_SESSION['customer_role'] === 'admin';
}

/**
 * Get current user ID
 * @return int|null
 */
function getCurrentUserId() {
    return getSessionValue('id');
}

/**
 * Get current user role
 * @return string|null
 */
function getCurrentUserRole() {
    return getSessionValue('customer_role');
}

/**
 * Get cart item count
 * @return int
 */
function getCartItemCount() {
    return isset($_SESSION['mycart']) ? count($_SESSION['mycart']) : 0;
}

/**
 * Clear all session data
 * @return void
 */
function clearSession() {
    session_unset();
    session_destroy();
} 