<?php

namespace Tests\Evolution;

use Tests\Helpers\TestHelper;

/**
 * Feature Evolution Tests
 * Tests that ensure new features integrate properly with existing functionality
 */
class FeatureEvolutionTest extends TestHelper
{
    protected static $db;
    
    public static function setUpBeforeClass(): void
    {
        self::setUpTestDatabase();
        self::$db = self::getTestDatabase();
        self::createEvolutionTestData();
    }
    
    public static function tearDownAfterClass(): void
    {
        self::tearDownTestDatabase();
    }
    
    public function testNewFeatureIntegration()
    {
        // Test that new features don't break existing functionality
        
        // Simulate adding a new "wishlist" feature
        $this->createWishlistTable();
        
        // Test existing user functionality still works
        $userData = $this->createTestUser();
        $userId = $this->createUserInDatabase($userData);
        
        $this->assertGreaterThan(0, $userId);
        
        // Test new wishlist feature integration
        $productData = $this->createTestProduct();
        $productId = $this->createProductInDatabase($productData);
        
        // Add product to wishlist
        $this->addProductToWishlist($userId, $productId);
        
        // Verify wishlist functionality
        $wishlistItems = $this->getWishlistItems($userId);
        $this->assertCount(1, $wishlistItems);
        $this->assertEquals($productId, $wishlistItems[0]['product_id']);
        
        // Verify existing cart functionality still works
        $this->createCartTable();
        $sessionId = 'test_session_' . uniqid();
        $this->addToCart(['session_id' => $sessionId, 'product_id' => $productId, 'quantity' => 2]);
        
        $cartItems = $this->getCartItems($sessionId);
        $this->assertCount(1, $cartItems);
    }
    
    public function testDatabaseSchemaEvolution()
    {
        // Test database schema changes don't break existing functionality
        
        // Add new columns to existing tables
        self::$db->exec("ALTER TABLE users ADD COLUMN phone VARCHAR(20)");
        self::$db->exec("ALTER TABLE users ADD COLUMN address TEXT");
        self::$db->exec("ALTER TABLE products ADD COLUMN weight DECIMAL(8,2)");
        self::$db->exec("ALTER TABLE products ADD COLUMN dimensions VARCHAR(100)");
        
        // Test existing functionality still works
        $userData = $this->createTestUser();
        $userId = $this->createUserInDatabase($userData);
        
        $user = $this->getUserFromDatabase($userId);
        $this->assertNotFalse($user);
        $this->assertArrayHasKey('phone', $user);
        $this->assertArrayHasKey('address', $user);
        
        // Test new columns can be updated
        $this->updateUserProfile($userId, [
            'phone' => '+1234567890',
            'address' => '123 Test Street, Test City'
        ]);
        
        $updatedUser = $this->getUserFromDatabase($userId);
        $this->assertEquals('+1234567890', $updatedUser['phone']);
        $this->assertEquals('123 Test Street, Test City', $updatedUser['address']);
    }
    
    public function testAPIEvolution()
    {
        // Test API versioning and evolution
        
        // Simulate API v1 response structure
        $v1Response = $this->getProductAPIv1(1);
        $expectedV1Structure = ['id', 'name', 'price', 'description'];
        $this->assertArrayStructure($expectedV1Structure, $v1Response);
        
        // Simulate API v2 response structure (with additional fields)
        $v2Response = $this->getProductAPIv2(1);
        $expectedV2Structure = ['id', 'name', 'price', 'description', 'category', 'images', 'reviews'];
        $this->assertArrayStructure($expectedV2Structure, $v2Response);
        
        // Test backward compatibility - v1 fields should still exist in v2
        foreach ($expectedV1Structure as $field) {
            $this->assertArrayHasKey($field, $v2Response);
            $this->assertEquals($v1Response[$field], $v2Response[$field]);
        }
    }
    
    public function testFeatureFlagCompatibility()
    {
        // Test feature flags for gradual feature rollout
        $featureFlags = [
            'new_checkout_process' => false,
            'advanced_search' => true,
            'social_login' => false,
            'recommendation_engine' => true
        ];
        
        // Test that features behave correctly based on flags
        foreach ($featureFlags as $feature => $enabled) {
            $result = $this->checkFeatureFlag($feature);
            $this->assertEquals($enabled, $result, "Feature flag '$feature' should be " . ($enabled ? 'enabled' : 'disabled'));
        }
        
        // Test feature functionality when enabled
        if ($featureFlags['advanced_search']) {
            $searchResults = $this->performAdvancedSearch(['category' => 'Electronics', 'price_range' => '100-500']);
            $this->assertIsArray($searchResults);
        }
        
        if ($featureFlags['recommendation_engine']) {
            $recommendations = $this->getProductRecommendations(1);
            $this->assertIsArray($recommendations);
        }
    }
    
    public function testMigrationCompatibility()
    {
        // Test data migration scenarios
        
        // Create legacy data structure
        self::$db->exec("
            CREATE TABLE legacy_orders (
                id INTEGER PRIMARY KEY,
                customer_name VARCHAR(255),
                customer_email VARCHAR(255),
                order_total DECIMAL(10,2),
                order_date DATE
            )
        ");
        
        // Insert legacy data
        $stmt = self::$db->prepare("INSERT INTO legacy_orders (customer_name, customer_email, order_total, order_date) VALUES (?, ?, ?, ?)");
        $stmt->execute(['John Doe', 'john@example.com', 99.99, '2023-01-01']);
        $stmt->execute(['Jane Smith', 'jane@example.com', 149.50, '2023-01-02']);
        
        // Migrate to new structure
        $this->migrateLegacyOrders();
        
        // Verify migration
        $stmt = self::$db->query("SELECT COUNT(*) FROM orders WHERE total_amount > 0");
        $migratedCount = $stmt->fetchColumn();
        $this->assertGreaterThan(0, $migratedCount);
        
        // Verify data integrity
        $stmt = self::$db->prepare("SELECT * FROM orders WHERE total_amount = ?");
        $stmt->execute([99.99]);
        $order = $stmt->fetch(\PDO::FETCH_ASSOC);
        $this->assertNotFalse($order);
    }
    
    public function testPerformanceWithNewFeatures()
    {
        // Test that new features don't significantly impact performance
        
        $startTime = microtime(true);
        
        // Simulate complex operation with new features
        $this->performComplexOperationWithNewFeatures();
        
        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;
        
        // Should complete within reasonable time even with new features
        $this->assertLessThan(2.0, $executionTime, "Complex operation with new features took too long");
    }
    
    public function testCrossFeatureCompatibility()
    {
        // Test that multiple new features work together
        
        $userId = $this->createUserInDatabase($this->createTestUser());
        $productId = $this->createProductInDatabase($this->createTestProduct());
        
        // Test wishlist + cart interaction
        $this->addProductToWishlist($userId, $productId);
        $this->createCartTable();
        $sessionId = 'cross_feature_test';
        $this->addToCart(['session_id' => $sessionId, 'product_id' => $productId, 'quantity' => 1]);
        
        // Move from wishlist to cart
        $this->moveWishlistToCart($userId, $productId, $sessionId);
        
        // Verify product was removed from wishlist
        $wishlistItems = $this->getWishlistItems($userId);
        $this->assertCount(0, $wishlistItems);
        
        // Verify product is in cart
        $cartItems = $this->getCartItems($sessionId);
        $this->assertGreaterThan(0, count($cartItems));
    }
    
    // Helper methods for evolution testing
    
    private static function createEvolutionTestData()
    {
        // Create some base test data for evolution tests
        $stmt = self::$db->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->execute(['evolutionuser', 'evolution@example.com', password_hash('password', PASSWORD_DEFAULT)]);
        
        $stmt = self::$db->prepare("INSERT INTO products (name, description, price, category_id, stock_quantity) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute(['Evolution Test Product', 'Product for testing evolution', 99.99, 1, 10]);
    }
    
    private function createWishlistTable()
    {
        self::$db->exec("
            CREATE TABLE IF NOT EXISTS wishlist (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER,
                product_id INTEGER,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                UNIQUE(user_id, product_id)
            )
        ");
    }
    
    private function createCartTable()
    {
        self::$db->exec("
            CREATE TABLE IF NOT EXISTS cart (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                session_id VARCHAR(255),
                product_id INTEGER,
                quantity INTEGER,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ");
    }
    
    private function addProductToWishlist($userId, $productId)
    {
        $stmt = self::$db->prepare("INSERT OR IGNORE INTO wishlist (user_id, product_id) VALUES (?, ?)");
        return $stmt->execute([$userId, $productId]);
    }
    
    private function getWishlistItems($userId)
    {
        $stmt = self::$db->prepare("SELECT * FROM wishlist WHERE user_id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    private function addToCart($cartItem)
    {
        $stmt = self::$db->prepare("INSERT INTO cart (session_id, product_id, quantity) VALUES (?, ?, ?)");
        return $stmt->execute([$cartItem['session_id'], $cartItem['product_id'], $cartItem['quantity']]);
    }
    
    private function getCartItems($sessionId)
    {
        $stmt = self::$db->prepare("SELECT * FROM cart WHERE session_id = ?");
        $stmt->execute([$sessionId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    private function getProductAPIv1($productId)
    {
        $stmt = self::$db->prepare("SELECT id, name, price, description FROM products WHERE id = ?");
        $stmt->execute([$productId]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
    
    private function getProductAPIv2($productId)
    {
        $stmt = self::$db->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$productId]);
        $product = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        if ($product) {
            // Add v2-specific fields
            $product['category'] = 'Electronics'; // Mock category name
            $product['images'] = []; // Mock images array
            $product['reviews'] = []; // Mock reviews array
        }
        
        return $product;
    }
    
    private function checkFeatureFlag($featureName)
    {
        $featureFlags = [
            'new_checkout_process' => false,
            'advanced_search' => true,
            'social_login' => false,
            'recommendation_engine' => true
        ];
        
        return $featureFlags[$featureName] ?? false;
    }
    
    private function performAdvancedSearch($criteria)
    {
        // Mock advanced search
        return [
            ['id' => 1, 'name' => 'Smartphone', 'price' => 299.99],
            ['id' => 2, 'name' => 'Tablet', 'price' => 199.99]
        ];
    }
    
    private function getProductRecommendations($userId)
    {
        // Mock recommendation engine
        return [
            ['id' => 3, 'name' => 'Recommended Product 1'],
            ['id' => 4, 'name' => 'Recommended Product 2']
        ];
    }
    
    private function migrateLegacyOrders()
    {
        // Simple migration from legacy_orders to orders
        $stmt = self::$db->query("SELECT * FROM legacy_orders");
        $legacyOrders = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        $insertStmt = self::$db->prepare("INSERT INTO orders (user_id, total_amount, status, created_at) VALUES (?, ?, ?, ?)");
        
        foreach ($legacyOrders as $legacyOrder) {
            $insertStmt->execute([
                1, // Default user_id
                $legacyOrder['order_total'],
                'completed',
                $legacyOrder['order_date']
            ]);
        }
    }
    
    private function performComplexOperationWithNewFeatures()
    {
        // Simulate a complex operation that uses multiple new features
        for ($i = 0; $i < 100; $i++) {
            $stmt = self::$db->prepare("SELECT COUNT(*) FROM products WHERE price > ?");
            $stmt->execute([rand(1, 100)]);
            $stmt->fetchColumn();
        }
    }
    
    private function moveWishlistToCart($userId, $productId, $sessionId)
    {
        // Remove from wishlist
        $stmt = self::$db->prepare("DELETE FROM wishlist WHERE user_id = ? AND product_id = ?");
        $stmt->execute([$userId, $productId]);
        
        // Add to cart if not already there
        $stmt = self::$db->prepare("INSERT OR IGNORE INTO cart (session_id, product_id, quantity) VALUES (?, ?, ?)");
        $stmt->execute([$sessionId, $productId, 1]);
    }
    
    // Include helper methods from other test classes
    private function createUserInDatabase($userData)
    {
        $stmt = self::$db->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->execute([$userData['username'], $userData['email'], password_hash($userData['password'], PASSWORD_DEFAULT)]);
        return self::$db->lastInsertId();
    }
    
    private function getUserFromDatabase($userId)
    {
        $stmt = self::$db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
    
    private function createProductInDatabase($productData)
    {
        $stmt = self::$db->prepare("INSERT INTO products (name, description, price, category_id, stock_quantity) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$productData['name'], $productData['description'], $productData['price'], $productData['category_id'], $productData['stock_quantity']]);
        return self::$db->lastInsertId();
    }
    
    private function updateUserProfile($userId, $updateData)
    {
        $setParts = [];
        $values = [];
        
        foreach ($updateData as $field => $value) {
            $setParts[] = "$field = ?";
            $values[] = $value;
        }
        
        $values[] = $userId;
        $sql = "UPDATE users SET " . implode(', ', $setParts) . " WHERE id = ?";
        
        $stmt = self::$db->prepare($sql);
        return $stmt->execute($values);
    }
}
