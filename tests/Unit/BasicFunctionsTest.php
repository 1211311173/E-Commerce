<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

/**
 * Basic Unit Test Example
 * Tests fundamental application components
 */
class BasicFunctionsTest extends TestCase
{
    public function testSanitizeInput()
    {
        // Test HTML sanitization
        $input = '<script>alert("xss")</script>Hello World';
        $expected = 'Hello World';
        $result = strip_tags($input);
        
        $this->assertEquals($expected, $result);
    }
    
    public function testValidateEmail()
    {
        // Test valid email
        $validEmail = 'test@example.com';
        $this->assertTrue(filter_var($validEmail, FILTER_VALIDATE_EMAIL) !== false);
        
        // Test invalid email
        $invalidEmail = 'invalid-email';
        $this->assertFalse(filter_var($invalidEmail, FILTER_VALIDATE_EMAIL) !== false);
    }
    
    public function testPasswordHashing()
    {
        $password = 'testpassword123';
        $hash = password_hash($password, PASSWORD_DEFAULT);
        
        // Test hash is created
        $this->assertNotEmpty($hash);
        $this->assertNotEquals($password, $hash);
        
        // Test password verification
        $this->assertTrue(password_verify($password, $hash));
        $this->assertFalse(password_verify('wrongpassword', $hash));
    }
    
    public function testPriceFormatting()
    {
        $price = 1234.56;
        $formatted = number_format($price, 2);
        
        $this->assertEquals('1,234.56', $formatted);
    }
    
    public function testArrayValidation()
    {
        $testArray = ['key1' => 'value1', 'key2' => 'value2'];
        
        $this->assertArrayHasKey('key1', $testArray);
        $this->assertArrayNotHasKey('key3', $testArray);
        $this->assertCount(2, $testArray);
    }
}
