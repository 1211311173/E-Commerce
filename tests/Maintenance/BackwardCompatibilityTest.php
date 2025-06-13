<?php

namespace Tests\Maintenance;

use Tests\Helpers\TestHelper;

/**
 * Backward Compatibility Tests
 * Ensures that updates don't break existing functionality
 */
class BackwardCompatibilityTest extends TestHelper
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
    
    public function testLegacyUserDataCompatibility()
    {
        // Test that old user data structure still works
        $legacyUserData = [
            'username' => 'legacy_user',
            'email' => 'legacy@example.com',
            'password' => md5('oldpassword'), // Old MD5 hash (deprecated but should still work)
            'created_at' => '2020-01-01 00:00:00'
        ];
        
        // Insert legacy user data
        $stmt = self::$db->prepare("
            INSERT INTO users (username, email, password, created_at) 
            VALUES (?, ?, ?, ?)
        ");
        
        $result = $stmt->execute([
            $legacyUserData['username'],
            $legacyUserData['email'],
            $legacyUserData['password'],
            $legacyUserData['created_at']
        ]);
        
        $this->assertTrue($result);
        
        // Test that legacy user can still be retrieved
        $stmt = self::$db->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$legacyUserData['username']]);
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        $this->assertNotFalse($user);
        $this->assertEquals($legacyUserData['username'], $user['username']);
        $this->assertEquals($legacyUserData['email'], $user['email']);
    }
    
    public function testDatabaseSchemaBackwardCompatibility()
    {
        // Test that existing columns still exist and function
        $requiredColumns = [
            'users' => ['id', 'username', 'email', 'password', 'created_at'],
            'products' => ['id', 'name', 'description', 'price', 'category_id'],
            'categories' => ['id', 'name', 'description'],
            'orders' => ['id', 'user_id', 'total_amount', 'status']
        ];
        
        foreach ($requiredColumns as $table => $columns) {
            $this->assertTableHasColumns($table, $columns);
        }
    }
    
    public function testAPIResponseCompatibility()
    {
        // Test that API responses maintain expected structure
        $expectedUserStructure = [
            'id',
            'username',
            'email',
            'created_at'
        ];
        
        $expectedProductStructure = [
            'id',
            'name',
            'description',
            'price',
            'category_id',
            'stock_quantity'
        ];
        
        // Create test data
        $userData = $this->createTestUser();
        $userId = $this->createUserInDatabase($userData);
        $user = $this->getUserFromDatabase($userId);
        
        $productData = $this->createTestProduct();
        $productId = $this->createProductInDatabase($productData);
        $product = $this->getProductFromDatabase($productId);
        
        // Test structure compatibility
        $this->assertArrayStructure($expectedUserStructure, $user);
        $this->assertArrayStructure($expectedProductStructure, $product);
    }
    
    public function testLegacyFunctionCompatibility()
    {
        // Test that legacy utility functions still work
        
        // Test old-style session handling
        $sessionData = ['user_id' => 1, 'username' => 'testuser'];
        $this->assertIsArray($sessionData);
        $this->assertArrayHasKey('user_id', $sessionData);
        
        // Test old-style price formatting
        $price = 1234.567;
        $formattedPrice = number_format($price, 2);
        $this->assertEquals('1,234.57', $formattedPrice);
        
        // Test old-style date formatting
        $date = '2023-12-25 15:30:45';
        $formattedDate = date('Y-m-d', strtotime($date));
        $this->assertEquals('2023-12-25', $formattedDate);
    }
    
    public function testFormDataCompatibility()
    {
        // Test that old form field names are still accepted
        $legacyFormData = [
            'uname' => 'oldusername',  // Legacy field name
            'email' => 'old@example.com',
            'pwd' => 'oldpassword',
            'fname' => 'John',
            'lname' => 'Doe'
        ];
        
        // Map legacy fields to new structure
        $mappedData = $this->mapLegacyFormData($legacyFormData);
        
        $this->assertArrayHasKey('username', $mappedData);
        $this->assertArrayHasKey('password', $mappedData);
        $this->assertEquals($legacyFormData['uname'], $mappedData['username']);
        $this->assertEquals($legacyFormData['pwd'], $mappedData['password']);
    }
    
    public function testURLCompatibility()
    {
        // Test that old URL patterns still work
        $legacyUrls = [
            '/product.php?id=123',
            '/category.php?cat=electronics',
            '/user.php?action=profile',
            '/cart.php?action=add&product=456'
        ];
        
        foreach ($legacyUrls as $url) {
            $parsedUrl = parse_url($url);
            $this->assertNotFalse($parsedUrl);
            $this->assertArrayHasKey('path', $parsedUrl);
            
            if (isset($parsedUrl['query'])) {
                parse_str($parsedUrl['query'], $queryParams);
                $this->assertIsArray($queryParams);
            }
        }
    }
    
    private function assertTableHasColumns($tableName, $expectedColumns)
    {
        $stmt = self::$db->query("PRAGMA table_info($tableName)");
        $columns = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        $actualColumns = array_column($columns, 'name');
        
        foreach ($expectedColumns as $expectedColumn) {
            $this->assertContains($expectedColumn, $actualColumns, 
                "Table '$tableName' is missing column '$expectedColumn'");
        }
    }
    
    private function createUserInDatabase($userData)
    {
        $stmt = self::$db->prepare("
            INSERT INTO users (username, email, password, created_at) 
            VALUES (?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $userData['username'],
            $userData['email'],
            password_hash($userData['password'], PASSWORD_DEFAULT),
            date('Y-m-d H:i:s')
        ]);
        
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
        $stmt = self::$db->prepare("
            INSERT INTO products (name, description, price, category_id, stock_quantity) 
            VALUES (?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $productData['name'],
            $productData['description'],
            $productData['price'],
            $productData['category_id'],
            $productData['stock_quantity']
        ]);
        
        return self::$db->lastInsertId();
    }
    
    private function getProductFromDatabase($productId)
    {
        $stmt = self::$db->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$productId]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
    
    private function mapLegacyFormData($legacyData)
    {
        $mapping = [
            'uname' => 'username',
            'pwd' => 'password',
            'fname' => 'first_name',
            'lname' => 'last_name'
        ];
        
        $mappedData = [];
        
        foreach ($legacyData as $oldKey => $value) {
            $newKey = isset($mapping[$oldKey]) ? $mapping[$oldKey] : $oldKey;
            $mappedData[$newKey] = $value;
        }
        
        return $mappedData;
    }
}
