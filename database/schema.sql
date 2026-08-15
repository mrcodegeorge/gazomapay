-- Gazoma Pay Production-Grade Hardened MySQL Database Schema

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `idempotency_keys`;
DROP TABLE IF EXISTS `ledger_entries`;
DROP TABLE IF EXISTS `ledger_transactions`;
DROP TABLE IF EXISTS `ledger_accounts`;
DROP TABLE IF EXISTS `audit_logs`;
DROP TABLE IF EXISTS `notifications`;
DROP TABLE IF EXISTS `webhook_logs`;
DROP TABLE IF EXISTS `webhook_endpoints`;
DROP TABLE IF EXISTS `api_keys`;
DROP TABLE IF EXISTS `disputes`;
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

-- Merchants Table with KYB / KYC Hardening
CREATE TABLE `merchants` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `uuid` VARCHAR(64) UNIQUE NOT NULL,
  `merchant_id` VARCHAR(32) UNIQUE NOT NULL,
  `name` VARCHAR(191) NOT NULL,
  `legal_name` VARCHAR(191) NULL,
  `trading_name` VARCHAR(191) NULL,
  `business_registration_number` VARCHAR(64) NULL,
  `business_type` ENUM('sole_proprietorship', 'limited_company', 'partnership', 'registered_charity') DEFAULT 'limited_company',
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
  `custom_fee_percentage` DECIMAL(5,2) NULL,
  `custom_fee_flat` DECIMAL(15,2) NULL,
  `onboarding_completed` TINYINT(1) DEFAULT 0,
  `onboarding_step` INT DEFAULT 1,
  `kyc_status` ENUM('verification_pending', 'under_review', 'approved', 'rejected') DEFAULT 'approved',
  `account_status` ENUM('pending', 'active', 'suspended', 'restricted', 'closed') DEFAULT 'active',
  `status` ENUM('active', 'suspended', 'pending') DEFAULT 'active',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_merchant_code` (`merchant_id`),
  INDEX `idx_mch_status` (`account_status`)
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
  FOREIGN KEY (`merchant_id`) REFERENCES `merchants`(`id`) ON DELETE CASCADE,
  INDEX `idx_user_email` (`email`)
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
  INDEX `idx_merchant_customer` (`merchant_id`, `email`),
  INDEX `idx_cust_uuid` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Ledger Accounts Table
CREATE TABLE `ledger_accounts` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `merchant_id` INT NOT NULL,
  `account_number` VARCHAR(64) UNIQUE NOT NULL,
  `account_type` ENUM('merchant_available', 'merchant_pending', 'platform_fee', 'provider_fee', 'customer_escrow', 'bank_disbursement') NOT NULL,
  `currency` VARCHAR(8) DEFAULT 'GHS',
  `status` ENUM('active', 'frozen') DEFAULT 'active',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`merchant_id`) REFERENCES `merchants`(`id`) ON DELETE CASCADE,
  INDEX `idx_ledger_account_type` (`merchant_id`, `account_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Ledger Transactions Table
CREATE TABLE `ledger_transactions` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `reference` VARCHAR(64) UNIQUE NOT NULL,
  `event_id` VARCHAR(64) UNIQUE NOT NULL,
  `event_type` VARCHAR(64) NOT NULL,
  `merchant_id` INT NOT NULL,
  `description` VARCHAR(255) NOT NULL,
  `status` ENUM('posted', 'pending', 'reversed') DEFAULT 'posted',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`merchant_id`) REFERENCES `merchants`(`id`) ON DELETE CASCADE,
  INDEX `idx_ledger_tx_ref` (`reference`),
  INDEX `idx_ledger_evt` (`event_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Ledger Entries Table (Double-Entry Financial Accounting)
CREATE TABLE `ledger_entries` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `ledger_transaction_id` INT NOT NULL,
  `account_id` INT NOT NULL,
  `debit_amount` DECIMAL(15,2) DEFAULT 0.00,
  `credit_amount` DECIMAL(15,2) DEFAULT 0.00,
  `currency` VARCHAR(8) DEFAULT 'GHS',
  `balance_after` DECIMAL(15,2) NOT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`ledger_transaction_id`) REFERENCES `ledger_transactions`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`account_id`) REFERENCES `ledger_accounts`(`id`) ON DELETE CASCADE,
  INDEX `idx_entry_account` (`account_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Idempotency Keys Table
CREATE TABLE `idempotency_keys` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `merchant_id` INT NOT NULL,
  `idempotency_key` VARCHAR(128) NOT NULL,
  `request_path` VARCHAR(255) NOT NULL,
  `request_hash` VARCHAR(64) NOT NULL,
  `response_code` INT NOT NULL,
  `response_body` JSON NOT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `expires_at` DATETIME NOT NULL,
  FOREIGN KEY (`merchant_id`) REFERENCES `merchants`(`id`) ON DELETE CASCADE,
  UNIQUE KEY `uk_merchant_key` (`merchant_id`, `idempotency_key`),
  INDEX `idx_expires` (`expires_at`)
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

-- Payment Links Table
CREATE TABLE `payment_links` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `token` VARCHAR(64) UNIQUE NOT NULL,
  `merchant_id` INT NOT NULL,
  `subscription_plan_id` INT NULL,
  `link_type` ENUM('one_time', 'recurring_subscription') DEFAULT 'one_time',
  `name` VARCHAR(191) NOT NULL,
  `description` TEXT NULL,
  `amount` DECIMAL(15,2) NOT NULL,
  `currency` VARCHAR(8) DEFAULT 'GHS',
  `usage_count` INT DEFAULT 0,
  `max_uses` INT DEFAULT 0,
  `redirect_url` VARCHAR(255) NULL,
  `success_message` TEXT NULL,
  `status` ENUM('active', 'inactive', 'expired') DEFAULT 'active',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`merchant_id`) REFERENCES `merchants`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`subscription_plan_id`) REFERENCES `subscription_plans`(`id`) ON DELETE SET NULL,
  INDEX `idx_token` (`token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Payment Link Views Table
CREATE TABLE `payment_link_views` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `payment_link_id` INT NOT NULL,
  `ip_address` VARCHAR(45) NULL,
  `user_agent` TEXT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`payment_link_id`) REFERENCES `payment_links`(`id`) ON DELETE CASCADE,
  INDEX `idx_pl_views` (`payment_link_id`)
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
  FOREIGN KEY (`customer_id`) REFERENCES `customers`(`id`) ON DELETE CASCADE,
  INDEX `idx_inv_num` (`invoice_number`)
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
  `event_id` VARCHAR(64) UNIQUE NOT NULL,
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
  INDEX `idx_tx_ref` (`reference`),
  INDEX `idx_tx_event` (`event_id`),
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
  FOREIGN KEY (`merchant_id`) REFERENCES `merchants`(`id`) ON DELETE CASCADE,
  INDEX `idx_refund_ref` (`refund_reference`)
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
  FOREIGN KEY (`merchant_id`) REFERENCES `merchants`(`id`) ON DELETE CASCADE,
  INDEX `idx_settle_ref` (`reference`)
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
  `last_used_at` DATETIME NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`merchant_id`) REFERENCES `merchants`(`id`) ON DELETE CASCADE,
  INDEX `idx_api_pub` (`public_key`)
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
  `event_id` VARCHAR(64) NOT NULL,
  `event_type` VARCHAR(64) NOT NULL,
  `payload` JSON NOT NULL,
  `signature` VARCHAR(128) NULL,
  `response_code` INT DEFAULT 0,
  `response_body` TEXT NULL,
  `attempt_count` INT DEFAULT 1,
  `next_retry_at` DATETIME NULL,
  `status` ENUM('delivered', 'failed', 'retrying') DEFAULT 'delivered',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`merchant_id`) REFERENCES `merchants`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`endpoint_id`) REFERENCES `webhook_endpoints`(`id`) ON DELETE CASCADE,
  INDEX `idx_wh_event` (`event_id`)
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
  FOREIGN KEY (`merchant_id`) REFERENCES `merchants`(`id`) ON DELETE CASCADE,
  INDEX `idx_audit_mch` (`merchant_id`, `created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Platform Settings Table
CREATE TABLE `platform_settings` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `setting_key` VARCHAR(128) UNIQUE NOT NULL,
  `setting_value` TEXT NOT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Disputes Table
CREATE TABLE `disputes` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `dispute_code` VARCHAR(64) UNIQUE NOT NULL,
  `merchant_id` INT NOT NULL,
  `transaction_id` INT NOT NULL,
  `customer_id` INT NULL,
  `amount` DECIMAL(15,2) NOT NULL,
  `currency` VARCHAR(8) DEFAULT 'GHS',
  `reason` ENUM('unauthorized_charge', 'fraudulent', 'product_not_received', 'duplicate_charge', 'subscription_cancelled', 'other') DEFAULT 'unauthorized_charge',
  `evidence_text` TEXT NULL,
  `evidence_file` VARCHAR(255) NULL,
  `status` ENUM('needs_response', 'under_review', 'won', 'lost', 'accepted') DEFAULT 'needs_response',
  `due_date` DATE NOT NULL,
  `resolved_at` DATETIME NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`merchant_id`) REFERENCES `merchants`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`transaction_id`) REFERENCES `transactions`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`customer_id`) REFERENCES `customers`(`id`) ON DELETE SET NULL,
  INDEX `idx_dispute_status` (`merchant_id`, `status`),
  INDEX `idx_dispute_code` (`dispute_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
