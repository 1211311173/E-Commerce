<?php

namespace Tests;

use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;

class ProfileTest extends BaseTest
{
    public function testUpdateProfile()
    {
        $this->login('test@example.com', 'password123');
        
        // Go to profile page
        $this->driver->get($this->baseUrl . 'profile.php');
        
        // Wait for profile page to load
        $this->driver->wait(10)->until(
            WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::className('profile-form'))
        );
        
        // Update profile information
        $this->driver->findElement(WebDriverBy::name('first_name'))->clear()->sendKeys('John');
        $this->driver->findElement(WebDriverBy::name('last_name'))->clear()->sendKeys('Doe');
        $this->driver->findElement(WebDriverBy::name('phone'))->clear()->sendKeys('1234567890');
        
        // Submit profile update
        $this->driver->findElement(WebDriverBy::name('update_profile'))->click();
        
        // Wait for success message
        $this->driver->wait(10)->until(
            WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::className('alert-success'))
        );
    }

    public function testUpdateAddress()
    {
        $this->login('test@example.com', 'password123');
        
        // Go to profile page
        $this->driver->get($this->baseUrl . 'profile.php');
        
        // Wait for profile page to load
        $this->driver->wait(10)->until(
            WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::className('profile-form'))
        );
        
        // Click on address tab
        $this->driver->findElement(WebDriverBy::linkText('Address'))->click();
        
        // Wait for address form to load
        $this->driver->wait(10)->until(
            WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::className('address-form'))
        );
        
        // Update address information
        $this->driver->findElement(WebDriverBy::name('address'))->clear()->sendKeys('456 New Street');
        $this->driver->findElement(WebDriverBy::name('city'))->clear()->sendKeys('New City');
        $this->driver->findElement(WebDriverBy::name('state'))->clear()->sendKeys('New State');
        $this->driver->findElement(WebDriverBy::name('zip'))->clear()->sendKeys('54321');
        
        // Submit address update
        $this->driver->findElement(WebDriverBy::name('update_address'))->click();
        
        // Wait for success message
        $this->driver->wait(10)->until(
            WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::className('alert-success'))
        );
    }

    public function testUpdateContact()
    {
        $this->login('test@example.com', 'password123');
        
        // Go to contact page
        $this->driver->get($this->baseUrl . 'contact.php');
        
        // Wait for contact form to load
        $this->driver->wait(10)->until(
            WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::className('contact-form'))
        );
        
        // Fill contact form
        $this->driver->findElement(WebDriverBy::name('subject'))->sendKeys('Test Subject');
        $this->driver->findElement(WebDriverBy::name('message'))->sendKeys('This is a test message');
        
        // Submit contact form
        $this->driver->findElement(WebDriverBy::name('submit'))->click();
        
        // Wait for success message
        $this->driver->wait(10)->until(
            WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::className('alert-success'))
        );
    }
} 