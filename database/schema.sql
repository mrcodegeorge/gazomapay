-- Gazoma Pay MySQL Database Schema

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `audit_logs`;
DROP TABLE IF EXISTS `notifications`;
DROP TABLE IF EXISTS `webhook_logs`;
DROP TABLE IF EXISTS `webhook_endpoints`;
DROP TABLE IF EXISTS `api_keys`;
DROP TABLE IF EXISTS `refunds`;
DROP TABLE IF EXISTS `subscriptions`;
DROP TABLE IF EXISTS `subscription_plans`;
DROP TABLE IF EXISTS `invoice_items`;
DROP TABLE IF EXISTS `invoices`;
DROP TABLE IF EXISTS `settlements`;
DROP TABLE IF EXISTS `payment_link_views`;
DROP TABLE IF EXISTS `payment_links`;
DROP TABLE IF EXISTS `transaction_items`;
DROP TABLE IF EXISTS `transactions`;
DROP TABLE IF EXISTS `customers`;
DROP TABLE IF EXISTS `merchant_users`;
DROP TABLE IF EXISTS `users`;
DROP TABLE IF EXISTS `merchants`;
DROP TABLE IF EXISTS `settings`;
DROP TABLE IF EXISTS `platform_settings`;

SET FOREIGN_KEY_CHECKS = 1;

-- Merchants Table
CREATE TABLE `merchants` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `uuid` VARCHAR(64) UNIQUE NOT NULL,
  `merchant_id` VARCHAR(32) UNIQUE NOT NULL,
  `name` VARCHAR(191) NOT NULL,
  `email` VARCHAR(191) NOT NULL,
  `phone` VARCHAR(32) NULL,
  `logo` VARCHAR(255) NULL,
  `country` VARCHAR(64) DEFAULT 'Ghana',
  `currency` VARCHAR(8) DEFAULT 'GHS',
  `timezone` VARCHAR(64) DEFAULT 'Africa/Accra',
  `address` TEXT NULL,
  `environment` ENUM('live', 'test') DEFAULT 'live',
  `available_balance` DECIMAL(15,2) DEFAULT 0.00,
  `pending_balance` DECIMAL(15,2) DEFAULT 0.00,
  `settled_balance` DECIMAL(15,2) DEFAULT 0.00,
  `status` ENUM('active', 'suspended', 'pending') DEFAULT 'active',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Users Table
CREATE TABLE `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `merchant_id` INT NOT NULL,
  `uuid` VARCHAR(64) UNIQUE NOT NULL,
  `name` VARCHAR(191) NOT NULL,
  `email` VARCHAR(191) UNIQUE NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `role` ENUM('platform_admin', 'owner', 'admin', 'finance', 'developer', 'support', 'viewer') DEFAULT 'admin',
  `two_factor_enabled` TINYINT(1) DEFAULT 0,
  `status` ENUM('active', 'inactive') DEFAULT 'active',
  `last_login` DATETIME NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`merchant_id`) REFERENCES `merchants`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Customers Table
CREATE TABLE `customers` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `merchant_id` INT NOT NULL,
  `uuid` VARCHAR(64) NOT NULL,
  `name` VARCHAR(191) NOT NULL,
  `email` VARCHAR(191) NOT NULL,
  `phone` VARCHAR(32) NULL,
  `country` VARCHAR(64) DEFAULT 'Ghana',
  `total_transactions` INT DEFAULT 0,
  `total_spending` DECIMAL(15,2) DEFAULT 0.00,
  `successful_payments` INT DEFAULT 0,
  `failed_payments` INT DEFAULT 0,
  `status` ENUM('active', 'inactive') DEFAULT 'active',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`merchant_id`) REFERENCES `merchants`(`id`) ON DELETE CASCADE,
  INDEX `idx_merchant_customer` (`merchant_id`, `email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Payment Links Table
CREATE TABLE `payment_links` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `token` VARCHAR(64) UNIQUE NOT NULL,
  `merchant_id` INT NOT NULL,
  `name` VARCHAR(191) NOT NULL,
  `description` TEXT NULL,
  `amount` DECIMAL(15,2) NOT NULL,
  `currency` VARCHAR(8) DEFAULT 'GHS',
  `usage_count` INT DEFAULT 0,
  `max_uses` INT DEFAULT 0, -- 0 for unlimited
  `redirect_url` VARCHAR(255) NULL,
  `success_message` TEXT NULL,
  `status` ENUM('active', 'inactive', 'expired') DEFAULT 'active',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`merchant_id`) REFERENCES `merchants`(`id`) ON DELETE CASCADE,
  INDEX `idx_token` (`token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Payment Link Views Table
CREATE TABLE `payment_link_views` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `payment_link_id` INT NOT NULL,
  `ip_address` VARCHAR(45) NULL,
  `user_agent` TEXT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`payment_link_id`) REFERENCES `payment_links`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Invoices Table
CREATE TABLE `invoices` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `merchant_id` INT NOT NULL,
  `customer_id` INT NOT NULL,
  `invoice_number` VARCHAR(64) UNIQUE NOT NULL,
  `subtotal` DECIMAL(15,2) DEFAULT 0.00,
  `tax` DECIMAL(15,2) DEFAULT 0.00,
  `discount` DECIMAL(15,2) DEFAULT 0.00,
  `total` DECIMAL(15,2) DEFAULT 0.00,
  `currency` VARCHAR(8) DEFAULT 'GHS',
  `status` ENUM('draft', 'sent', 'viewed', 'partially_paid', 'paid', 'overdue', 'cancelled') DEFAULT 'draft',
  `due_date` DATE NOT NULL,
  `notes` TEXT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`merchant_id`) REFERENCES `merchants`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`customer_id`) REFERENCES `customers`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Invoice Items Table
CREATE TABLE `invoice_items` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `invoice_id` INT NOT NULL,
  `description` VARCHAR(255) NOT NULL,
  `quantity` INT DEFAULT 1,
  `unit_price` DECIMAL(15,2) DEFAULT 0.00,
  `amount` DECIMAL(15,2) DEFAULT 0.00,
  FOREIGN KEY (`invoice_id`) REFERENCES `invoices`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Transactions Table
CREATE TABLE `transactions` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `reference` VARCHAR(64) UNIQUE NOT NULL,
  `merchant_id` INT NOT NULL,
  `customer_id` INT NULL,
  `payment_link_id` INT NULL,
  `invoice_id` INT NULL,
  `amount` DECIMAL(15,2) NOT NULL,
  `fee` DECIMAL(15,2) DEFAULT 0.00,
  `net_amount` DECIMAL(15,2) NOT NULL,
  `currency` VARCHAR(8) DEFAULT 'GHS',
  `payment_method` ENUM('card', 'mobile_money', 'bank_transfer', 'wallet') DEFAULT 'card',
  `provider` VARCHAR(64) DEFAULT 'Sandbox Gateway',
  `status` ENUM('successful', 'pending', 'failed', 'cancelled', 'refunded') DEFAULT 'pending',
  `failure_reason` VARCHAR(255) NULL,
  `ip_address` VARCHAR(45) NULL,
  `metadata` JSON NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`merchant_id`) REFERENCES `merchants`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`customer_id`) REFERENCES `customers`(`id`) ON DELETE SET NULL,
  FOREIGN KEY (`payment_link_id`) REFERENCES `payment_links`(`id`) ON DELETE SET NULL,
  FOREIGN KEY (`invoice_id`) REFERENCES `invoices`(`id`) ON DELETE SET NULL,
  INDEX `idx_merchant_status` (`merchant_id`, `status`),
  INDEX `idx_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Refunds Table
CREATE TABLE `refunds` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `transaction_id` INT NOT NULL,
  `merchant_id` INT NOT NULL,
  `refund_reference` VARCHAR(64) UNIQUE NOT NULL,
  `amount` DECIMAL(15,2) NOT NULL,
  `reason` VARCHAR(255) NULL,
  `status` ENUM('completed', 'pending', 'failed') DEFAULT 'completed',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`transaction_id`) REFERENCES `transactions`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`merchant_id`) REFERENCES `merchants`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Settlements Table
CREATE TABLE `settlements` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `reference` VARCHAR(64) UNIQUE NOT NULL,
  `merchant_id` INT NOT NULL,
  `gross_amount` DECIMAL(15,2) NOT NULL,
  `fee` DECIMAL(15,2) DEFAULT 0.00,
  `net_amount` DECIMAL(15,2) NOT NULL,
  `currency` VARCHAR(8) DEFAULT 'GHS',
  `bank_name` VARCHAR(128) NOT NULL,
  `account_number` VARCHAR(64) NOT NULL,
  `account_name` VARCHAR(128) NOT NULL,
  `status` ENUM('pending', 'processing', 'completed', 'failed') DEFAULT 'pending',
  `processed_at` DATETIME NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`merchant_id`) REFERENCES `merchants`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Subscription Plans Table
CREATE TABLE `subscription_plans` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `merchant_id` INT NOT NULL,
  `name` VARCHAR(191) NOT NULL,
  `description` TEXT NULL,
  `amount` DECIMAL(15,2) NOT NULL,
  `currency` VARCHAR(8) DEFAULT 'GHS',
  `billing_interval` ENUM('daily', 'weekly', 'monthly', 'quarterly', 'yearly') DEFAULT 'monthly',
  `trial_days` INT DEFAULT 0,
  `status` ENUM('active', 'inactive') DEFAULT 'active',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`merchant_id`) REFERENCES `merchants`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Subscriptions Table
CREATE TABLE `subscriptions` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `merchant_id` INT NOT NULL,
  `customer_id` INT NOT NULL,
  `plan_id` INT NOT NULL,
  `status` ENUM('active', 'paused', 'cancelled', 'past_due') DEFAULT 'active',
  `next_billing_date` DATE NOT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`merchant_id`) REFERENCES `merchants`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`customer_id`) REFERENCES `customers`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`plan_id`) REFERENCES `subscription_plans`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- API Keys Table
CREATE TABLE `api_keys` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `merchant_id` INT NOT NULL,
  `name` VARCHAR(191) NOT NULL,
  `key_type` ENUM('test', 'live') DEFAULT 'test',
  `public_key` VARCHAR(128) UNIQUE NOT NULL,
  `secret_key_hash` VARCHAR(255) NOT NULL,
  `secret_key_preview` VARCHAR(32) NOT NULL,
  `status` ENUM('active', 'revoked') DEFAULT 'active',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`merchant_id`) REFERENCES `merchants`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Webhook Endpoints Table
CREATE TABLE `webhook_endpoints` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `merchant_id` INT NOT NULL,
  `url` VARCHAR(255) NOT NULL,
  `secret` VARCHAR(128) NOT NULL,
  `events` JSON NOT NULL,
  `status` ENUM('active', 'disabled') DEFAULT 'active',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`merchant_id`) REFERENCES `merchants`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Webhook Delivery Logs Table
CREATE TABLE `webhook_logs` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `merchant_id` INT NOT NULL,
  `endpoint_id` INT NOT NULL,
  `event_type` VARCHAR(64) NOT NULL,
  `payload` JSON NOT NULL,
  `response_code` INT DEFAULT 0,
  `response_body` TEXT NULL,
  `attempt_count` INT DEFAULT 1,
  `status` ENUM('delivered', 'failed', 'retrying') DEFAULT 'delivered',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`merchant_id`) REFERENCES `merchants`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`endpoint_id`) REFERENCES `webhook_endpoints`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Notifications Table
CREATE TABLE `notifications` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `merchant_id` INT NOT NULL,
  `user_id` INT NULL,
  `title` VARCHAR(191) NOT NULL,
  `message` TEXT NOT NULL,
  `type` ENUM('success', 'info', 'warning', 'danger') DEFAULT 'info',
  `is_read` TINYINT(1) DEFAULT 0,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`merchant_id`) REFERENCES `merchants`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Audit Logs Table
CREATE TABLE `audit_logs` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `merchant_id` INT NOT NULL,
  `user_id` INT NULL,
  `user_email` VARCHAR(191) NULL,
  `action` VARCHAR(128) NOT NULL,
  `ip_address` VARCHAR(45) NULL,
  `user_agent` TEXT NULL,
  `metadata` JSON NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`merchant_id`) REFERENCES `merchants`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Platform Settings Table
CREATE TABLE `platform_settings` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `setting_key` VARCHAR(128) UNIQUE NOT NULL,
  `setting_value` TEXT NOT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
