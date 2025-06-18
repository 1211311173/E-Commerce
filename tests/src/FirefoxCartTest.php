<?php

namespace Tests\Src;

/**
 * Firefox Cart Test Class
 * Tests cart functionality specific to Firefox browser behavior
 */
class FirefoxCartTest extends BaseTest
{
    /**
     * Test cart functionality with Firefox-specific session handling
     */
    public function testFirefoxSessionHandling()
    {
        // Simulate Firefox session behavior
        $firefoxSessionId = 'firefox_session_' . uniqid();
        $productId = $this->createTestProduct('Firefox Test Product', 24.99);
        
        // Add product to cart
        $stmt = $this->testDatabase->prepare(
            "INSERT INTO cart (session_id, product_id, quantity) VALUES (?, ?, ?)"
        );
        $result = $stmt->execute([$firefoxSessionId, $productId, 1]);
        
        $this->assertTrue($result);
        $this->assertDatabaseHas('cart', [
            'session_id' => $firefoxSessionId,
            'product_id' => $productId,
            'quantity' => 1
        ]);
    }
    
    /**
     * Test Firefox-specific cookie behavior simulation
     */
    public function testFirefoxCookieBehavior()
    {
        // This test simulates Firefox-specific cookie handling
        // In a real scenario, this would test actual browser behavior
        
        $sessionId = 'firefox_cookie_test_' . uniqid();
        $productId = $this->createTestProduct('Cookie Test Product', 18.50);
        
        // Simulate adding to cart with Firefox cookie constraints
        $stmt = $this->testDatabase->prepare(
            "INSERT INTO cart (session_id, product_id, quantity) VALUES (?, ?, ?)"
        );
        $result = $stmt->execute([$sessionId, $productId, 3]);
        
        $this->assertTrue($result);
        
        // Verify cart persistence (simulating Firefox cookie behavior)
        $stmt = $this->testDatabase->prepare(
            "SELECT * FROM cart WHERE session_id = ?"
        );
        $stmt->execute([$sessionId]);
        $cartItems = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        $this->assertCount(1, $cartItems);
        $this->assertEquals($productId, $cartItems[0]['product_id']);
        $this->assertEquals(3, $cartItems[0]['quantity']);
    }
}
