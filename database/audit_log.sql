-- Create audit_log table for security logging
-- This table will store all security-related events

CREATE TABLE IF NOT EXISTS `audit_log` (
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
  KEY `idx_event_timestamp` (`event_timestamp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Add foreign key constraint to link with customer table
ALTER TABLE `audit_log` 
ADD CONSTRAINT `fk_audit_customer` 
FOREIGN KEY (`user_id`) REFERENCES `customer` (`customer_id`) 
ON DELETE SET NULL ON UPDATE CASCADE;
