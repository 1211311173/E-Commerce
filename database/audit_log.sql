-- Security Audit Log Table
-- This table stores security-related events for auditing purposes

CREATE TABLE `audit_log` (
  `log_id` int(11) NOT NULL AUTO_INCREMENT,
  `event_timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `user_id` int(11) DEFAULT NULL,
  `ip_address` varchar(45) NOT NULL,
  `event_type` varchar(50) NOT NULL,
  `event_status` enum('SUCCESS','FAILURE') NOT NULL,
  `event_description` text NOT NULL,
  PRIMARY KEY (`log_id`),
  KEY `idx_event_type` (`event_type`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_event_timestamp` (`event_timestamp`),
  CONSTRAINT `fk_audit_customer` FOREIGN KEY (`user_id`) REFERENCES `customer` (`customer_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Sample data for testing (optional)
-- INSERT INTO `audit_log` (`user_id`, `ip_address`, `event_type`, `event_status`, `event_description`) VALUES
-- (1, '192.168.1.100', 'LOGIN', 'SUCCESS', 'User logged in: test@example.com'),
-- (NULL, '192.168.1.101', 'LOGIN', 'FAILURE', 'Failed login attempt for: invalid@example.com - Invalid credentials'),
-- (1, '192.168.1.100', 'ADMIN_LOGIN', 'SUCCESS', 'Admin logged in: admin@example.com'),
-- (1, '192.168.1.100', 'PRODUCT_ADD', 'SUCCESS', 'Product added: Test Product'),
-- (NULL, '192.168.1.102', 'UNAUTHORIZED_ACCESS', 'FAILURE', 'Unauthorized access attempt to: /admin/restricted-page.php');
