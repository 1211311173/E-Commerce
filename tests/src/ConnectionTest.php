<?php

namespace Tests;

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use PHPUnit\Framework\TestCase;

class ConnectionTest extends TestCase
{
    public function testSeleniumConnection()
    {
        try {
            $capabilities = DesiredCapabilities::microsoftEdge();
            $capabilities->setCapability('ms:edgeChromium', true);
            $capabilities->setCapability('ms:edgeOptions', [
                'args' => ['--headless', '--disable-gpu', '--no-sandbox', '--disable-dev-shm-usage']
            ]);
            
            $driver = RemoteWebDriver::create('http://localhost:4444', $capabilities);
            $this->assertNotNull($driver->getSessionID(), "Failed to create WebDriver session");
            
            // Try to navigate to a simple page
            $driver->get('http://localhost/E-Commerce/login.php');
            $this->assertStringContainsString('Login', $driver->getTitle(), "Failed to load login page");
            
            $driver->quit();
        } catch (\Exception $e) {
            $this->fail("Connection test failed: " . $e->getMessage());
        }
    }
} 