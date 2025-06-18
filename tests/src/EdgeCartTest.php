<?php

namespace Tests\Src;

/**
 * Edge Cart Test Class
 * Tests edge cases and boundary conditions for shopping cart functionality
 */
class EdgeCartTest extends BaseTest
{
    /**
     * Test adding zero quantity to cart
     */
    public function testAddZeroQuantityToCart()
    {
        $productId = $this->createTestProduct('Zero Quantity Product', 19.99);
        $sessionId = 'test_session_' . uniqid();
        
        // Attempt to add zero quantity
        $stmt = $this->testDatabase->prepare(
            "INSERT INTO cart (session_id, product_id, quantity) VALUES (?, ?, ?)"
        );
        $result = $stmt->execute([$sessionId, $productId, 0]);
        
        $this->assertTrue($result);
        
        // Verify zero quantity item exists but should be handled appropriately
        $this->assertDatabaseHas('cart', [
            'session_id' => $sessionId,
            'product_id' => $productId,
            'quantity' => 0
        ]);
    }
    
    /**
     * Test adding negative quantity to cart
     */
    public function testAddNegativeQuantityToCart()
    {
        $productId = $this->createTestProduct('Negative Quantity Product', 29.99);
        $sessionId = 'test_session_' . uniqid();
        
        // Attempt to add negative quantity
        $stmt = $this->testDatabase->prepare(
            "INSERT INTO cart (session_id, product_id, quantity) VALUES (?, ?, ?)"
        );
        $result = $stmt->execute([$sessionId, $productId, -1]);
        
        $this->assertTrue($result);
        
        // Verify negative quantity is stored (application should validate this)
        $this->assertDatabaseHas('cart', [
            'session_id' => $sessionId,
            'product_id' => $productId,
            'quantity' => -1
        ]);
    }
    
    /**
     * Test adding very large quantity to cart
     */
    public function testAddLargeQuantityToCart()
    {
        $productId = $this->createTestProduct('Large Quantity Product', 5.99);
        $sessionId = 'test_session_' . uniqid();
        $largeQuantity = 999999;
        
        // Add large quantity
        $stmt = $this->testDatabase->prepare(
            "INSERT INTO cart (session_id, product_id, quantity) VALUES (?, ?, ?)"
        );
        $result = $stmt->execute([$sessionId, $productId, $largeQuantity]);
        
        $this->assertTrue($result);
        $this->assertDatabaseHas('cart', [
            'session_id' => $sessionId,
            'product_id' => $productId,
            'quantity' => $largeQuantity
        ]);
    }
    
    /**
     * Test adding non-existent product to cart
     */
    public function testAddNonExistentProductToCart()
    {
        $nonExistentProductId = 99999;
        $sessionId = 'test_session_' . uniqid();
        
        // Attempt to add non-existent product
        $stmt = $this->testDatabase->prepare(
            "INSERT INTO cart (session_id, product_id, quantity) VALUES (?, ?, ?)"
        );
        $result = $stmt->execute([$sessionId, $nonExistentProductId, 1]);
        
        // This should succeed at database level (foreign key constraints would prevent this in real scenario)
        $this->assertTrue($result);
        $this->assertDatabaseHas('cart', [
            'session_id' => $sessionId,
            'product_id' => $nonExistentProductId,
            'quantity' => 1
        ]);
    }
    
    /**
     * Test cart with extremely long session ID
     */
    public function testCartWithLongSessionId()
    {
        $productId = $this->createTestProduct('Long Session Product', 12.99);
        $longSessionId = str_repeat('a', 250); // Very long session ID
        
        $stmt = $this->testDatabase->prepare(
            "INSERT INTO cart (session_id, product_id, quantity) VALUES (?, ?, ?)"
        );
        $result = $stmt->execute([$longSessionId, $productId, 1]);
        
        $this->assertTrue($result);
        $this->assertDatabaseHas('cart', [
            'session_id' => $longSessionId,
            'product_id' => $productId,
            'quantity' => 1
        ]);
    }
    
    /**
     * Test cart with special characters in session ID
     */
    public function testCartWithSpecialCharacterSessionId()
    {
        $productId = $this->createTestProduct('Special Char Product', 8.99);
        $specialSessionId = "test_session_!@#$%^&*()_+-=[]{}|;':\",./<>?";
        
        $stmt = $this->testDatabase->prepare(
            "INSERT INTO cart (session_id, product_id, quantity) VALUES (?, ?, ?)"
        );
        $result = $stmt->execute([$specialSessionId, $productId, 2]);
        
        $this->assertTrue($result);
        $this->assertDatabaseHas('cart', [
            'session_id' => $specialSessionId,
            'product_id' => $productId,
            'quantity' => 2
        ]);
    }
    
    /**
     * Test multiple identical products in cart
     */
    public function testMultipleIdenticalProductsInCart()
    {
        $productId = $this->createTestProduct('Duplicate Product', 15.99);
        $sessionId = 'test_session_' . uniqid();
        
        // Add same product multiple times (should be handled by application logic)
        $stmt = $this->testDatabase->prepare(
            "INSERT INTO cart (session_id, product_id, quantity) VALUES (?, ?, ?)"
        );
        $stmt->execute([$sessionId, $productId, 1]);
        $stmt->execute([$sessionId, $productId, 2]);
        $stmt->execute([$sessionId, $productId, 3]);
        
        // Count how many entries exist
        $stmt = $this->testDatabase->prepare(
            "SELECT COUNT(*) FROM cart WHERE session_id = ? AND product_id = ?"
        );
        $stmt->execute([$sessionId, $productId]);
        $count = $stmt->fetchColumn();
        
        $this->assertEquals(3, $count);
        
        // Calculate total quantity
        $stmt = $this->testDatabase->prepare(
            "SELECT SUM(quantity) FROM cart WHERE session_id = ? AND product_id = ?"
        );
        $stmt->execute([$sessionId, $productId]);
        $totalQuantity = $stmt->fetchColumn();
        
        $this->assertEquals(6, $totalQuantity);
    }
    
    /**
     * Test cart total with zero-priced products
     */
    public function testCartTotalWithZeroPricedProducts()
    {
        $sessionId = 'test_session_' . uniqid();
        
        // Create products with different prices including zero
        $freeProductId = $this->createTestProduct('Free Product', 0.00);
        $paidProductId = $this->createTestProduct('Paid Product', 10.00);
        
        // Add to cart
        $stmt = $this->testDatabase->prepare(
            "INSERT INTO cart (session_id, product_id, quantity) VALUES (?, ?, ?)"
        );
        $stmt->execute([$sessionId, $freeProductId, 5]);
        $stmt->execute([$sessionId, $paidProductId, 2]);
        
        // Calculate total
        $stmt = $this->testDatabase->prepare("
            SELECT SUM(c.quantity * p.product_price) as total
            FROM cart c
            JOIN products p ON c.product_id = p.product_id
            WHERE c.session_id = ?
        ");
        $stmt->execute([$sessionId]);
        $total = $stmt->fetchColumn();
        
        $this->assertEquals(20.00, $total); // Only paid product contributes
    }
    
    /**
     * Test cart with products having negative prices
     */
    public function testCartWithNegativePricedProducts()
    {
        $sessionId = 'test_session_' . uniqid();
        
        // Create product with negative price (discount/refund scenario)
        $negativeProductId = $this->createTestProduct('Discount Product', -5.00);
        $normalProductId = $this->createTestProduct('Normal Product', 20.00);
        
        // Add to cart
        $stmt = $this->testDatabase->prepare(
            "INSERT INTO cart (session_id, product_id, quantity) VALUES (?, ?, ?)"
        );
        $stmt->execute([$sessionId, $negativeProductId, 1]);
        $stmt->execute([$sessionId, $normalProductId, 1]);
        
        // Calculate total
        $stmt = $this->testDatabase->prepare("
            SELECT SUM(c.quantity * p.product_price) as total
            FROM cart c
            JOIN products p ON c.product_id = p.product_id
            WHERE c.session_id = ?
        ");
        $stmt->execute([$sessionId]);
        $total = $stmt->fetchColumn();
        
        $this->assertEquals(15.00, $total); // 20.00 - 5.00
    }
    
    /**
     * Test cart operations with empty session ID
     */
    public function testCartWithEmptySessionId()
    {
        $productId = $this->createTestProduct('Empty Session Product', 7.99);
        $emptySessionId = '';
        
        $stmt = $this->testDatabase->prepare(
            "INSERT INTO cart (session_id, product_id, quantity) VALUES (?, ?, ?)"
        );
        $result = $stmt->execute([$emptySessionId, $productId, 1]);
        
        $this->assertTrue($result);
        $this->assertDatabaseHas('cart', [
            'session_id' => $emptySessionId,
            'product_id' => $productId,
            'quantity' => 1
        ]);
    }
}
