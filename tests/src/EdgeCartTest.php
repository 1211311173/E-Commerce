<?php

namespace Tests;

use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\Exception\TimeoutException;
use Facebook\WebDriver\Exception\WebDriverException;

class EdgeCartTest extends CartTest
{
    protected $browser = 'edge';

    public function testAddToCart()
    {
        $this->driver->get('http://localhost/E-commerce/');
        
        // Wait for and click the first product title link
        $productLink = $this->waitForElement(WebDriverBy::className('showcase-title'));
        
        // Click the product title
        $productLink->click();

        // Get the current URL
        $currentUrl = $this->driver->getCurrentURL();
        $this->assertStringContainsString('http://localhost/E-commerce/viewdetail.php?id=1&category=', $currentUrl);
        
        // Wait for the product detail page to load
        try {
            $this->driver->wait(20)->until(
                WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::className('product_deatail_container'))
            );
        } catch (TimeoutException $e) {
            $this->fail("Product detail page failed to load: " . $e->getMessage() . 
                "\nCurrent URL: " . $this->driver->getCurrentURL() . 
                "\nPage Source: " . $this->driver->getPageSource());
        }
        
        // Wait for and click the add to cart button
        $addToCartButton = $this->waitForElement(WebDriverBy::id('add-to-cart-btn'));
        $addToCartButton->click();

        // Wait for the cart count to update in the shopping bag icon
        $this->driver->wait(10)->until(
            WebDriverExpectedCondition::presenceOfElementLocated(
                WebDriverBy::id('cart-count')
            )
        );
        
        // Verify cart count increased in the shopping bag icon
        $cartCount = $this->driver->findElement(
            WebDriverBy::id('cart-count')
        );
        $this->assertGreaterThan(0, (int)$cartCount->getText());
    }

    public function testViewCartWhenCartIsEmpty()
    {
        $this->driver->get('http://localhost/E-commerce/');
        
        // Wait for and click the shopping bag icon
        $shoppingBagIcon = $this->waitForElement(WebDriverBy::id('cart-button'));
        $shoppingBagIcon->click();

        // Wait for the cart page to load
        $this->driver->wait(10)->until(
            WebDriverExpectedCondition::presenceOfElementLocated(
                WebDriverBy::id('cart-count')
            )
        );
        
        // Verify cart count increased in the shopping bag icon
        $cartCount = $this->driver->findElement(
            WebDriverBy::id('cart-count')
        );
        $this->assertEquals(0, (int)$cartCount->getText());
    }

    public function testViewCartWhenCartIsNotEmpty()
    {
        $this->driver->get($this->baseUrl);
        
        // First add an item to cart
        $productLink = $this->waitForElement(WebDriverBy::className('showcase-title'));
        $productLink->click();
        
        // Wait for the product detail page to load
        $this->driver->wait(20)->until(
            WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::className('product_deatail_container'))
        );
        
        // Click add to cart button
        $addToCartButton = $this->waitForElement(WebDriverBy::id('add-to-cart-btn'));
        $addToCartButton->click();
        
        // Wait for cart count to update and page to refresh
        $this->driver->wait(10)->until(
            WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::id('cart-count'))
        );
        
        // Wait a moment for the page to fully refresh
        sleep(1);
        
        // Find the cart button again after page refresh
        $shoppingBagIcon = $this->waitForElement(WebDriverBy::id('cart-button'));
        $shoppingBagIcon->click();

        // Wait for the cart page to load
        $this->driver->wait(10)->until(
            WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::id('cart-count'))
        );

        // Verify cart count is greater than 0
        $cartCount = $this->driver->findElement(WebDriverBy::id('cart-count'));
        $this->assertGreaterThan(0, (int)$cartCount->getText());
    }

    public function testCheckoutWithValidData()
    {
        $this->driver->get('http://localhost/E-commerce/');
        
        // Wait for and click the first product title link
        $productLink = $this->waitForElement(WebDriverBy::className('showcase-title'));
        
        // Click the product title
        $productLink->click();

        // Get the current URL
        $currentUrl = $this->driver->getCurrentURL();
        $this->assertStringContainsString('http://localhost/E-commerce/viewdetail.php?id=1&category=', $currentUrl);
        
        // Wait for the product detail page to load
        try {
            $this->driver->wait(20)->until(
                WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::className('product_deatail_container'))
            );
        } catch (TimeoutException $e) {
            $this->fail("Product detail page failed to load: " . $e->getMessage() . 
                "\nCurrent URL: " . $this->driver->getCurrentURL() . 
                "\nPage Source: " . $this->driver->getPageSource());
        }
        
        // Click add to cart button
        $addToCartButton = $this->waitForElement(WebDriverBy::id('add-to-cart-btn'));
        $addToCartButton->click();

        // Click add to cart button
        $cartButton = $this->waitForElement(WebDriverBy::id('cart-button'));
        $cartButton->click();

        $currentUrl = $this->driver->getCurrentURL();
        $this->assertStringContainsString('http://localhost/E-commerce/cart.php', $currentUrl);

        // Click add to cart button
        $proceedToCheckoutButton = $this->waitForElement(WebDriverBy::id('proceed-to-checkout-btn'));
        $proceedToCheckoutButton->click();

        $currentUrl = $this->driver->getCurrentURL();
        $this->assertStringContainsString('http://localhost/E-commerce/checkout.php', $currentUrl);
    }

    public function testCheckoutWithInvalidData()
    {
        // First add an item to cart
        $this->driver->get($this->baseUrl);
        $productLink = $this->waitForElement(WebDriverBy::className('showcase-title'));
        $productLink->click();
        
        // Wait for product detail page
        $this->driver->wait(20)->until(
            WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::className('product_deatail_container'))
        );
        
        // Add to cart
        $addToCartButton = $this->waitForElement(WebDriverBy::id('add-to-cart-btn'));
        $addToCartButton->click();
        
        // Wait for cart count update
        $this->driver->wait(10)->until(
            WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::id('cart-count'))
        );
        
        // Go to cart page
        $this->driver->get($this->baseUrl . 'cart.php');
        
        // Wait for cart page and click checkout
        $checkoutButton = $this->waitForElement(WebDriverBy::name('proceed_to_checkout_action'));
        $checkoutButton->click();
        
        // Test Case 1: Empty cart
        $this->driver->get($this->baseUrl . 'cart.php');
        $checkoutButton = $this->waitForElement(WebDriverBy::name('proceed_to_checkout_action'));
        $checkoutButton->click();
        
        // Verify error message for empty cart
        $errorMessage = $this->waitForElement(WebDriverBy::className('error-ms'));
        $this->assertStringContainsString('No items available in cart', $errorMessage->getText());
        
        // Test Case 2: Invalid product data
        $this->driver->get($this->baseUrl . 'viewdetail.php?id=1&category=test');
        $buyNowButton = $this->waitForElement(WebDriverBy::name('buy_now_action'));
        $buyNowButton->click();
        
        // Verify error message for invalid product data
        $errorMessage = $this->waitForElement(WebDriverBy::className('error-ms'));
        $this->assertStringContainsString('Invalid product data', $errorMessage->getText());
        
        // Test Case 3: Invalid quantity
        $this->driver->get($this->baseUrl . 'viewdetail.php?id=1&category=test');
        $quantityInput = $this->waitForElement(WebDriverBy::id('p_qty'));
        $quantityInput->clear();
        $quantityInput->sendKeys('0'); // Invalid quantity
        
        $buyNowButton = $this->waitForElement(WebDriverBy::id('buy-now-btn'));
        $buyNowButton->click();
        
        // Verify error message for invalid quantity
        $errorMessage = $this->waitForElement(WebDriverBy::className('error-ms'));
        $this->assertStringContainsString('Invalid quantity', $errorMessage->getText());
    }

    /**
     * Test checkout with empty cart
     */
    public function testCheckoutWithEmptyCart()
    {
        // Go to cart page
        $this->driver->get($this->baseUrl . 'cart.php');
        
        // Wait for cart page and click checkout
        $checkoutButton = $this->waitForElement(WebDriverBy::name('proceed_to_checkout_action'));
        $checkoutButton->click();
        
        // Verify error message for empty cart
        $errorMessage = $this->waitForElement(WebDriverBy::className('error-ms'));
        $this->assertStringContainsString('No items available in cart', $errorMessage->getText());
    }

    /**
     * Test checkout with invalid product data
     */
    public function testCheckoutWithInvalidProductData()
    {
        $this->driver->get($this->baseUrl . 'viewdetail.php?id=1&category=test');
        $buyNowButton = $this->waitForElement(WebDriverBy::name('buy_now_action'));
        $buyNowButton->click();
        
        // Verify error message for invalid product data
        $errorMessage = $this->waitForElement(WebDriverBy::className('error-ms'));
        $this->assertStringContainsString('Invalid product data', $errorMessage->getText());
    }

    /**
     * Test checkout with invalid quantity
     */
    public function testCheckoutWithInvalidQuantity()
    {
        $this->driver->get($this->baseUrl . 'viewdetail.php?id=1&category=test');
        $quantityInput = $this->waitForElement(WebDriverBy::id('p_qty'));
        $quantityInput->clear();
        $quantityInput->sendKeys('0'); // Invalid quantity
        
        $buyNowButton = $this->waitForElement(WebDriverBy::name('buy_now_action'));
        $buyNowButton->click();
        
        // Verify error message for invalid quantity
        $errorMessage = $this->waitForElement(WebDriverBy::className('error-ms'));
        $this->assertStringContainsString('Invalid quantity', $errorMessage->getText());
    }

    /**
     * Test checkout with invalid shipping address
     */
    public function testCheckoutWithInvalidShippingAddress()
    {
        // First add an item to cart
        $this->driver->get($this->baseUrl);
        $productLink = $this->waitForElement(WebDriverBy::className('showcase-title'));
        $productLink->click();
        
        // Wait for product detail page
        $this->driver->wait(20)->until(
            WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::className('product_deatail_container'))
        );
        
        // Add to cart
        $addToCartButton = $this->waitForElement(WebDriverBy::id('add-to-cart-btn'));
        $addToCartButton->click();
        
        // Wait for cart count update
        $this->driver->wait(10)->until(
            WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::id('cart-count'))
        );
        
        // Go to cart page
        $this->driver->get($this->baseUrl . 'cart.php');
        
        // Wait for cart page and click checkout
        $checkoutButton = $this->waitForElement(WebDriverBy::name('proceed_to_checkout_action'));
        $checkoutButton->click();
        
        // Try to submit with empty shipping address
        $submitButton = $this->waitForElement(WebDriverBy::name('place_order'));
        $submitButton->click();
        
        // Verify error message for invalid shipping address
        $errorMessage = $this->waitForElement(WebDriverBy::className('error-ms'));
        $this->assertStringContainsString('Shipping address is required', $errorMessage->getText());
    }

    /**
     * Test product quantity using equivalence partitioning and boundary value analysis
     */
    public function testProductQuantityBoundaries()
    {
        $this->driver->get($this->baseUrl . 'viewdetail.php?id=1&category=test');
        $quantityInput = $this->waitForElement(WebDriverBy::id('p_qty'));
        
        // Equivalence Partitioning Tests
        // Valid equivalence class: quantities between 1 and 99
        $quantityInput->clear();
        $quantityInput->sendKeys('50');
        $addToCartButton = $this->waitForElement(WebDriverBy::id('add-to-cart-btn'));
        $addToCartButton->click();
        $this->driver->wait(10)->until(
            WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::id('cart-count'))
        );
        
        // Invalid equivalence class: negative numbers
        $quantityInput->clear();
        $quantityInput->sendKeys('-1');
        $addToCartButton->click();
        $errorMessage = $this->waitForElement(WebDriverBy::className('error-ms'));
        $this->assertStringContainsString('Invalid quantity', $errorMessage->getText());
        
        // Invalid equivalence class: non-numeric input
        $quantityInput->clear();
        $quantityInput->sendKeys('abc');
        $addToCartButton->click();
        $errorMessage = $this->waitForElement(WebDriverBy::className('error-ms'));
        $this->assertStringContainsString('Invalid quantity', $errorMessage->getText());
        
        // Boundary Value Analysis Tests
        // Lower boundary: 0
        $quantityInput->clear();
        $quantityInput->sendKeys('0');
        $addToCartButton->click();
        $errorMessage = $this->waitForElement(WebDriverBy::className('error-ms'));
        $this->assertStringContainsString('Invalid quantity', $errorMessage->getText());
        
        // Lower boundary + 1: 1
        $quantityInput->clear();
        $quantityInput->sendKeys('1');
        $addToCartButton->click();
        $this->driver->wait(10)->until(
            WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::id('cart-count'))
        );
        
        // Upper boundary: 99
        $quantityInput->clear();
        $quantityInput->sendKeys('99');
        $addToCartButton->click();
        $this->driver->wait(10)->until(
            WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::id('cart-count'))
        );
        
        // Upper boundary + 1: 100
        $quantityInput->clear();
        $quantityInput->sendKeys('100');
        $addToCartButton->click();
        $errorMessage = $this->waitForElement(WebDriverBy::className('error-ms'));
        $this->assertStringContainsString('Invalid quantity', $errorMessage->getText());
    }

    // public function testUpdateProfile()
    // {
    //     // Test implementation
    // }

    // public function testUpdateAddress()
    // {
    //     // Test implementation
    // }

    // public function testUpdateContact()
    // {
    //     // Test implementation
    // }
} 