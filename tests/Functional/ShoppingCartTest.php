<?php

namespace Tests\Functional;

use Tests\Helpers\TestHelper;

/**
 * Shopping Cart Functional Tests
 * Tests complete shopping cart workflow
 */
class ShoppingCartTest extends TestHelper
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
    
    public function testAddProductToCart()
    {
        // Create test product
        $productData = $this->createTestProduct();
        $productId = $this->createProductInDatabase($productData);
        
        // Simulate adding to cart
        $cartItem = [
            'product_id' => $productId,
            'quantity' => 2,
            'session_id' => 'test_session_' . uniqid()
        ];
        
        $this->addToCart($cartItem);
        
        // Verify cart contents
        $cartItems = $this->getCartItems($cartItem['session_id']);
        $this->assertCount(1, $cartItems);
        $this->assertEquals($cartItem['product_id'], $cartItems[0]['product_id']);
        $this->assertEquals($cartItem['quantity'], $cartItems[0]['quantity']);
    }
    
    public function testUpdateCartQuantity()
    {
        // Create test product and add to cart
        $productData = $this->createTestProduct();
        $productId = $this->createProductInDatabase($productData);
        $sessionId = 'test_session_' . uniqid();
        
        $cartItem = [
            'product_id' => $productId,
            'quantity' => 1,
            'session_id' => $sessionId
        ];
        
        $this->addToCart($cartItem);
        
        // Update quantity
        $this->updateCartQuantity($sessionId, $productId, 5);
        
        // Verify update
        $cartItems = $this->getCartItems($sessionId);
        $this->assertEquals(5, $cartItems[0]['quantity']);
    }
    
    public function testRemoveFromCart()
    {
        // Create test product and add to cart
        $productData = $this->createTestProduct();
        $productId = $this->createProductInDatabase($productData);
        $sessionId = 'test_session_' . uniqid();
        
        $cartItem = [
            'product_id' => $productId,
            'quantity' => 2,
            'session_id' => $sessionId
        ];
        
        $this->addToCart($cartItem);
        
        // Verify item is in cart
        $cartItems = $this->getCartItems($sessionId);
        $this->assertCount(1, $cartItems);
        
        // Remove from cart
        $this->removeFromCart($sessionId, $productId);
        
        // Verify removal
        $cartItems = $this->getCartItems($sessionId);
        $this->assertCount(0, $cartItems);
    }
    
    public function testCartTotalCalculation()
    {
        $sessionId = 'test_session_' . uniqid();
        
        // Add multiple products
        $product1 = $this->createTestProduct(['price' => 10.00]);
        $product1Id = $this->createProductInDatabase($product1);
        
        $product2 = $this->createTestProduct(['price' => 25.50]);
        $product2Id = $this->createProductInDatabase($product2);
        
        // Add to cart
        $this->addToCart(['product_id' => $product1Id, 'quantity' => 2, 'session_id' => $sessionId]);
        $this->addToCart(['product_id' => $product2Id, 'quantity' => 1, 'session_id' => $sessionId]);
        
        // Calculate total
        $total = $this->calculateCartTotal($sessionId);
        
        // Expected: (10.00 * 2) + (25.50 * 1) = 45.50
        $this->assertEquals(45.50, $total);
    }
    
    public function testStockValidation()
    {
        // Create product with limited stock
        $productData = $this->createTestProduct(['stock_quantity' => 5]);
        $productId = $this->createProductInDatabase($productData);
        $sessionId = 'test_session_' . uniqid();
        
        // Try to add more than available stock
        $cartItem = [
            'product_id' => $productId,
            'quantity' => 10, // More than available stock
            'session_id' => $sessionId
        ];
        
        $result = $this->addToCartWithValidation($cartItem);
        $this->assertFalse($result['success']);
        $this->assertStringContainsString('stock', strtolower($result['message']));
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
    
    private function addToCart($cartItem)
    {
        // Create cart table if not exists
        self::$db->exec("
            CREATE TABLE IF NOT EXISTS cart (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                session_id VARCHAR(255),
                product_id INTEGER,
                quantity INTEGER,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ");
        
        $stmt = self::$db->prepare("
            INSERT INTO cart (session_id, product_id, quantity) 
            VALUES (?, ?, ?)
        ");
        
        return $stmt->execute([
            $cartItem['session_id'],
            $cartItem['product_id'],
            $cartItem['quantity']
        ]);
    }
    
    private function addToCartWithValidation($cartItem)
    {
        // Check stock first
        $stmt = self::$db->prepare("SELECT stock_quantity FROM products WHERE id = ?");
        $stmt->execute([$cartItem['product_id']]);
        $product = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        if (!$product) {
            return ['success' => false, 'message' => 'Product not found'];
        }
        
        if ($cartItem['quantity'] > $product['stock_quantity']) {
            return ['success' => false, 'message' => 'Insufficient stock available'];
        }
        
        $this->addToCart($cartItem);
        return ['success' => true, 'message' => 'Added to cart successfully'];
    }
    
    private function getCartItems($sessionId)
    {
        $stmt = self::$db->prepare("
            SELECT c.*, p.name, p.price 
            FROM cart c 
            JOIN products p ON c.product_id = p.id 
            WHERE c.session_id = ?
        ");
        
        $stmt->execute([$sessionId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    private function updateCartQuantity($sessionId, $productId, $quantity)
    {
        $stmt = self::$db->prepare("
            UPDATE cart SET quantity = ? 
            WHERE session_id = ? AND product_id = ?
        ");
        
        return $stmt->execute([$quantity, $sessionId, $productId]);
    }
    
    private function removeFromCart($sessionId, $productId)
    {
        $stmt = self::$db->prepare("
            DELETE FROM cart 
            WHERE session_id = ? AND product_id = ?
        ");
        
        return $stmt->execute([$sessionId, $productId]);
    }
    
    private function calculateCartTotal($sessionId)
    {
        $stmt = self::$db->prepare("
            SELECT SUM(c.quantity * p.price) as total 
            FROM cart c 
            JOIN products p ON c.product_id = p.id 
            WHERE c.session_id = ?
        ");
        
        $stmt->execute([$sessionId]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        return floatval($result['total'] ?? 0);
    }
}
