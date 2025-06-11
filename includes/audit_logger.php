<?php
/**
 * Simple Security Audit Logger - File Based
 *
 * This file contains a simple function to log security events to a file
 */

/**
 * Log security events to a file
 *
 * @param string $event_type Type of event (LOGIN, ADMIN_LOGIN, PRODUCT_ADD, etc.)
 * @param string $event_status SUCCESS or FAILURE
 * @param string $event_description Description of the event
 * @param int|null $user_id User ID if applicable
 * @return bool Success status
 */
function logSecurityEvent($event_type, $event_status, $event_description, $user_id = null) {
    try {
        // Get client IP address
        $ip_address = getClientIP();

        // Create log directory if it doesn't exist
        $log_dir = __DIR__ . '/../logs';
        if (!is_dir($log_dir)) {
            mkdir($log_dir, 0755, true);
        }

        // Create log file path
        $log_file = $log_dir . '/security_audit.log';

        // Create log entry
        $timestamp = date('Y-m-d H:i:s');
        $user_info = $user_id ? "User ID: $user_id" : "No User";
        $log_entry = "[$timestamp] [$event_status] [$event_type] $event_description | $user_info | IP: $ip_address" . PHP_EOL;

        // Write to log file
        return file_put_contents($log_file, $log_entry, FILE_APPEND | LOCK_EX) !== false;

    } catch (Exception $e) {
        // Log to error log if file logging fails
        error_log("Audit Logger Error: " . $e->getMessage());
        return false;
    }
}

/**
 * Get client IP address
 */
function getClientIP() {
    $ipKeys = ['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR'];
    foreach ($ipKeys as $key) {
        if (array_key_exists($key, $_SERVER) === true) {
            foreach (explode(',', $_SERVER[$key]) as $ip) {
                $ip = trim($ip);
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                    return $ip;
                }
            }
        }
    }
    return $_SERVER['REMOTE_ADDR'] ?? 'unknown';
}

/**
 * Helper functions for common events
 */

// Log successful login
function logLoginSuccess($user_id, $user_email) {
    return logSecurityEvent('LOGIN', 'SUCCESS', "User logged in: {$user_email}", $user_id);
}

// Log failed login
function logLoginFailure($email, $reason = 'Invalid credentials') {
    return logSecurityEvent('LOGIN', 'FAILURE', "Failed login attempt for: {$email} - {$reason}");
}

// Log successful admin login
function logAdminLoginSuccess($user_id, $user_email) {
    return logSecurityEvent('ADMIN_LOGIN', 'SUCCESS', "Admin logged in: {$user_email}", $user_id);
}

// Log failed admin login
function logAdminLoginFailure($email, $reason = 'Invalid credentials') {
    return logSecurityEvent('ADMIN_LOGIN', 'FAILURE', "Failed admin login attempt for: {$email} - {$reason}");
}

// Log user registration
function logUserRegistration($user_id, $user_email) {
    return logSecurityEvent('USER_REGISTRATION', 'SUCCESS', "New user registered: {$user_email}", $user_id);
}

// Log product addition
function logProductAdd($admin_id, $product_title) {
    return logSecurityEvent('PRODUCT_ADD', 'SUCCESS', "Product added: {$product_title}", $admin_id);
}

// Log product update
function logProductUpdate($admin_id, $product_id, $product_title) {
    return logSecurityEvent('PRODUCT_UPDATE', 'SUCCESS', "Product updated: {$product_title} (ID: {$product_id})", $admin_id);
}

// Log product deletion
function logProductDelete($admin_id, $product_id, $product_title) {
    return logSecurityEvent('PRODUCT_DELETE', 'SUCCESS', "Product deleted: {$product_title} (ID: {$product_id})", $admin_id);
}

// Log user update
function logUserUpdate($admin_id, $target_user_id, $target_user_email) {
    return logSecurityEvent('USER_UPDATE', 'SUCCESS', "User updated: {$target_user_email} (ID: {$target_user_id})", $admin_id);
}

// Log user deletion
function logUserDelete($admin_id, $target_user_id, $target_user_email) {
    return logSecurityEvent('USER_DELETE', 'SUCCESS', "User deleted: {$target_user_email} (ID: {$target_user_id})", $admin_id);
}

// Log unauthorized access
function logUnauthorizedAccess($attempted_resource, $user_id = null) {
    return logSecurityEvent('UNAUTHORIZED_ACCESS', 'FAILURE', "Unauthorized access attempt to: {$attempted_resource}", $user_id);
}

?>
