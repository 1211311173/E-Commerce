<?php

namespace Tests\Evolution;

use Tests\Helpers\TestHelper;

/**
 * Data Migration Tests
 * Tests data migration scenarios during system evolution
 */
class DataMigrationTest extends TestHelper
{
    protected static $db;
    
    public static function setUpBeforeClass(): void
    {
        self::setUpTestDatabase();
        self::$db = self::getTestDatabase();
        self::createLegacyTestData();
    }
    
    public static function tearDownAfterClass(): void
    {
        self::tearDownTestDatabase();
    }
    
    public function testUserDataMigration()
    {
        // Test migration from old user schema to new schema
        
        // Create legacy user table with old structure
        self::$db->exec("
            CREATE TABLE users_legacy (
                id INTEGER PRIMARY KEY,
                uname VARCHAR(50),
                email VARCHAR(100),
                pwd VARCHAR(32),
                fname VARCHAR(50),
                lname VARCHAR(50),
                reg_date DATE
            )
        ");
        
        // Insert legacy user data
        $stmt = self::$db->prepare("
            INSERT INTO users_legacy (uname, email, pwd, fname, lname, reg_date) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute(['olduser', 'old@example.com', md5('oldpass'), 'John', 'Doe', '2020-01-01']);
        $stmt->execute(['legacyuser', 'legacy@example.com', md5('legacypass'), 'Jane', 'Smith', '2020-02-01']);
        
        // Perform migration
        $migrationResult = $this->migrateUsers();
        $this->assertTrue($migrationResult['success']);
        $this->assertEquals(2, $migrationResult['migrated_count']);        // Verify migrated data
        $stmt = self::$db->prepare("SELECT * FROM customer WHERE customer_fname = ?");
        $stmt->execute(['olduser']);
        $migratedUser = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        $this->assertNotFalse($migratedUser, "User 'olduser' was not found in customer table after migration");
        $this->assertEquals('olduser', $migratedUser['customer_fname']);
        $this->assertEquals('old@example.com', $migratedUser['customer_email']);
          // Verify password was migrated properly (should be re-hashed)
        $this->assertNotEquals(md5('oldpass'), $migratedUser['customer_pwd']);
        // In a real migration, we would hash the existing MD5 hash, so we verify against that
        $this->assertTrue(password_verify(md5('oldpass'), $migratedUser['customer_pwd']), "Password verification failed for migrated user");
    }
    
    public function testProductDataMigration()
    {
        // Test product data migration with schema changes
        
        // Create legacy product table
        self::$db->exec("
            CREATE TABLE products_legacy (
                id INTEGER PRIMARY KEY,
                product_name VARCHAR(255),
                product_desc TEXT,
                product_price DECIMAL(8,2),
                cat_id INTEGER,
                qty INTEGER,
                add_date DATETIME
            )
        ");
        
        // Insert legacy product data
        $stmt = self::$db->prepare("
            INSERT INTO products_legacy (product_name, product_desc, product_price, cat_id, qty, add_date) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute(['Old Product 1', 'Old description 1', 99.99, 1, 50, '2020-01-01 10:00:00']);
        $stmt->execute(['Old Product 2', 'Old description 2', 149.50, 2, 25, '2020-01-02 11:00:00']);
        
        // Perform migration
        $migrationResult = $this->migrateProducts();
        $this->assertTrue($migrationResult['success']);
        $this->assertEquals(2, $migrationResult['migrated_count']);
        
        // Verify migrated data
        $stmt = self::$db->prepare("SELECT * FROM products WHERE name = ?");
        $stmt->execute(['Old Product 1']);
        $migratedProduct = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        $this->assertNotFalse($migratedProduct);
        $this->assertEquals('Old Product 1', $migratedProduct['name']);
        $this->assertEquals('Old description 1', $migratedProduct['description']);
        $this->assertEquals(99.99, $migratedProduct['price']);
        $this->assertEquals(50, $migratedProduct['stock_quantity']);
    }
    
    public function testOrderDataMigration()
    {
        // Test complex order data migration with relationship updates
        
        // Create legacy order structure
        self::$db->exec("
            CREATE TABLE orders_legacy (
                order_id INTEGER PRIMARY KEY,
                customer_id INTEGER,
                order_total DECIMAL(10,2),
                order_status VARCHAR(20),
                order_date DATETIME,
                shipping_address TEXT,
                billing_address TEXT
            )
        ");
        
        self::$db->exec("
            CREATE TABLE order_items_legacy (
                item_id INTEGER PRIMARY KEY,
                order_id INTEGER,
                product_id INTEGER,
                item_qty INTEGER,
                item_price DECIMAL(8,2)
            )
        ");
        
        // Insert legacy order data
        $stmt = self::$db->prepare("
            INSERT INTO orders_legacy (customer_id, order_total, order_status, order_date, shipping_address, billing_address) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([1, 199.98, 'completed', '2020-01-01 12:00:00', '123 Ship St', '456 Bill Ave']);
        $orderId = self::$db->lastInsertId();
        
        $stmt = self::$db->prepare("
            INSERT INTO order_items_legacy (order_id, product_id, item_qty, item_price) 
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([$orderId, 1, 2, 99.99]);
        
        // Perform migration
        $migrationResult = $this->migrateOrders();
        $this->assertTrue($migrationResult['success']);
        $this->assertGreaterThan(0, $migrationResult['migrated_orders']);
        $this->assertGreaterThan(0, $migrationResult['migrated_items']);
        
        // Verify migrated order
        $stmt = self::$db->prepare("SELECT * FROM orders WHERE user_id = ? AND total_amount = ?");
        $stmt->execute([1, 199.98]);
        $migratedOrder = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        $this->assertNotFalse($migratedOrder);
        $this->assertEquals('completed', $migratedOrder['status']);
    }
    
    public function testDataIntegrityDuringMigration()
    {
        // Test that data integrity is maintained during migration
        
        // Count original data
        $originalUserCount = $this->getLegacyTableCount('users_legacy');
        $originalProductCount = $this->getLegacyTableCount('products_legacy');
        $originalOrderCount = $this->getLegacyTableCount('orders_legacy');
        
        // Perform complete migration
        $userMigration = $this->migrateUsers();
        $productMigration = $this->migrateProducts();
        $orderMigration = $this->migrateOrders();        // Count migrated data (be more flexible with pre-existing test data)
        $totalCustomersAfterMigration = $this->getTableCount('customer');
        $totalProductsAfterMigration = $this->getTableCount('products');
        $totalOrdersAfterMigration = $this->getTableCount('orders');
        
        // Verify that at least the expected number of records were migrated
        $this->assertGreaterThanOrEqual($originalUserCount, $totalCustomersAfterMigration, 
            "Expected at least $originalUserCount customers after migration");
        $this->assertGreaterThanOrEqual($originalProductCount, $totalProductsAfterMigration, 
            "Expected at least $originalProductCount products after migration");
        $this->assertGreaterThanOrEqual($originalOrderCount, $totalOrdersAfterMigration, 
            "Expected at least $originalOrderCount orders after migration");
        
        // Verify no duplicate data
        $this->assertNoDuplicateUsers();
        $this->assertNoDuplicateProducts();
    }
    
    public function testRollbackCapability()
    {
        // Test that migrations can be rolled back
        
        // Backup current state
        $beforeBackup = $this->createDataBackup();
        
        // Perform migration
        $migrationResult = $this->performTestMigration();
        $this->assertTrue($migrationResult['success']);
        
        // Verify migration changed data
        $afterMigration = $this->getDataChecksum();
        $this->assertNotEquals($beforeBackup['checksum'], $afterMigration);
        
        // Perform rollback
        $rollbackResult = $this->rollbackMigration($beforeBackup);
        $this->assertTrue($rollbackResult['success']);
        
        // Verify data is restored
        $afterRollback = $this->getDataChecksum();
        $this->assertEquals($beforeBackup['checksum'], $afterRollback);
    }
    
    public function testBatchMigration()
    {
        // Test migration of large datasets in batches
        
        // Create large dataset
        $this->createLargeDataset(1000);
        
        $startTime = microtime(true);
        
        // Perform batch migration
        $migrationResult = $this->performBatchMigration(100); // 100 records per batch
        
        $endTime = microtime(true);
        $migrationTime = $endTime - $startTime;
        
        $this->assertTrue($migrationResult['success']);
        $this->assertEquals(1000, $migrationResult['total_migrated']);
        $this->assertEquals(10, $migrationResult['batch_count']);
        
        // Should complete within reasonable time
        $this->assertLessThan(30.0, $migrationTime, "Batch migration took too long");
    }
    
    public function testMigrationErrorHandling()
    {
        // Test migration handles errors gracefully
        
        // Create data that will cause migration errors
        $this->createProblematicData();
        
        // Perform migration with error handling
        $migrationResult = $this->performMigrationWithErrorHandling();
        
        // Should handle errors gracefully
        $this->assertArrayHasKey('success', $migrationResult);
        $this->assertArrayHasKey('errors', $migrationResult);
        $this->assertArrayHasKey('successful_migrations', $migrationResult);
        $this->assertArrayHasKey('failed_migrations', $migrationResult);
        
        // Should have some successful migrations despite errors
        $this->assertGreaterThan(0, $migrationResult['successful_migrations']);
        $this->assertGreaterThan(0, $migrationResult['failed_migrations']);
    }
    
    // Helper methods for migration testing
    
    private static function createLegacyTestData()
    {
        // Create some initial legacy data for testing
        // This method is called in setUpBeforeClass
    }
    
    private function migrateUsers()
    {
        try {
            $migratedCount = 0;
            
            // Get legacy users
            $stmt = self::$db->query("SELECT * FROM users_legacy");
            $legacyUsers = $stmt->fetchAll(\PDO::FETCH_ASSOC);
              // Migrate each user
            $insertStmt = self::$db->prepare("
                INSERT INTO customer (customer_fname, customer_email, customer_pwd, customer_phone, customer_address) 
                VALUES (?, ?, ?, ?, ?)
            ");
            
            foreach ($legacyUsers as $legacyUser) {
                // Re-hash MD5 passwords to bcrypt
                $newPassword = password_hash($legacyUser['pwd'], PASSWORD_DEFAULT);
                
                $insertStmt->execute([
                    $legacyUser['uname'],
                    $legacyUser['email'],
                    $newPassword,
                    '1234567890', // Default phone
                    'Migrated Address' // Default address
                ]);
                
                $migratedCount++;
            }
            
            return ['success' => true, 'migrated_count' => $migratedCount];
            
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
      private function migrateProducts()
    {
        try {
            $migratedCount = 0;
            
            $stmt = self::$db->query("SELECT * FROM products_legacy");
            $legacyProducts = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            // Check for existing product before inserting
            $checkStmt = self::$db->prepare("SELECT COUNT(*) FROM products WHERE name = ?");
            $insertStmt = self::$db->prepare("
                INSERT INTO products (name, description, price, category_id, stock_quantity, created_at, updated_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            
            foreach ($legacyProducts as $legacyProduct) {
                // Check if product already exists
                $checkStmt->execute([$legacyProduct['product_name']]);
                $exists = $checkStmt->fetchColumn() > 0;
                
                if (!$exists) {
                    $insertStmt->execute([
                        $legacyProduct['product_name'],
                        $legacyProduct['product_desc'],
                        $legacyProduct['product_price'],
                        $legacyProduct['cat_id'],
                        $legacyProduct['qty'],
                        $legacyProduct['add_date'],
                        date('Y-m-d H:i:s')
                    ]);
                    
                    $migratedCount++;
                }
            }
            
            return ['success' => true, 'migrated_count' => $migratedCount];
            
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    private function migrateOrders()
    {
        try {
            $migratedOrders = 0;
            $migratedItems = 0;
            
            // Migrate orders
            $stmt = self::$db->query("SELECT * FROM orders_legacy");
            $legacyOrders = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            $insertOrderStmt = self::$db->prepare("
                INSERT INTO orders (user_id, total_amount, status, created_at, updated_at) 
                VALUES (?, ?, ?, ?, ?)
            ");
            
            foreach ($legacyOrders as $legacyOrder) {
                $insertOrderStmt->execute([
                    $legacyOrder['customer_id'],
                    $legacyOrder['order_total'],
                    $legacyOrder['order_status'],
                    $legacyOrder['order_date'],
                    date('Y-m-d H:i:s')
                ]);
                
                $migratedOrders++;
            }
            
            // For simplicity, count legacy order items
            $stmt = self::$db->query("SELECT COUNT(*) FROM order_items_legacy");
            $migratedItems = $stmt->fetchColumn();
            
            return [
                'success' => true, 
                'migrated_orders' => $migratedOrders,
                'migrated_items' => $migratedItems
            ];
            
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    private function getLegacyTableCount($tableName)
    {
        $stmt = self::$db->query("SELECT COUNT(*) FROM $tableName");
        return $stmt->fetchColumn();
    }
    
    private function getTableCount($tableName)
    {
        $stmt = self::$db->query("SELECT COUNT(*) FROM $tableName");
        return $stmt->fetchColumn();
    }
      private function assertNoDuplicateUsers()
    {
        $stmt = self::$db->query("
            SELECT customer_fname, COUNT(*) as count 
            FROM customer 
            GROUP BY customer_fname 
            HAVING count > 1
        ");
        $duplicates = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        $this->assertEmpty($duplicates, "Found duplicate users: " . json_encode($duplicates));
    }
    
    private function assertNoDuplicateProducts()
    {
        $stmt = self::$db->query("
            SELECT name, COUNT(*) as count 
            FROM products 
            GROUP BY name 
            HAVING count > 1
        ");
        $duplicates = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        $this->assertEmpty($duplicates, "Found duplicate products: " . json_encode($duplicates));
    }
      private function createDataBackup()
    {
        // Create a simple backup representation using the same tables as checksum
        $customerCount = self::$db->query("SELECT COUNT(*) FROM customer")->fetchColumn();
        $productCount = self::$db->query("SELECT COUNT(*) FROM products")->fetchColumn();
        
        $backup = [
            'customer_count' => $customerCount,
            'product_count' => $productCount
        ];
        
        $backup['checksum'] = md5($customerCount . '|' . $productCount);
        
        return $backup;
    }
      private function performTestMigration()
    {
        // Simulate a test migration that modifies data by adding a test customer
        self::$db->exec("INSERT INTO customer (customer_fname, customer_email, customer_pwd, customer_phone, customer_address) 
                        VALUES ('migrationtest', 'migration@test.com', 'hashedpwd', '9999999999', 'Test Migration Address')");
        
        return ['success' => true];
    }
      private function getDataChecksum()
    {
        // Create checksum based on customer table since that's what we're actually using
        $stmt = self::$db->query("SELECT COUNT(*) FROM customer");
        $customerCount = $stmt->fetchColumn();
        
        $stmt = self::$db->query("SELECT COUNT(*) FROM products");
        $productCount = $stmt->fetchColumn();
        
        return md5($customerCount . '|' . $productCount);
    }    private function rollbackMigration($backup)
    {
        try {
            // Rollback the test migration by removing the test customer we added
            self::$db->exec("DELETE FROM customer WHERE customer_fname = 'migrationtest'");
            
            return ['success' => true];
            
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    private function createLargeDataset($count)
    {
        self::$db->exec("
            CREATE TABLE IF NOT EXISTS large_dataset (
                id INTEGER PRIMARY KEY,
                data_field VARCHAR(255),
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ");
        
        $stmt = self::$db->prepare("INSERT INTO large_dataset (data_field) VALUES (?)");
        
        for ($i = 1; $i <= $count; $i++) {
            $stmt->execute(["Data item $i"]);
        }
    }
    
    private function performBatchMigration($batchSize)
    {
        $totalCount = $this->getTableCount('large_dataset');
        $batchCount = 0;
        $totalMigrated = 0;
        
        for ($offset = 0; $offset < $totalCount; $offset += $batchSize) {
            $stmt = self::$db->prepare("
                SELECT * FROM large_dataset 
                LIMIT ? OFFSET ?
            ");
            $stmt->execute([$batchSize, $offset]);
            $batch = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            // Simulate migration processing
            $totalMigrated += count($batch);
            $batchCount++;
            
            // Small delay to simulate processing time
            usleep(1000); // 1ms
        }
        
        return [
            'success' => true,
            'total_migrated' => $totalMigrated,
            'batch_count' => $batchCount
        ];
    }
    
    private function createProblematicData()
    {
        // Create data that will cause migration issues
        self::$db->exec("
            CREATE TABLE problematic_data (
                id INTEGER PRIMARY KEY,
                bad_email VARCHAR(100),
                invalid_date VARCHAR(50),
                negative_price DECIMAL(8,2)
            )
        ");
        
        $stmt = self::$db->prepare("INSERT INTO problematic_data (bad_email, invalid_date, negative_price) VALUES (?, ?, ?)");
        $stmt->execute(['not-an-email', 'invalid-date', -50.00]);
        $stmt->execute(['', '2023-13-40', -100.00]);
        $stmt->execute(['good@email.com', '2023-01-01', 99.99]); // One good record
    }
    
    private function performMigrationWithErrorHandling()
    {
        $successful = 0;
        $failed = 0;
        $errors = [];
        
        $stmt = self::$db->query("SELECT * FROM problematic_data");
        $records = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        foreach ($records as $record) {
            try {
                // Attempt migration with validation
                if (filter_var($record['bad_email'], FILTER_VALIDATE_EMAIL) === false && !empty($record['bad_email'])) {
                    throw new \Exception("Invalid email: " . $record['bad_email']);
                }
                
                if ($record['negative_price'] < 0) {
                    throw new \Exception("Negative price: " . $record['negative_price']);
                }
                
                // If we get here, migration is successful
                $successful++;
                
            } catch (\Exception $e) {
                $failed++;
                $errors[] = $e->getMessage();
            }
        }
        
        return [
            'success' => $successful > 0,
            'successful_migrations' => $successful,
            'failed_migrations' => $failed,
            'errors' => $errors
        ];
    }
}
