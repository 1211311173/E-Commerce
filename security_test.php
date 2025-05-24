<?php
/**
 * Security Test Script
 * This script tests the security implementations
 * Run this script to verify SQL injection prevention is working
 */

require_once 'includes/config.php';

echo "<h1>Security Implementation Test</h1>";
echo "<style>
    .test-pass { color: green; font-weight: bold; }
    .test-fail { color: red; font-weight: bold; }
    .test-section { margin: 20px 0; padding: 10px; border: 1px solid #ccc; }
</style>";

// Test 1: Input Validator Tests
echo "<div class='test-section'>";
echo "<h2>1. Input Validator Tests</h2>";

// Test email validation
$test_emails = [
    'valid@example.com' => true,
    'invalid-email' => false,
    'test@' => false,
    '@example.com' => false,
    'user@domain.co.uk' => true
];

echo "<h3>Email Validation:</h3>";
foreach ($test_emails as $email => $expected) {
    $result = InputValidator::validateEmail($email);
    $passed = ($result !== false) === $expected;
    $status = $passed ? "<span class='test-pass'>PASS</span>" : "<span class='test-fail'>FAIL</span>";
    echo "Email: '$email' - Expected: " . ($expected ? 'valid' : 'invalid') . " - $status<br>";
}

// Test integer validation
echo "<h3>Integer Validation:</h3>";
$test_integers = [
    ['5', 1, 10, true],
    ['15', 1, 10, false],
    ['-5', 1, 10, false],
    ['abc', 1, 10, false],
    ['7', null, null, true]
];

foreach ($test_integers as $test) {
    [$value, $min, $max, $expected] = $test;
    $result = InputValidator::validateInt($value, $min, $max);
    $passed = ($result !== false) === $expected;
    $status = $passed ? "<span class='test-pass'>PASS</span>" : "<span class='test-fail'>FAIL</span>";
    echo "Value: '$value' (min: $min, max: $max) - Expected: " . ($expected ? 'valid' : 'invalid') . " - $status<br>";
}

// Test string sanitization
echo "<h3>String Sanitization:</h3>";
$test_strings = [
    '<script>alert("xss")</script>' => '&lt;script&gt;alert(&quot;xss&quot;)&lt;/script&gt;',
    'Normal text' => 'Normal text',
    '"quotes" & ampersand' => '&quot;quotes&quot; &amp; ampersand'
];

foreach ($test_strings as $input => $expected) {
    $result = InputValidator::sanitizeString($input);
    $passed = $result === $expected;
    $status = $passed ? "<span class='test-pass'>PASS</span>" : "<span class='test-fail'>FAIL</span>";
    echo "Input: '$input' - Result: '$result' - $status<br>";
}

echo "</div>";

// Test 2: SecureDB Class Tests
echo "<div class='test-section'>";
echo "<h2>2. SecureDB Class Tests</h2>";

try {
    // Test secure select
    echo "<h3>Secure SELECT Test:</h3>";
    $result = $secureDB->select("SELECT COUNT(*) as count FROM customer WHERE customer_id = ?", [1], 'i');
    if ($result) {
        echo "<span class='test-pass'>PASS</span> - Secure SELECT query executed successfully<br>";
    } else {
        echo "<span class='test-fail'>FAIL</span> - Secure SELECT query failed<br>";
    }
    
    // Test with potential SQL injection payload
    echo "<h3>SQL Injection Prevention Test:</h3>";
    $malicious_input = "1' OR '1'='1";
    $result = $secureDB->select("SELECT COUNT(*) as count FROM customer WHERE customer_id = ?", [$malicious_input], 's');
    if ($result) {
        $row = $result->fetch_assoc();
        if ($row['count'] == 0) {
            echo "<span class='test-pass'>PASS</span> - SQL injection payload was safely handled (returned 0 results)<br>";
        } else {
            echo "<span class='test-fail'>FAIL</span> - SQL injection payload may have succeeded (returned {$row['count']} results)<br>";
        }
    } else {
        echo "<span class='test-pass'>PASS</span> - SQL injection payload was rejected<br>";
    }
    
} catch (Exception $e) {
    echo "<span class='test-fail'>ERROR</span> - Database test failed: " . $e->getMessage() . "<br>";
}

echo "</div>";

// Test 3: Search Function Security
echo "<div class='test-section'>";
echo "<h2>3. Search Function Security Test</h2>";

$malicious_searches = [
    "'; DROP TABLE products; --",
    "' UNION SELECT 1,2,3,4,5 --",
    "<script>alert('xss')</script>",
    "normal search term"
];

echo "<h3>Search Term Validation:</h3>";
foreach ($malicious_searches as $search_term) {
    $sanitized = InputValidator::validateSearchTerm($search_term);
    if ($sanitized === false) {
        echo "<span class='test-pass'>PASS</span> - Malicious search term '$search_term' was rejected<br>";
    } else {
        echo "<span class='test-fail'>WARN</span> - Search term '$search_term' was sanitized to: '$sanitized'<br>";
    }
}

echo "</div>";

// Test 4: CSRF Protection
echo "<div class='test-section'>";
echo "<h2>4. CSRF Protection Test</h2>";

try {
    $token1 = CSRFProtection::generateToken();
    $token2 = CSRFProtection::generateToken();
    
    if ($token1 === $token2) {
        echo "<span class='test-pass'>PASS</span> - CSRF tokens are consistent within session<br>";
    } else {
        echo "<span class='test-fail'>FAIL</span> - CSRF tokens are inconsistent<br>";
    }
    
    $valid = CSRFProtection::validateToken($token1);
    if ($valid) {
        echo "<span class='test-pass'>PASS</span> - CSRF token validation works<br>";
    } else {
        echo "<span class='test-fail'>FAIL</span> - CSRF token validation failed<br>";
    }
    
    $invalid = CSRFProtection::validateToken('invalid_token');
    if (!$invalid) {
        echo "<span class='test-pass'>PASS</span> - Invalid CSRF token was rejected<br>";
    } else {
        echo "<span class='test-fail'>FAIL</span> - Invalid CSRF token was accepted<br>";
    }
    
} catch (Exception $e) {
    echo "<span class='test-fail'>ERROR</span> - CSRF test failed: " . $e->getMessage() . "<br>";
}

echo "</div>";

echo "<div class='test-section'>";
echo "<h2>Test Summary</h2>";
echo "<p>If all tests show <span class='test-pass'>PASS</span>, your security implementation is working correctly.</p>";
echo "<p>Any <span class='test-fail'>FAIL</span> results indicate areas that need attention.</p>";
echo "<p><strong>Note:</strong> This is a basic test. For production environments, consider using professional security testing tools.</p>";
echo "</div>";

?>
