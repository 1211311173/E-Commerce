<?php

namespace Tests\Src;

use PHPUnit\Framework\TestCase;

/**
 * Base Test Class
 * Provides common functionality for all legacy tests
 */
class BaseTest extends TestCase
{
    protected $testDatabase;
    protected $testConfig;
    
    /**
     * Set up test environment
     */
    protected function setUp(): void
    {
        parent::setUp();
        
        // Initialize test configuration
        $this->testConfig = [
            'db_host' => 'localhost',
            'db_name' => 'test_ecommerce',
            'db_user' => 'root',
            'db_pass' => '',
            'test_mode' => true
        ];
        
        // Set up test database connection
        $this->setUpTestDatabase();
        
        // Initialize session for tests
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    /**
     * Tear down test environment
     */
    protected function tearDown(): void
    {
        // Clean up test data
        $this->cleanUpTestData();
        
        // Close database connection
        if ($this->testDatabase) {
            $this->testDatabase = null;
        }
        
        parent::tearDown();
    }
    
    /**
     * Set up test database
     */
    protected function setUpTestDatabase()
    {
        try {
            // Use SQLite in-memory database for testing
            $this->testDatabase = new \PDO('sqlite::memory:');
            $this->testDatabase->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            
            // Create basic test tables
            $this->createTestTables();
            
        } catch (\Exception $e) {
            $this->fail('Failed to set up test database: ' . $e->getMessage());
        }
    }
    
    /**
     * Create test tables
     */
    protected function createTestTables()
    {
        $tables = [
            'customer' => "
                CREATE TABLE customer (
                    customer_id INTEGER PRIMARY KEY AUTOINCREMENT,
                    customer_email VARCHAR(255) NOT NULL,
                    customer_pwd VARCHAR(255) NOT NULL,
                    customer_name VARCHAR(255) NOT NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                )
            ",
            'products' => "
                CREATE TABLE products (
                    product_id INTEGER PRIMARY KEY AUTOINCREMENT,
                    product_title VARCHAR(255) NOT NULL,
                    product_price DECIMAL(10,2) NOT NULL,
                    product_description TEXT,
                    product_stock INTEGER DEFAULT 0,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                )
            ",
            'cart' => "
                CREATE TABLE cart (
                    cart_id INTEGER PRIMARY KEY AUTOINCREMENT,
                    session_id VARCHAR(255) NOT NULL,
                    product_id INTEGER NOT NULL,
                    quantity INTEGER NOT NULL DEFAULT 1,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                )
            "
        ];
        
        foreach ($tables as $tableName => $sql) {
            $this->testDatabase->exec($sql);
        }
    }
    
    /**
     * Clean up test data
     */
    protected function cleanUpTestData()
    {
        if ($this->testDatabase) {
            try {
                $tables = ['customer', 'products', 'cart'];
                foreach ($tables as $table) {
                    $this->testDatabase->exec("DELETE FROM $table");
                }
            } catch (\Exception $e) {
                // Ignore cleanup errors
            }
        }
    }
    
    /**
     * Create test user
     */
    protected function createTestUser($email = 'test@example.com', $password = 'password123')
    {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->testDatabase->prepare(
            "INSERT INTO customer (customer_email, customer_pwd, customer_name) VALUES (?, ?, ?)"
        );
        $stmt->execute([$email, $hashedPassword, 'Test User']);
        return $this->testDatabase->lastInsertId();
    }
    
    /**
     * Create test product
     */
    protected function createTestProduct($title = 'Test Product', $price = 19.99, $stock = 10)
    {
        $stmt = $this->testDatabase->prepare(
            "INSERT INTO products (product_title, product_price, product_stock) VALUES (?, ?, ?)"
        );
        $stmt->execute([$title, $price, $stock]);
        return $this->testDatabase->lastInsertId();
    }
    
    /**
     * Assert database has record
     */
    protected function assertDatabaseHas($table, $conditions)
    {
        $whereClause = [];
        $values = [];
        
        foreach ($conditions as $column => $value) {
            $whereClause[] = "$column = ?";
            $values[] = $value;
        }
        
        $sql = "SELECT COUNT(*) FROM $table WHERE " . implode(' AND ', $whereClause);
        $stmt = $this->testDatabase->prepare($sql);
        $stmt->execute($values);
        
        $count = $stmt->fetchColumn();
        $this->assertGreaterThan(0, $count, "Failed asserting that table '$table' contains matching record.");
    }
    
    /**
     * Assert database missing record
     */
    protected function assertDatabaseMissing($table, $conditions)
    {
        $whereClause = [];
        $values = [];
        
        foreach ($conditions as $column => $value) {
            $whereClause[] = "$column = ?";
            $values[] = $value;
        }
        
        $sql = "SELECT COUNT(*) FROM $table WHERE " . implode(' AND ', $whereClause);
        $stmt = $this->testDatabase->prepare($sql);
        $stmt->execute($values);
        
        $count = $stmt->fetchColumn();
        $this->assertEquals(0, $count, "Failed asserting that table '$table' does not contain matching record.");
    }
}
