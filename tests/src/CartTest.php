<?php

namespace Tests;

use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;

class CartTest extends BaseTest
{
    public function testAddToCart()
    {
        // Login first
        $this->login('test@example.com', 'password123');

        // Go to product page
        $this->driver->get($this->baseUrl . 'product.php');
        
        // Wait for product page to load
        $this->driver->wait(10)->until(
            WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector('.product-item'))
        );
        
        // Add first product to cart
        $addToCartButton = $this->driver->findElement(WebDriverBy::cssSelector('.add-to-cart'));
        $addToCartButton->click();

        // Wait for success message or cart update
        $this->driver->wait(10)->until(
            WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::className('alert-success'))
        );
    }
} 