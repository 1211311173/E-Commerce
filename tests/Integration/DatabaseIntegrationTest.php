<?php

namespace Tests\Integration;

use Tests\Helpers\TestHelper;
use PDO;
use Exception;

/**
 * Database Integration Tests
 * Tests database connections and operations
 */
class DatabaseIntegrationTest extends TestHelper
{
    protected static $db;
    
    public static function setUpBeforeClass(): void
    {
        self::setUpTestDatabase();
        self::$db = self::getTestDatabase();
    }
    
    public static function tearDownAfterClass(): void
    {
        self::tearDownTestDatabase();
    }
    
    public function testDatabaseConnection()
    {
        $this->assertInstanceOf(PDO::class, self::$db);
        $this->assertNotNull(self::$db);
    }
    
    public function testUserCRUD()
    {
        // Test Create
        $userData = $this->createTestUser();
        $stmt = self::$db->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $result = $stmt->execute([
            $userData['username'],
            $userData['email'],
            password_hash($userData['password'], PASSWORD_DEFAULT)
        ]);
        
        $this->assertTrue($result);
        $userId = self::$db->lastInsertId();
        $this->assertGreaterThan(0, $userId);
        
        // Test Read
        $stmt = self::$db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $this->assertNotFalse($user);
        $this->assertEquals($userData['username'], $user['username']);
        $this->assertEquals($userData['email'], $user['email']);
        
        // Test Update
        $newEmail = 'updated_' . $userData['email'];
        $stmt = self::$db->prepare("UPDATE users SET email = ? WHERE id = ?");
        $result = $stmt->execute([$newEmail, $userId]);
        
        $this->assertTrue($result);
        
        // Verify update
        $stmt = self::$db->prepare("SELECT email FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $updatedUser = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->assertEquals($newEmail, $updatedUser['email']);
        
        // Test Delete
        $stmt = self::$db->prepare("DELETE FROM users WHERE id = ?");
        $result = $stmt->execute([$userId]);
        
        $this->assertTrue($result);
        
        // Verify deletion
        $stmt = self::$db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $deletedUser = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->assertFalse($deletedUser);
    }
    
    public function testProductCRUD()
    {
        // Test Create Product
        $productData = $this->createTestProduct();
        $stmt = self::$db->prepare("INSERT INTO products (name, description, price, category_id, stock_quantity) VALUES (?, ?, ?, ?, ?)");
        $result = $stmt->execute([
            $productData['name'],
            $productData['description'],
            $productData['price'],
            $productData['category_id'],
            $productData['stock_quantity']
        ]);
        
        $this->assertTrue($result);
        $productId = self::$db->lastInsertId();
        
        // Test Read Product
        $stmt = self::$db->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$productId]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $this->assertNotFalse($product);
        $this->assertEquals($productData['name'], $product['name']);
        $this->assertEquals($productData['price'], $product['price']);
        
        // Test Product with Category Join
        $stmt = self::$db->prepare("
            SELECT p.*, c.name as category_name 
            FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id 
            WHERE p.id = ?
        ");
        $stmt->execute([$productId]);
        $productWithCategory = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $this->assertNotFalse($productWithCategory);
        $this->assertArrayHasKey('category_name', $productWithCategory);
    }
    
    public function testTransactions()
    {
        self::$db->beginTransaction();
        
        try {
            // Insert test data within transaction
            $userData = $this->createTestUser();
            $stmt = self::$db->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            $stmt->execute([
                $userData['username'],
                $userData['email'],
                password_hash($userData['password'], PASSWORD_DEFAULT)
            ]);
            
            $userId = self::$db->lastInsertId();
            
            // Insert order for the user
            $stmt = self::$db->prepare("INSERT INTO orders (user_id, total_amount, status) VALUES (?, ?, ?)");
            $stmt->execute([$userId, 99.99, 'pending']);
            
            self::$db->commit();
            
            // Verify both records exist
            $stmt = self::$db->prepare("SELECT COUNT(*) FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $userCount = $stmt->fetchColumn();
            $this->assertEquals(1, $userCount);
            
            $stmt = self::$db->prepare("SELECT COUNT(*) FROM orders WHERE user_id = ?");
            $stmt->execute([$userId]);
            $orderCount = $stmt->fetchColumn();
            $this->assertEquals(1, $orderCount);
            
        } catch (Exception $e) {
            self::$db->rollback();
            $this->fail("Transaction failed: " . $e->getMessage());
        }
    }
}
