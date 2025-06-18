<?php

namespace Tests\Src;

/**
 * Profile Test Class
 * Tests user profile functionality
 */
class ProfileTest extends BaseTest
{
    /**
     * Test user profile creation
     */
    public function testCreateUserProfile()
    {
        $email = 'profile.test@example.com';
        $name = 'Profile Test User';
        $password = 'securepassword123';
        
        $userId = $this->createTestUser($email, $password);
        
        // Update user name
        $stmt = $this->testDatabase->prepare(
            "UPDATE customer SET customer_name = ? WHERE customer_id = ?"
        );
        $result = $stmt->execute([$name, $userId]);
        
        $this->assertTrue($result);
        $this->assertDatabaseHas('customer', [
            'customer_id' => $userId,
            'customer_email' => $email,
            'customer_name' => $name
        ]);
    }
    
    /**
     * Test user profile retrieval
     */
    public function testGetUserProfile()
    {
        $email = 'get.profile@example.com';
        $name = 'Get Profile User';
        $userId = $this->createTestUser($email);
        
        // Update user name
        $stmt = $this->testDatabase->prepare(
            "UPDATE customer SET customer_name = ? WHERE customer_id = ?"
        );
        $stmt->execute([$name, $userId]);
        
        // Retrieve profile
        $stmt = $this->testDatabase->prepare(
            "SELECT * FROM customer WHERE customer_id = ?"
        );
        $stmt->execute([$userId]);
        $profile = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        $this->assertNotFalse($profile);
        $this->assertEquals($email, $profile['customer_email']);
        $this->assertEquals($name, $profile['customer_name']);
        $this->assertEquals($userId, $profile['customer_id']);
    }
    
    /**
     * Test user profile update
     */
    public function testUpdateUserProfile()
    {
        $originalEmail = 'original@example.com';
        $newEmail = 'updated@example.com';
        $newName = 'Updated User Name';
        
        $userId = $this->createTestUser($originalEmail);
        
        // Update profile
        $stmt = $this->testDatabase->prepare(
            "UPDATE customer SET customer_email = ?, customer_name = ? WHERE customer_id = ?"
        );
        $result = $stmt->execute([$newEmail, $newName, $userId]);
        
        $this->assertTrue($result);
        
        // Verify update
        $this->assertDatabaseHas('customer', [
            'customer_id' => $userId,
            'customer_email' => $newEmail,
            'customer_name' => $newName
        ]);
        
        // Verify old email is gone
        $this->assertDatabaseMissing('customer', [
            'customer_id' => $userId,
            'customer_email' => $originalEmail
        ]);
    }
    
    /**
     * Test password update
     */
    public function testUpdatePassword()
    {
        $email = 'password.test@example.com';
        $oldPassword = 'oldpassword123';
        $newPassword = 'newpassword456';
        
        $userId = $this->createTestUser($email, $oldPassword);
        
        // Get current password hash
        $stmt = $this->testDatabase->prepare(
            "SELECT customer_pwd FROM customer WHERE customer_id = ?"
        );
        $stmt->execute([$userId]);
        $oldHash = $stmt->fetchColumn();
        
        // Update password
        $newHash = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $this->testDatabase->prepare(
            "UPDATE customer SET customer_pwd = ? WHERE customer_id = ?"
        );
        $result = $stmt->execute([$newHash, $userId]);
        
        $this->assertTrue($result);
        
        // Verify password was updated
        $stmt = $this->testDatabase->prepare(
            "SELECT customer_pwd FROM customer WHERE customer_id = ?"
        );
        $stmt->execute([$userId]);
        $currentHash = $stmt->fetchColumn();
        
        $this->assertNotEquals($oldHash, $currentHash);
        $this->assertTrue(password_verify($newPassword, $currentHash));
        $this->assertFalse(password_verify($oldPassword, $currentHash));
    }
    
    /**
     * Test profile validation
     */
    public function testProfileValidation()
    {
        // Test email validation
        $invalidEmails = [
            'invalid-email',
            'missing@',
            '@missing-domain.com',
            'spaces in@email.com'
        ];
        
        foreach ($invalidEmails as $email) {
            $isValid = filter_var($email, FILTER_VALIDATE_EMAIL);
            $this->assertFalse($isValid, "Email '$email' should be invalid");
        }
        
        // Test valid email
        $validEmail = 'valid@example.com';
        $isValid = filter_var($validEmail, FILTER_VALIDATE_EMAIL);
        $this->assertNotFalse($isValid, "Email '$validEmail' should be valid");
    }
    
    /**
     * Test profile deletion
     */
    public function testDeleteProfile()
    {
        $email = 'delete.test@example.com';
        $userId = $this->createTestUser($email);
        
        // Verify user exists
        $this->assertDatabaseHas('customer', [
            'customer_id' => $userId,
            'customer_email' => $email
        ]);
        
        // Delete profile
        $stmt = $this->testDatabase->prepare(
            "DELETE FROM customer WHERE customer_id = ?"
        );
        $result = $stmt->execute([$userId]);
        
        $this->assertTrue($result);
        
        // Verify deletion
        $this->assertDatabaseMissing('customer', [
            'customer_id' => $userId
        ]);
    }
}
