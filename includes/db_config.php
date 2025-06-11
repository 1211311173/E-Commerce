<?php
/**
 * Centralized Database Configuration
 * This file contains the database connection settings and provides a singleton connection instance
 */

class Database {
    private static $instance = null;
    private $conn;
    
    // Database configuration
    private $serverName = "localhost";
    private $dBUsername = "root";
    private $dBPassword = "";
    private $dBName = "db_ecommerce";
    private $dbPort = 3307;
    
    private function __construct() {
        // Create connection
        $this->conn = new mysqli(
            $this->serverName,
            $this->dBUsername,
            $this->dBPassword,
            $this->dBName,
            $this->dbPort
        );
        
        // Check connection
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
        
        // Set charset to prevent character set confusion attacks
        $this->conn->set_charset("utf8");
    }
    
    // Prevent cloning of the instance
    private function __clone() {}
    
    // Get the database instance
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    // Get the connection
    public function getConnection() {
        return $this->conn;
    }
    
    // Close the connection
    public function closeConnection() {
        if ($this->conn) {
            $this->conn->close();
        }
    }
}

// Create global connection instance
$db = Database::getInstance();
$conn = $db->getConnection();

// Include security functions
require_once __DIR__ . '/security.php'; 