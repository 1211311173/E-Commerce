<?php

namespace Tests;

use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Firefox\FirefoxOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\Exception\TimeoutException;
use Facebook\WebDriver\Exception\WebDriverException;
use PHPUnit\Framework\TestCase;

class BaseTest extends TestCase
{
    protected $driver;
    protected $baseUrl = 'http://localhost/E-Commerce/';
    protected $browser = 'chrome'; // Default browser
    protected $sessionId = null;

    protected function setUp(): void
    {
        try {
            switch ($this->browser) {
                case 'firefox':
                    $options = new FirefoxOptions();
                    $options->addArguments(['--headless', '--disable-gpu', '--no-sandbox']);
                    $capabilities = DesiredCapabilities::firefox();
                    $capabilities->setCapability(FirefoxOptions::CAPABILITY, $options);
                    break;

                case 'edge':
                    $capabilities = DesiredCapabilities::microsoftEdge();
                    $capabilities->setCapability('ms:edgeChromium', true);
                    // Add additional Edge-specific capabilities
                    $capabilities->setCapability('ms:inPrivate', false);
                    $capabilities->setCapability('ms:edgeOptions', [
                        'args' => ['--headless', '--disable-gpu', '--no-sandbox', '--disable-dev-shm-usage', '--window-size=1920,1080']
                    ]);
                    break;

                case 'chrome':
                default:
                    $options = new ChromeOptions();
                    $options->addArguments(['--headless', '--disable-gpu', '--no-sandbox', '--disable-dev-shm-usage']);
                    $capabilities = DesiredCapabilities::chrome();
                    $capabilities->setCapability(ChromeOptions::CAPABILITY, $options);
                    break;
            }
            
            $this->driver = RemoteWebDriver::create('http://localhost:4444', $capabilities);
            $this->sessionId = $this->driver->getSessionID();
            $this->driver->manage()->window()->maximize();
            
            // Set implicit wait time
            $this->driver->manage()->timeouts()->implicitlyWait(10);
            
            // Set page load timeout
            $this->driver->manage()->timeouts()->pageLoadTimeout(30);
            
        } catch (WebDriverException $e) {
            $this->fail("Failed to initialize WebDriver: " . $e->getMessage());
        }
    }

    protected function tearDown(): void
    {
        try {
            if ($this->driver && $this->sessionId) {
                $this->driver->quit();
            }
        } catch (WebDriverException $e) {
            // Log the error but don't fail the test
            error_log("Error during WebDriver cleanup: " . $e->getMessage());
        }
    }

    protected function login($email, $password)
    {
        try {
            $this->driver->get($this->baseUrl . 'login.php');
            
            // Wait for the login form to be present
            $this->driver->wait(10)->until(
                WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::id('inputEmail'))
            );
            
            // Fill in the login form
            $this->driver->findElement(WebDriverBy::id('inputEmail'))->clear()->sendKeys($email);
            $this->driver->findElement(WebDriverBy::id('inputPassword'))->clear()->sendKeys($password);
            
            // Click the login button
            $this->driver->findElement(WebDriverBy::cssSelector('button[name="login"]'))->click();
            
            // Wait for redirect to profile page
            $this->driver->wait(10)->until(
                WebDriverExpectedCondition::urlContains('profile.php')
            );

            // Additional wait to ensure the page is fully loaded
            $this->driver->wait(10)->until(
                WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::tagName('body'))
            );
        } catch (TimeoutException $e) {
            $this->fail("Login timeout: " . $e->getMessage());
        } catch (WebDriverException $e) {
            $this->fail("Login failed: " . $e->getMessage());
        }
    }

    protected function waitForElement($by, $timeout = 10)
    {
        try {
            return $this->driver->wait($timeout)->until(
                WebDriverExpectedCondition::presenceOfElementLocated($by)
            );
        } catch (TimeoutException $e) {
            $this->fail("Element not found: " . $e->getMessage());
        }
    }

    protected function waitForElementClickable($by, $timeout = 10)
    {
        try {
            return $this->driver->wait($timeout)->until(
                WebDriverExpectedCondition::elementToBeClickable($by)
            );
        } catch (TimeoutException $e) {
            $this->fail("Element not clickable: " . $e->getMessage());
        }
    }
} 