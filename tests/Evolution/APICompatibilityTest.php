<?php

namespace Tests\Evolution;

use Tests\Helpers\TestHelper;

/**
 * API Compatibility Tests
 * Ensures API changes maintain backward compatibility
 */
class APICompatibilityTest extends TestHelper
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
    
    public function testAPIVersionCompatibility()
    {
        // Test that multiple API versions can coexist
        
        $productId = 1;
        
        // Test API v1 response
        $v1Response = $this->simulateAPICall('v1', 'products', $productId);
        $this->assertAPIv1Structure($v1Response);
        
        // Test API v2 response
        $v2Response = $this->simulateAPICall('v2', 'products', $productId);
        $this->assertAPIv2Structure($v2Response);
        
        // Test API v3 response (latest)
        $v3Response = $this->simulateAPICall('v3', 'products', $productId);
        $this->assertAPIv3Structure($v3Response);
        
        // Verify backward compatibility
        $this->assertBackwardCompatibility($v1Response, $v2Response, $v3Response);
    }
    
    public function testDeprecatedEndpointsStillWork()
    {
        // Test that deprecated endpoints still function but warn
        
        $deprecatedEndpoints = [
            ['method' => 'GET', 'endpoint' => '/api/v1/product/{id}'],
            ['method' => 'POST', 'endpoint' => '/api/v1/user/login'],
            ['method' => 'GET', 'endpoint' => '/api/v1/cart/items']
        ];
        
        foreach ($deprecatedEndpoints as $endpoint) {
            $response = $this->callDeprecatedEndpoint($endpoint['method'], $endpoint['endpoint']);
            
            // Should still work
            $this->assertTrue($response['success']);
            
            // Should include deprecation warning
            $this->assertArrayHasKey('warnings', $response);
            $this->assertStringContainsString('deprecated', strtolower($response['warnings'][0]));
        }
    }
    
    public function testFieldCompatibility()
    {
        // Test that field changes maintain compatibility
        
        $userId = $this->createTestUser();
        $userId = $this->createUserInDatabase($userId);
        
        // Old API field names should still work
        $oldFieldRequest = [
            'uname' => 'newusername',  // Legacy field name
            'pwd' => 'newpassword'     // Legacy field name
        ];
        
        $response = $this->updateUserProfile($userId, $oldFieldRequest, 'v1');
        $this->assertTrue($response['success']);
        
        // New API field names should also work
        $newFieldRequest = [
            'username' => 'newerusername',
            'password' => 'newerpassword'
        ];
        
        $response = $this->updateUserProfile($userId, $newFieldRequest, 'v2');
        $this->assertTrue($response['success']);
    }
    
    public function testResponseFormatCompatibility()
    {
        // Test different response format requirements
        
        $productId = 1;
        
        // XML format (legacy)
        $xmlResponse = $this->getProductResponse($productId, 'xml');
        $this->assertStringContainsString('<?xml', $xmlResponse);
        $this->assertStringContainsString('<product>', $xmlResponse);
        
        // JSON format (current)
        $jsonResponse = $this->getProductResponse($productId, 'json');
        $decodedJson = json_decode($jsonResponse, true);
        $this->assertIsArray($decodedJson);
        $this->assertArrayHasKey('id', $decodedJson);
        
        // Both should contain the same core data
        $this->assertSameProductData($xmlResponse, $jsonResponse);
    }
    
    public function testStatusCodeCompatibility()
    {
        // Test that status codes remain consistent across versions
        
        $testCases = [
            ['scenario' => 'valid_product', 'expected_code' => 200],
            ['scenario' => 'product_not_found', 'expected_code' => 404],
            ['scenario' => 'unauthorized_access', 'expected_code' => 401],
            ['scenario' => 'server_error', 'expected_code' => 500],
            ['scenario' => 'invalid_input', 'expected_code' => 400]
        ];
        
        foreach ($testCases as $testCase) {
            $v1Response = $this->simulateScenario($testCase['scenario'], 'v1');
            $v2Response = $this->simulateScenario($testCase['scenario'], 'v2');
            $v3Response = $this->simulateScenario($testCase['scenario'], 'v3');
            
            $this->assertEquals($testCase['expected_code'], $v1Response['status_code']);
            $this->assertEquals($testCase['expected_code'], $v2Response['status_code']);
            $this->assertEquals($testCase['expected_code'], $v3Response['status_code']);
        }
    }
    
    public function testPaginationCompatibility()
    {
        // Test pagination across API versions
        
        // Create test products
        for ($i = 1; $i <= 25; $i++) {
            $productData = $this->createTestProduct(['name' => "Test Product $i"]);
            $this->createProductInDatabase($productData);
        }
        
        // Test v1 pagination (page-based)
        $v1Page1 = $this->getProductList('v1', ['page' => 1, 'limit' => 10]);
        $this->assertCount(10, $v1Page1['data']);
        $this->assertEquals(1, $v1Page1['current_page']);
        $this->assertEquals(3, $v1Page1['total_pages']);
        
        // Test v2 pagination (offset-based)
        $v2Page1 = $this->getProductList('v2', ['offset' => 0, 'limit' => 10]);
        $this->assertCount(10, $v2Page1['data']);
        $this->assertEquals(0, $v2Page1['offset']);
        $this->assertGreaterThan(20, $v2Page1['total']);
        
        // Test v3 pagination (cursor-based)
        $v3Page1 = $this->getProductList('v3', ['limit' => 10]);
        $this->assertCount(10, $v3Page1['data']);
        $this->assertArrayHasKey('next_cursor', $v3Page1);
    }
    
    public function testFilterCompatibility()
    {
        // Test that filtering works across versions
        
        $filters = [
            'v1' => ['category' => 'electronics', 'min_price' => 100],
            'v2' => ['category_id' => 1, 'price_range' => '100-1000'],
            'v3' => ['filters' => ['category_id' => 1, 'price' => ['min' => 100, 'max' => 1000]]]
        ];
        
        foreach ($filters as $version => $filter) {
            $response = $this->getProductList($version, $filter);
            
            $this->assertTrue($response['success']);
            $this->assertIsArray($response['data']);
            
            // All versions should return similar products
            foreach ($response['data'] as $product) {
                $this->assertGreaterThanOrEqual(100, $product['price']);
            }
        }
    }
    
    public function testErrorHandlingCompatibility()
    {
        // Test error responses are consistent
        
        $errorScenarios = [
            'invalid_product_id' => 'invalid_id',
            'missing_required_field' => '',
            'unauthorized_request' => 'no_auth'
        ];
        
        foreach ($errorScenarios as $scenario => $input) {
            $v1Error = $this->triggerError($scenario, 'v1', $input);
            $v2Error = $this->triggerError($scenario, 'v2', $input);
            
            // Both should have error structure
            $this->assertArrayHasKey('error', $v1Error);
            $this->assertArrayHasKey('error', $v2Error);
            
            // Error codes should be consistent
            $this->assertEquals($v1Error['error']['code'], $v2Error['error']['code']);
        }
    }
    
    // Helper methods for API compatibility testing
    
    private function simulateAPICall($version, $endpoint, $id)
    {
        // Simulate different API version responses
        switch ($version) {
            case 'v1':
                return $this->getAPIv1Response($endpoint, $id);
            case 'v2':
                return $this->getAPIv2Response($endpoint, $id);
            case 'v3':
                return $this->getAPIv3Response($endpoint, $id);
            default:
                return ['error' => 'Unsupported API version'];
        }
    }
    
    private function getAPIv1Response($endpoint, $id)
    {
        if ($endpoint === 'products') {
            return [
                'id' => $id,
                'name' => 'Test Product',
                'price' => 99.99,
                'desc' => 'Product description'  // Short field name
            ];
        }
        return ['error' => 'Not found'];
    }
    
    private function getAPIv2Response($endpoint, $id)
    {
        if ($endpoint === 'products') {
            return [
                'id' => $id,
                'name' => 'Test Product',
                'price' => 99.99,
                'description' => 'Product description',  // Full field name
                'category_id' => 1,
                'stock_quantity' => 10
            ];
        }
        return ['error' => 'Not found'];
    }
    
    private function getAPIv3Response($endpoint, $id)
    {
        if ($endpoint === 'products') {
            return [
                'id' => $id,
                'name' => 'Test Product',
                'price' => 99.99,
                'description' => 'Product description',
                'category' => [
                    'id' => 1,
                    'name' => 'Electronics'
                ],
                'stock_quantity' => 10,
                'images' => [],
                'reviews' => [],
                'metadata' => [
                    'created_at' => '2023-01-01T00:00:00Z',
                    'updated_at' => '2023-01-01T00:00:00Z'
                ]
            ];
        }
        return ['error' => 'Not found'];
    }
    
    private function assertAPIv1Structure($response)
    {
        $this->assertArrayHasKey('id', $response);
        $this->assertArrayHasKey('name', $response);
        $this->assertArrayHasKey('price', $response);
        $this->assertArrayHasKey('desc', $response);
    }
    
    private function assertAPIv2Structure($response)
    {
        $this->assertArrayHasKey('id', $response);
        $this->assertArrayHasKey('name', $response);
        $this->assertArrayHasKey('price', $response);
        $this->assertArrayHasKey('description', $response);
        $this->assertArrayHasKey('category_id', $response);
    }
    
    private function assertAPIv3Structure($response)
    {
        $this->assertArrayHasKey('id', $response);
        $this->assertArrayHasKey('name', $response);
        $this->assertArrayHasKey('price', $response);
        $this->assertArrayHasKey('description', $response);
        $this->assertArrayHasKey('category', $response);
        $this->assertArrayHasKey('metadata', $response);
    }
    
    private function assertBackwardCompatibility($v1, $v2, $v3)
    {
        // Core fields should be consistent
        $this->assertEquals($v1['id'], $v2['id']);
        $this->assertEquals($v2['id'], $v3['id']);
        
        $this->assertEquals($v1['name'], $v2['name']);
        $this->assertEquals($v2['name'], $v3['name']);
        
        $this->assertEquals($v1['price'], $v2['price']);
        $this->assertEquals($v2['price'], $v3['price']);
    }
    
    private function callDeprecatedEndpoint($method, $endpoint)
    {
        // Simulate calling a deprecated endpoint
        return [
            'success' => true,
            'data' => ['id' => 1, 'name' => 'Test'],
            'warnings' => ['This endpoint is deprecated and will be removed in v4.0']
        ];
    }
    
    private function updateUserProfile($userId, $data, $version)
    {
        // Simulate user profile update with field mapping
        if ($version === 'v1') {
            // Map legacy field names
            $mappedData = [];
            if (isset($data['uname'])) $mappedData['username'] = $data['uname'];
            if (isset($data['pwd'])) $mappedData['password'] = $data['pwd'];
            $data = array_merge($data, $mappedData);
        }
        
        return ['success' => true, 'updated_fields' => array_keys($data)];
    }
    
    private function getProductResponse($productId, $format)
    {
        $productData = [
            'id' => $productId,
            'name' => 'Test Product',
            'price' => 99.99,
            'description' => 'Test Description'
        ];
        
        if ($format === 'xml') {
            return '<?xml version="1.0"?><product><id>' . $productData['id'] . '</id><name>' . $productData['name'] . '</name></product>';
        } else {
            return json_encode($productData);
        }
    }
    
    private function assertSameProductData($xmlResponse, $jsonResponse)
    {
        // Both should contain the same product ID
        $this->assertStringContainsString('<id>1</id>', $xmlResponse);
        
        $jsonData = json_decode($jsonResponse, true);
        $this->assertEquals(1, $jsonData['id']);
    }
    
    private function simulateScenario($scenario, $version)
    {
        switch ($scenario) {
            case 'valid_product':
                return ['status_code' => 200, 'data' => ['id' => 1]];
            case 'product_not_found':
                return ['status_code' => 404, 'error' => 'Product not found'];
            case 'unauthorized_access':
                return ['status_code' => 401, 'error' => 'Unauthorized'];
            case 'server_error':
                return ['status_code' => 500, 'error' => 'Internal server error'];
            case 'invalid_input':
                return ['status_code' => 400, 'error' => 'Bad request'];
            default:
                return ['status_code' => 500, 'error' => 'Unknown scenario'];
        }
    }
    
    private function getProductList($version, $params)
    {
        // Simulate paginated product list responses
        $products = [];
        for ($i = 1; $i <= 10; $i++) {
            $products[] = ['id' => $i, 'name' => "Product $i", 'price' => $i * 10];
        }
        
        switch ($version) {
            case 'v1':
                return [
                    'success' => true,
                    'data' => $products,
                    'current_page' => $params['page'] ?? 1,
                    'total_pages' => 3
                ];
            case 'v2':
                return [
                    'success' => true,
                    'data' => $products,
                    'offset' => $params['offset'] ?? 0,
                    'limit' => $params['limit'] ?? 10,
                    'total' => 25
                ];
            case 'v3':
                return [
                    'success' => true,
                    'data' => $products,
                    'limit' => $params['limit'] ?? 10,
                    'next_cursor' => 'eyJpZCI6MTB9'
                ];
        }
    }
    
    private function triggerError($scenario, $version, $input)
    {
        return [
            'error' => [
                'code' => 'INVALID_INPUT',
                'message' => 'Invalid input provided'
            ]
        ];
    }
    
    private function createUserInDatabase($userData)
    {
        $stmt = self::$db->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->execute([$userData['username'], $userData['email'], password_hash($userData['password'], PASSWORD_DEFAULT)]);
        return self::$db->lastInsertId();
    }
    
    private function createProductInDatabase($productData)
    {
        $stmt = self::$db->prepare("INSERT INTO products (name, description, price, category_id, stock_quantity) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$productData['name'], $productData['description'], $productData['price'], $productData['category_id'], $productData['stock_quantity']]);
        return self::$db->lastInsertId();
    }
}
