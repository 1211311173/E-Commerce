<?php
/**
 * PHPUnit Bootstrap File
 * Sets up the testing environment for the E-Commerce application
 */

// Include the autoloader
require_once __DIR__ . '/../vendor/autoload.php';

// Set up autoloader for Tests namespace
spl_autoload_register(function ($class) {
    if (strpos($class, 'Tests\\') === 0) {
        $file = __DIR__ . '/' . str_replace('\\', '/', substr($class, 6)) . '.php';
        if (file_exists($file)) {
            require_once $file;
        }
    }
});

// Include application functions
require_once __DIR__ . '/../functions/functions.php';

// Set up test environment variables
$_ENV['TEST_MODE'] = true;
$_ENV['DB_HOST'] = 'localhost';
$_ENV['DB_NAME'] = 'test_ecommerce';
$_ENV['DB_USER'] = 'root';
$_ENV['DB_PASS'] = '';

// Start session for tests that require it
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set error reporting for tests
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set timezone
date_default_timezone_set('UTC');

// Mock global functions if needed
if (!function_exists('mock_database_connection')) {
    function mock_database_connection() {
        // Mock database connection for testing
        return new PDO('sqlite::memory:');
    }
}
