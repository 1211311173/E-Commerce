<?php

namespace Tests\Src;

/**
 * Connection Test Class
 * Tests database connectivity and basic operations
 */
class ConnectionTest extends BaseTest
{
    /**
     * Test database connection
     */
    public function testDatabaseConnection()
    {
        $this->assertNotNull($this->testDatabase);
        $this->assertInstanceOf(\PDO::class, $this->testDatabase);
    }
    
    /**
     * Test database query execution
     */
    public function testDatabaseQuery()
    {
        // Test simple query
        $result = $this->testDatabase->query("SELECT 1 as test");
        $this->assertNotFalse($result);
        
        $row = $result->fetch(\PDO::FETCH_ASSOC);
        $this->assertEquals(1, $row['test']);
    }
    
    /**
     * Test prepared statements
     */
    public function testPreparedStatements()
    {
        // Create test user
        $email = 'connection.test@example.com';
        $userId = $this->createTestUser($email);
        
        // Test prepared statement
        $stmt = $this->testDatabase->prepare("SELECT * FROM customer WHERE customer_id = ?");
        $stmt->execute([$userId]);
        
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);
        $this->assertNotFalse($user);
        $this->assertEquals($email, $user['customer_email']);
    }
    
    /**
     * Test transaction support
     */
    public function testTransactionSupport()
    {
        // Begin transaction
        $this->testDatabase->beginTransaction();
        
        // Insert test data
        $userId = $this->createTestUser('transaction.test@example.com');
        $this->assertNotNull($userId);
        
        // Rollback transaction
        $this->testDatabase->rollback();
        
        // Verify data was rolled back
        $stmt = $this->testDatabase->prepare("SELECT COUNT(*) FROM customer WHERE customer_id = ?");
        $stmt->execute([$userId]);
        $count = $stmt->fetchColumn();
        
        $this->assertEquals(0, $count);
    }
    
    /**
     * Test error handling
     */
    public function testErrorHandling()
    {
        $this->expectException(\PDOException::class);
        
        // Execute invalid query
        $this->testDatabase->query("SELECT * FROM non_existent_table");
    }
}
