<?php

namespace Tests\Functional;

use Tests\Helpers\TestHelper;

/**
 * User Registration Functional Tests
 * Tests complete user registration workflow
 */
class UserRegistrationTest extends TestHelper
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
    
    public function testCompleteUserRegistrationFlow()
    {
        // Simulate user registration form data
        $registrationData = [
            'username' => 'newuser123',
            'email' => 'newuser@example.com',
            'password' => 'SecurePass123!',
            'confirm_password' => 'SecurePass123!',
            'first_name' => 'John',
            'last_name' => 'Doe'
        ];
        
        // Test form validation
        $this->validateRegistrationData($registrationData);
        
        // Test user creation
        $userId = $this->createUserInDatabase($registrationData);
        $this->assertGreaterThan(0, $userId);
        
        // Test user can be retrieved
        $user = $this->getUserFromDatabase($userId);
        $this->assertNotNull($user);
        $this->assertEquals($registrationData['username'], $user['username']);
        $this->assertEquals($registrationData['email'], $user['email']);
        
        // Test password is properly hashed
        $this->assertTrue(password_verify($registrationData['password'], $user['password']));
        
        // Test duplicate registration prevention
        $this->expectDuplicateUserError($registrationData);
    }
    
    public function testLoginAfterRegistration()
    {
        // Create a test user
        $userData = $this->createTestUser([
            'username' => 'logintest',
            'email' => 'logintest@example.com',
            'password' => 'TestPass123!'
        ]);
        
        $userId = $this->createUserInDatabase($userData);
        
        // Test login functionality
        $loginResult = $this->simulateLogin($userData['username'], $userData['password']);
        $this->assertTrue($loginResult['success']);
        $this->assertEquals($userId, $loginResult['user_id']);
        
        // Test wrong password
        $wrongPasswordResult = $this->simulateLogin($userData['username'], 'wrongpassword');
        $this->assertFalse($wrongPasswordResult['success']);
        
        // Test non-existent user
        $nonExistentResult = $this->simulateLogin('nonexistent', 'password');
        $this->assertFalse($nonExistentResult['success']);
    }
    
    public function testUserProfileUpdate()
    {
        // Create a test user
        $userData = $this->createTestUser();
        $userId = $this->createUserInDatabase($userData);
        
        // Test profile update
        $updateData = [
            'email' => 'updated_' . $userData['email'],
            'first_name' => 'UpdatedFirstName',
            'last_name' => 'UpdatedLastName'
        ];
        
        $this->updateUserProfile($userId, $updateData);
        
        // Verify updates
        $updatedUser = $this->getUserFromDatabase($userId);
        $this->assertEquals($updateData['email'], $updatedUser['email']);
    }
    
    private function validateRegistrationData($data)
    {
        // Username validation
        $this->assertNotEmpty($data['username']);
        $this->assertGreaterThan(3, strlen($data['username']));
        $this->assertLessThan(50, strlen($data['username']));
        
        // Email validation
        $this->assertTrue(filter_var($data['email'], FILTER_VALIDATE_EMAIL) !== false);
        
        // Password validation
        $this->assertNotEmpty($data['password']);
        $this->assertGreaterThan(7, strlen($data['password']));
        $this->assertEquals($data['password'], $data['confirm_password']);
        
        // Name validation
        $this->assertNotEmpty($data['first_name']);
        $this->assertNotEmpty($data['last_name']);
    }
    
    private function createUserInDatabase($userData)
    {
        $stmt = self::$db->prepare("
            INSERT INTO users (username, email, password, created_at) 
            VALUES (?, ?, ?, ?)
        ");
        
        $result = $stmt->execute([
            $userData['username'],
            $userData['email'],
            password_hash($userData['password'], PASSWORD_DEFAULT),
            date('Y-m-d H:i:s')
        ]);
        
        $this->assertTrue($result);
        return self::$db->lastInsertId();
    }
    
    private function getUserFromDatabase($userId)
    {
        $stmt = self::$db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
    
    private function simulateLogin($username, $password)
    {
        $stmt = self::$db->prepare("SELECT id, password FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        if ($user && password_verify($password, $user['password'])) {
            return ['success' => true, 'user_id' => $user['id']];
        }
        
        return ['success' => false];
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
    
    private function expectDuplicateUserError($userData)
    {
        try {
            $this->createUserInDatabase($userData);
            $this->fail("Expected duplicate user error but none occurred");
        } catch (\Exception $e) {
            // Expected behavior - duplicate user should cause an error
            $this->assertStringContainsString('UNIQUE', $e->getMessage());
        }
    }
}
