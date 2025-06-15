<?php

namespace Tests\Maintenance;

use Tests\Helpers\TestHelper;

/**
 * Performance Regression Tests
 * Ensures that code changes don't negatively impact performance
 */
class PerformanceRegressionTest extends TestHelper
{
    protected static $db;
    private $performanceThresholds = [
        'database_query' => 0.1,      // 100ms
        'page_load' => 2.0,           // 2 seconds
        'search_operation' => 0.5,    // 500ms
        'user_login' => 0.3,          // 300ms
        'cart_operations' => 0.2      // 200ms
    ];
    
    public static function setUpBeforeClass(): void
    {
        self::setUpTestDatabase();
        self::$db = self::getTestDatabase();
        
        // Create performance test data
        self::createPerformanceTestData();
    }
    
    public static function tearDownAfterClass(): void
    {
        self::tearDownTestDatabase();
    }
    
    public function testDatabaseQueryPerformance()
    {
        $startTime = microtime(true);
        
        // Test basic user query
        $stmt = self::$db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([1]);
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;
        
        $this->assertLessThan($this->performanceThresholds['database_query'], $executionTime,
            "Database query took {$executionTime}s, expected less than {$this->performanceThresholds['database_query']}s");
        
        $this->assertNotFalse($user);
    }
    
    public function testComplexQueryPerformance()
    {
        $startTime = microtime(true);
        
        // Test complex join query
        $stmt = self::$db->prepare("
            SELECT p.*, c.name as category_name, COUNT(o.id) as order_count
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            LEFT JOIN orders o ON p.id = o.id
            WHERE p.price > ?
            GROUP BY p.id
            ORDER BY p.price DESC
            LIMIT 10
        ");
        $stmt->execute([50.00]);
        $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;
        
        $this->assertLessThan($this->performanceThresholds['database_query'] * 2, $executionTime,
            "Complex query took {$executionTime}s, expected less than " . ($this->performanceThresholds['database_query'] * 2) . "s");
    }
    
    public function testBulkInsertPerformance()
    {
        $startTime = microtime(true);
        
        // Test bulk insert performance
        self::$db->beginTransaction();
        
        $stmt = self::$db->prepare("INSERT INTO products (name, description, price, category_id, stock_quantity) VALUES (?, ?, ?, ?, ?)");
        
        for ($i = 0; $i < 100; $i++) {
            $stmt->execute([
                "Bulk Product $i",
                "Description for bulk product $i",
                rand(10, 1000) / 10,
                rand(1, 2),
                rand(1, 100)
            ]);
        }
        
        self::$db->commit();
        
        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;
        
        $this->assertLessThan(1.0, $executionTime,
            "Bulk insert of 100 records took {$executionTime}s, expected less than 1s");
    }
    
    public function testSearchPerformance()
    {
        $startTime = microtime(true);
        
        // Test product search performance
        $searchTerm = 'test';
        $stmt = self::$db->prepare("
            SELECT * FROM products 
            WHERE name LIKE ? OR description LIKE ?
            ORDER BY name
            LIMIT 20
        ");
        $stmt->execute(["%$searchTerm%", "%$searchTerm%"]);
        $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;
        
        $this->assertLessThan($this->performanceThresholds['search_operation'], $executionTime,
            "Search operation took {$executionTime}s, expected less than {$this->performanceThresholds['search_operation']}s");
    }
    
    public function testUserLoginPerformance()
    {
        $startTime = microtime(true);
        
        // Simulate login process
        $username = 'testuser';
        $password = 'password123';
        
        // Get user from database
        $stmt = self::$db->prepare("SELECT id, password FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        if ($user) {
            // Verify password (this is the expensive operation)
            password_verify($password, $user['password']);
        }
        
        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;
        
        $this->assertLessThan($this->performanceThresholds['user_login'], $executionTime,
            "User login took {$executionTime}s, expected less than {$this->performanceThresholds['user_login']}s");
    }
    
    public function testCartOperationPerformance()
    {
        $startTime = microtime(true);
        
        // Create cart table
        self::$db->exec("
            CREATE TABLE IF NOT EXISTS cart (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                session_id VARCHAR(255),
                product_id INTEGER,
                quantity INTEGER,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ");
        
        $sessionId = 'perf_test_session';
        
        // Add multiple items to cart
        $stmt = self::$db->prepare("INSERT INTO cart (session_id, product_id, quantity) VALUES (?, ?, ?)");
        for ($i = 1; $i <= 10; $i++) {
            $stmt->execute([$sessionId, $i, rand(1, 5)]);
        }
        
        // Calculate cart total
        $stmt = self::$db->prepare("
            SELECT SUM(c.quantity * p.price) as total 
            FROM cart c 
            JOIN products p ON c.product_id = p.id 
            WHERE c.session_id = ?
        ");
        $stmt->execute([$sessionId]);
        $total = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;
        
        $this->assertLessThan($this->performanceThresholds['cart_operations'], $executionTime,
            "Cart operations took {$executionTime}s, expected less than {$this->performanceThresholds['cart_operations']}s");
        
        $this->assertNotNull($total['total']);
    }
    
    public function testMemoryUsage()
    {
        $startMemory = memory_get_usage();
        
        // Perform memory-intensive operation
        $largeArray = [];
        for ($i = 0; $i < 10000; $i++) {
            $largeArray[] = [
                'id' => $i,
                'name' => "Item $i",
                'data' => str_repeat('x', 100)
            ];
        }
        
        $peakMemory = memory_get_peak_usage();
        $memoryUsed = $peakMemory - $startMemory;
        
        // Should not use more than 50MB for this operation
        $maxMemoryMB = 50 * 1024 * 1024;
        
        $this->assertLessThan($maxMemoryMB, $memoryUsed,
            "Memory usage was " . ($memoryUsed / 1024 / 1024) . "MB, expected less than 50MB");
        
        // Clean up
        unset($largeArray);
    }
    
    public function testConcurrentOperations()
    {
        $startTime = microtime(true);
        
        // Simulate multiple database operations
        $operations = [];
        
        for ($i = 0; $i < 10; $i++) {
            $stmt = self::$db->prepare("SELECT COUNT(*) FROM products WHERE price > ?");
            $stmt->execute([rand(1, 100)]);
            $operations[] = $stmt->fetchColumn();
        }
        
        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;
        
        $this->assertLessThan(1.0, $executionTime,
            "10 concurrent operations took {$executionTime}s, expected less than 1s");
        
        $this->assertCount(10, $operations);
    }
    
    private static function createPerformanceTestData()
    {
        // Create a substantial amount of test data for performance testing
        
        // Insert test users
        $stmt = self::$db->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        for ($i = 1; $i <= 100; $i++) {
            $stmt->execute([
                "perfuser$i",
                "perfuser$i@example.com",
                password_hash('password123', PASSWORD_DEFAULT)
            ]);
        }
        
        // Insert test products
        $stmt = self::$db->prepare("INSERT INTO products (name, description, price, category_id, stock_quantity) VALUES (?, ?, ?, ?, ?)");
        for ($i = 1; $i <= 1000; $i++) {
            $stmt->execute([
                "Performance Test Product $i",
                "Description for performance test product $i",
                rand(100, 10000) / 100,
                rand(1, 2),
                rand(1, 100)
            ]);
        }
        
        // Insert test orders
        $stmt = self::$db->prepare("INSERT INTO orders (user_id, total_amount, status) VALUES (?, ?, ?)");
        for ($i = 1; $i <= 500; $i++) {
            $stmt->execute([
                rand(1, 100),
                rand(1000, 50000) / 100,
                ['pending', 'completed', 'cancelled'][rand(0, 2)]
            ]);
        }
    }
}
