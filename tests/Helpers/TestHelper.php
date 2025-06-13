<?php

namespace Tests\Helpers;

use PHPUnit\Framework\TestCase;
use PDO;

/**
 * Test Helper Class
 * Provides common utilities for all test classes
 */
class TestHelper extends TestCase
{
    protected static $testDatabase;
    
    /**
     * Set up test database
     */
    public static function setUpTestDatabase()
    {
        self::$testDatabase = new PDO('sqlite::memory:');
        self::$testDatabase->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Create test tables
        self::createTestTables();
        self::seedTestData();
    }
    
    /**
     * Create test tables
     */
    private static function createTestTables()
    {
        $tables = [
            'users' => "
                CREATE TABLE users (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    username VARCHAR(50) UNIQUE,
                    email VARCHAR(100) UNIQUE,
                    password VARCHAR(255),
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
                )
            ",
            'products' => "
                CREATE TABLE products (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    name VARCHAR(255),
                    description TEXT,
                    price DECIMAL(10,2),
                    category_id INTEGER,
                    stock_quantity INTEGER DEFAULT 0,
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
                )
            ",
            'categories' => "
                CREATE TABLE categories (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    name VARCHAR(100),
                    description TEXT,
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
                )
            ",
            'orders' => "
                CREATE TABLE orders (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    user_id INTEGER,
                    total_amount DECIMAL(10,2),
                    status VARCHAR(50) DEFAULT 'pending',
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
                )
            "
        ];
        
        foreach ($tables as $table => $sql) {
            self::$testDatabase->exec($sql);
        }
    }
    
    /**
     * Seed test data
     */
    private static function seedTestData()
    {
        // Insert test categories
        $stmt = self::$testDatabase->prepare("INSERT INTO categories (name, description) VALUES (?, ?)");
        $stmt->execute(['Electronics', 'Electronic devices and gadgets']);
        $stmt->execute(['Clothing', 'Fashion and apparel']);
        
        // Insert test products
        $stmt = self::$testDatabase->prepare("INSERT INTO products (name, description, price, category_id, stock_quantity) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute(['Smartphone', 'Latest smartphone model', 699.99, 1, 50]);
        $stmt->execute(['T-Shirt', 'Cotton t-shirt', 29.99, 2, 100]);
        
        // Insert test user
        $stmt = self::$testDatabase->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->execute(['testuser', 'test@example.com', password_hash('password123', PASSWORD_DEFAULT)]);
    }
    
    /**
     * Clean up test data
     */
    public static function tearDownTestDatabase()
    {
        if (self::$testDatabase) {
            self::$testDatabase = null;
        }
    }
    
    /**
     * Get test database instance
     */
    public static function getTestDatabase()
    {
        return self::$testDatabase;
    }
    
    /**
     * Create test user data
     */
    public function createTestUser($overrides = [])
    {
        $defaults = [
            'username' => 'testuser_' . uniqid(),
            'email' => 'test_' . uniqid() . '@example.com',
            'password' => 'password123'
        ];
        
        return array_merge($defaults, $overrides);
    }
    
    /**
     * Create test product data
     */
    public function createTestProduct($overrides = [])
    {
        $defaults = [
            'name' => 'Test Product ' . uniqid(),
            'description' => 'Test product description',
            'price' => 99.99,
            'category_id' => 1,
            'stock_quantity' => 10
        ];
        
        return array_merge($defaults, $overrides);
    }
    
    /**
     * Mock HTTP request
     */
    public function mockHttpRequest($method = 'GET', $uri = '/', $data = [])
    {
        $_SERVER['REQUEST_METHOD'] = $method;
        $_SERVER['REQUEST_URI'] = $uri;
        
        if ($method === 'POST') {
            $_POST = $data;
        } else {
            $_GET = $data;
        }
    }
    
    /**
     * Assert array structure
     */
    public function assertArrayStructure(array $structure, array $array)
    {
        foreach ($structure as $key => $value) {
            if (is_array($value)) {
                $this->assertArrayHasKey($key, $array);
                $this->assertArrayStructure($value, $array[$key]);
            } else {
                $this->assertArrayHasKey($value, $array);
            }
        }
    }
}
