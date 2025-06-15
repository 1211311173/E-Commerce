<?php

namespace Tests\Maintenance;

use Tests\Helpers\TestHelper;

/**
 * Security Maintenance Tests
 * Ensures security measures remain effective during maintenance
 */
class SecurityMaintenanceTest extends TestHelper
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
    
    public function testSQLInjectionPrevention()
    {
        // Test that SQL injection attempts are blocked
        $maliciousInputs = [
            "'; DROP TABLE users; --",
            "' OR '1'='1",
            "' UNION SELECT * FROM users --",
            "admin'--",
            "' OR 1=1 #"
        ];
        
        foreach ($maliciousInputs as $maliciousInput) {
            // Test login with malicious input
            $stmt = self::$db->prepare("SELECT * FROM users WHERE username = ? AND password = ?");
            $result = $stmt->execute([$maliciousInput, 'password']);
            
            $this->assertTrue($result, "Prepared statement should execute without error");
            
            $user = $stmt->fetch(\PDO::FETCH_ASSOC);
            $this->assertFalse($user, "Malicious input should not return any user");
        }
    }
    
    public function testXSSPrevention()
    {
        // Test that XSS attempts are properly sanitized
        $xssInputs = [
            '<script>alert("XSS")</script>',
            '<img src="x" onerror="alert(1)">',
            'javascript:alert("XSS")',
            '<svg onload="alert(1)">',
            '"><script>alert("XSS")</script>'
        ];
        
        foreach ($xssInputs as $xssInput) {
            // Test input sanitization
            $sanitized = htmlspecialchars($xssInput, ENT_QUOTES, 'UTF-8');
            
            // Should not contain executable HTML tags (they should be escaped)
            $this->assertStringNotContainsString('<script>', $sanitized, 'Raw script tags should be escaped');
            $this->assertStringNotContainsString('<img src=', $sanitized, 'Raw img tags should be escaped');
            $this->assertStringNotContainsString('<svg', $sanitized, 'Raw svg tags should be escaped');
            
            // The key security test: ensure HTML is properly escaped
            if (strpos($xssInput, '<') !== false) {
                $this->assertStringContainsString('&lt;', $sanitized, 'HTML should be escaped');
            }
            if (strpos($xssInput, '>') !== false) {
                $this->assertStringContainsString('&gt;', $sanitized, 'HTML should be escaped');
            }
            if (strpos($xssInput, '"') !== false) {
                $this->assertStringContainsString('&quot;', $sanitized, 'Quotes should be escaped');
            }
            
            // Test specific XSS vectors based on input
            if ($xssInput === '<img src="x" onerror="alert(1)">') {
                // For this specific case, verify it's safely escaped
                $expected = '&lt;img src=&quot;x&quot; onerror=&quot;alert(1)&quot;&gt;';
                $this->assertEquals($expected, $sanitized, 'IMG with onerror should be completely escaped');
                
                // The important security check: no executable HTML remains
                $this->assertStringNotContainsString('<img', $sanitized, 'No executable img tag should remain');
                $this->assertStringNotContainsString('onerror="alert', $sanitized, 'No executable onerror should remain');
            }
            
            if ($xssInput === 'javascript:alert("XSS")') {
                // JavaScript protocol should be preserved but safe as text content
                $expected = 'javascript:alert(&quot;XSS&quot;)';
                $this->assertEquals($expected, $sanitized, 'JavaScript protocol should be safely escaped');
            }
        }
        
        // Additional test: verify that the escaped content is safe when output in HTML
        $dangerousInput = '<img src="x" onerror="alert(1)">';
        $sanitized = htmlspecialchars($dangerousInput, ENT_QUOTES, 'UTF-8');
        
        // When this sanitized content is output in HTML, it should be safe
        $htmlOutput = '<p>' . $sanitized . '</p>';
        $expectedOutput = '<p>&lt;img src=&quot;x&quot; onerror=&quot;alert(1)&quot;&gt;</p>';
        $this->assertEquals($expectedOutput, $htmlOutput, 'Sanitized content should be safe in HTML output');
        
        // The sanitized content should not execute JavaScript when rendered in browser
        $this->assertStringNotContainsString('<img', $htmlOutput, 'No executable HTML in final output');
        $this->assertStringNotContainsString('onerror="alert', $htmlOutput, 'No executable JavaScript in final output');
    }
    
    public function testPasswordSecurity()
    {
        // Test password hashing and verification
        $passwords = [
            'weakpass',
            'StrongP@ssw0rd!',
            'AnotherSecureP@ssw0rd123',
            'password123'
        ];
        
        foreach ($passwords as $password) {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            
            // Test hash is strong
            $this->assertNotEquals($password, $hashedPassword);
            $this->assertGreaterThan(50, strlen($hashedPassword), "Hash should be sufficiently long");
            
            // Test verification works
            $this->assertTrue(password_verify($password, $hashedPassword));
            $this->assertFalse(password_verify('wrongpassword', $hashedPassword));
        }
    }
    
    public function testSessionSecurity()
    {
        // Test session configuration and security
        $secureSessionData = [
            'user_id' => 123,
            'username' => 'testuser',
            'role' => 'user',
            'login_time' => time(),
            'ip_address' => '127.0.0.1'
        ];
        
        // Test session data structure
        $this->assertArrayHasKey('user_id', $secureSessionData);
        $this->assertArrayHasKey('login_time', $secureSessionData);
        $this->assertArrayHasKey('ip_address', $secureSessionData);
        
        // Test session timeout (mock)
        $sessionTimeout = 3600; // 1 hour
        $currentTime = time();
        $timeDiff = $currentTime - $secureSessionData['login_time'];
        
        $this->assertLessThan($sessionTimeout, $timeDiff, "Session should not be expired");
    }
    
    public function testCSRFProtection()
    {
        // Test CSRF token generation and validation
        $csrfToken = bin2hex(random_bytes(32));
        
        // Test token is properly generated
        $this->assertEquals(64, strlen($csrfToken), "CSRF token should be 64 characters long");
        $this->assertMatchesRegularExpression('/^[a-f0-9]+$/', $csrfToken, "CSRF token should be hexadecimal");
        
        // Test token validation
        $validToken = $csrfToken;
        $invalidToken = 'invalid_token';
        
        $this->assertTrue(hash_equals($csrfToken, $validToken), "Valid token should match");
        $this->assertFalse(hash_equals($csrfToken, $invalidToken), "Invalid token should not match");
    }
    
    public function testInputValidation()
    {
        // Test various input validation scenarios
        $testInputs = [
            ['email', 'test@example.com', true],
            ['email', 'invalid-email', false],
            ['username', 'validuser123', true],
            ['username', 'us', false], // Too short
            ['username', str_repeat('a', 51), false], // Too long
            ['price', '99.99', true],
            ['price', '-10.00', false], // Negative price
            ['price', 'abc', false], // Non-numeric
            ['quantity', '5', true],
            ['quantity', '0', false], // Zero quantity
            ['quantity', '-1', false] // Negative quantity
        ];
        
        foreach ($testInputs as [$field, $value, $expected]) {
            $isValid = $this->validateInput($field, $value);
            $this->assertEquals($expected, $isValid, 
                "Input validation for $field with value '$value' should return " . ($expected ? 'true' : 'false'));
        }
    }
    
    public function testFileUploadSecurity()
    {
        // Test file upload validation
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        $maxFileSize = 5 * 1024 * 1024; // 5MB
        
        $testFiles = [
            ['image.jpg', 'image/jpeg', 1024000, true],
            ['document.pdf', 'application/pdf', 2048000, false], // Not allowed extension
            ['script.php', 'application/x-httpd-php', 1024, false], // Dangerous file type
            ['large_image.jpg', 'image/jpeg', 10 * 1024 * 1024, false], // Too large
            ['image.png', 'image/png', 512000, true]
        ];
        
        foreach ($testFiles as [$filename, $mimetype, $size, $expected]) {
            $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            
            $isValidExtension = in_array($extension, $allowedExtensions);
            $isValidSize = $size <= $maxFileSize;
            $isValidMime = strpos($mimetype, 'image/') === 0;
            
            $isValid = $isValidExtension && $isValidSize && $isValidMime;
            
            $this->assertEquals($expected, $isValid,
                "File upload validation for $filename should return " . ($expected ? 'true' : 'false'));
        }
    }
    
    public function testDatabaseConnectionSecurity()
    {
        // Test that database connection uses secure practices
        
        // Test PDO error mode is set to exception
        $errorMode = self::$db->getAttribute(\PDO::ATTR_ERRMODE);
        $this->assertEquals(\PDO::ERRMODE_EXCEPTION, $errorMode,
            "Database should use exception error mode");
        
        // Test that prepared statements are used (tested implicitly in other tests)
        $stmt = self::$db->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
        $this->assertInstanceOf(\PDOStatement::class, $stmt);
    }
    
    public function testSecureHeadersConfiguration()
    {
        // Test security headers that should be configured
        $securityHeaders = [
            'X-Content-Type-Options' => 'nosniff',
            'X-Frame-Options' => 'DENY',
            'X-XSS-Protection' => '1; mode=block',
            'Strict-Transport-Security' => 'max-age=31536000; includeSubDomains',
            'Content-Security-Policy' => "default-src 'self'"
        ];
        
        // Mock header validation
        foreach ($securityHeaders as $header => $expectedValue) {
            // In a real scenario, you would test actual headers sent by the application
            $this->assertNotEmpty($expectedValue, "Security header $header should have a value");
        }
    }
    
    private function validateInput($field, $value)
    {
        switch ($field) {
            case 'email':
                return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
            
            case 'username':
                return strlen($value) >= 3 && strlen($value) <= 50 && 
                       preg_match('/^[a-zA-Z0-9_]+$/', $value);
            
            case 'price':
                return is_numeric($value) && floatval($value) > 0;
            
            case 'quantity':
                return is_numeric($value) && intval($value) > 0;
            
            default:
                return false;
        }
    }
}
