<?php

namespace Tests\Src;

/**
 * Cart Test Class
 * Tests shopping cart functionality
 */
class CartTest extends BaseTest
{
    /**
     * Test adding item to cart
     */
    public function testAddToCart()
    {
        // Create test product
        $productId = $this->createTestProduct('Test Cart Product', 25.99);
        $sessionId = 'test_session_' . uniqid();
        
        // Add to cart
        $stmt = $this->testDatabase->prepare(
            "INSERT INTO cart (session_id, product_id, quantity) VALUES (?, ?, ?)"
        );
        $result = $stmt->execute([$sessionId, $productId, 2]);
        
        $this->assertTrue($result);
        $this->assertDatabaseHas('cart', [
            'session_id' => $sessionId,
            'product_id' => $productId,
            'quantity' => 2
        ]);
    }
    
    /**
     * Test updating cart quantity
     */
    public function testUpdateCartQuantity()
    {
        // Create test product and add to cart
        $productId = $this->createTestProduct('Update Test Product', 15.99);
        $sessionId = 'test_session_' . uniqid();
        
        // Add to cart
        $stmt = $this->testDatabase->prepare(
            "INSERT INTO cart (session_id, product_id, quantity) VALUES (?, ?, ?)"
        );
        $stmt->execute([$sessionId, $productId, 1]);
        
        // Update quantity
        $stmt = $this->testDatabase->prepare(
            "UPDATE cart SET quantity = ? WHERE session_id = ? AND product_id = ?"
        );
        $result = $stmt->execute([5, $sessionId, $productId]);
        
        $this->assertTrue($result);
        $this->assertDatabaseHas('cart', [
            'session_id' => $sessionId,
            'product_id' => $productId,
            'quantity' => 5
        ]);
    }
    
    /**
     * Test removing item from cart
     */
    public function testRemoveFromCart()
    {
        // Create test product and add to cart
        $productId = $this->createTestProduct('Remove Test Product', 30.99);
        $sessionId = 'test_session_' . uniqid();
        
        // Add to cart
        $stmt = $this->testDatabase->prepare(
            "INSERT INTO cart (session_id, product_id, quantity) VALUES (?, ?, ?)"
        );
        $stmt->execute([$sessionId, $productId, 3]);
        
        // Verify item is in cart
        $this->assertDatabaseHas('cart', [
            'session_id' => $sessionId,
            'product_id' => $productId
        ]);
        
        // Remove from cart
        $stmt = $this->testDatabase->prepare(
            "DELETE FROM cart WHERE session_id = ? AND product_id = ?"
        );
        $result = $stmt->execute([$sessionId, $productId]);
        
        $this->assertTrue($result);
        $this->assertDatabaseMissing('cart', [
            'session_id' => $sessionId,
            'product_id' => $productId
        ]);
    }
    
    /**
     * Test cart total calculation
     */
    public function testCartTotalCalculation()
    {
        $sessionId = 'test_session_' . uniqid();
        
        // Add multiple products to cart
        $product1Id = $this->createTestProduct('Product 1', 10.00);
        $product2Id = $this->createTestProduct('Product 2', 25.50);
        
        // Add to cart
        $stmt = $this->testDatabase->prepare(
            "INSERT INTO cart (session_id, product_id, quantity) VALUES (?, ?, ?)"
        );
        $stmt->execute([$sessionId, $product1Id, 2]); // 2 * 10.00 = 20.00
        $stmt->execute([$sessionId, $product2Id, 1]); // 1 * 25.50 = 25.50
        
        // Calculate total
        $stmt = $this->testDatabase->prepare("
            SELECT SUM(c.quantity * p.product_price) as total
            FROM cart c
            JOIN products p ON c.product_id = p.product_id
            WHERE c.session_id = ?
        ");
        $stmt->execute([$sessionId]);
        $total = $stmt->fetchColumn();
        
        $this->assertEquals(45.50, $total);
    }
    
    /**
     * Test cart item count
     */
    public function testCartItemCount()
    {
        $sessionId = 'test_session_' . uniqid();
        
        // Add multiple products
        $product1Id = $this->createTestProduct('Count Product 1', 5.99);
        $product2Id = $this->createTestProduct('Count Product 2', 8.99);
        
        $stmt = $this->testDatabase->prepare(
            "INSERT INTO cart (session_id, product_id, quantity) VALUES (?, ?, ?)"
        );
        $stmt->execute([$sessionId, $product1Id, 3]);
        $stmt->execute([$sessionId, $product2Id, 2]);
        
        // Count total items
        $stmt = $this->testDatabase->prepare(
            "SELECT SUM(quantity) as total_items FROM cart WHERE session_id = ?"
        );
        $stmt->execute([$sessionId]);
        $totalItems = $stmt->fetchColumn();
        
        $this->assertEquals(5, $totalItems);
    }
}
