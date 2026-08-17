-- Gazoma Pay Production Migration 002: Stripe-Style Payment Object Lifecycle

SET FOREIGN_KEY_CHECKS = 0;

-- 1. Payments Table (Stripe-Style Payment Intent object)
CREATE TABLE IF NOT EXISTS `payments` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `public_id` VARCHAR(64) UNIQUE NOT NULL,
  `merchant_id` INT NOT NULL,
  `customer_id` INT NULL,
  `amount` BIGINT NOT NULL,
  `currency` VARCHAR(8) DEFAULT 'GHS',
  `status` ENUM('requires_payment_method', 'requires_confirmation', 'requires_action', 'processing', 'succeeded', 'failed', 'canceled') DEFAULT 'requires_payment_method',
  `payment_method` ENUM('card', 'mobile_money', 'bank_transfer', 'wallet') NULL,
  `provider` VARCHAR(64) DEFAULT 'sandbox',
  `provider_reference` VARCHAR(191) NULL,
  `description` VARCHAR(255) NULL,
  `livemode` TINYINT(1) DEFAULT 0,
  `metadata` JSON NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`merchant_id`) REFERENCES `merchants`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`customer_id`) REFERENCES `customers`(`id`) ON DELETE SET NULL,
  INDEX `idx_payment_pub` (`public_id`),
  INDEX `idx_payment_mch_status` (`merchant_id`, `status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Payment Attempts Table (Tracking multiple attempts per payment)
CREATE TABLE IF NOT EXISTS `payment_attempts` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `public_id` VARCHAR(64) UNIQUE NOT NULL,
  `payment_id` INT NOT NULL,
  `provider` VARCHAR(64) NOT NULL,
  `provider_reference` VARCHAR(191) NULL,
  `payment_method` VARCHAR(32) NOT NULL,
  `status` ENUM('initiated', 'processing', 'succeeded', 'failed', 'requires_action') DEFAULT 'initiated',
  `failure_code` VARCHAR(64) NULL,
  `failure_message` VARCHAR(255) NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`payment_id`) REFERENCES `payments`(`id`) ON DELETE CASCADE,
  INDEX `idx_attempt_pub` (`public_id`),
  INDEX `idx_attempt_pay` (`payment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Payment Methods Table (Saved Customer Payment References)
CREATE TABLE IF NOT EXISTS `payment_methods` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `public_id` VARCHAR(64) UNIQUE NOT NULL,
  `merchant_id` INT NOT NULL,
  `customer_id` INT NOT NULL,
  `type` ENUM('card', 'mobile_money') NOT NULL,
  `provider` VARCHAR(64) NOT NULL,
  `provider_token` VARCHAR(191) NOT NULL,
  `card_brand` VARCHAR(32) NULL,
  `last4` VARCHAR(8) NULL,
  `exp_month` INT NULL,
  `exp_year` INT NULL,
  `momo_phone` VARCHAR(32) NULL,
  `momo_network` VARCHAR(32) NULL,
  `is_default` TINYINT(1) DEFAULT 0,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`merchant_id`) REFERENCES `merchants`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`customer_id`) REFERENCES `customers`(`id`) ON DELETE CASCADE,
  INDEX `idx_pm_pub` (`public_id`),
  INDEX `idx_pm_cust` (`customer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Outbound Webhook Deliveries Table (Merchant Webhook Event Queue)
CREATE TABLE IF NOT EXISTS `outbound_webhooks` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `public_id` VARCHAR(64) UNIQUE NOT NULL,
  `merchant_id` INT NOT NULL,
  `event_type` VARCHAR(128) NOT NULL,
  `payload` LONGTEXT NOT NULL,
  `target_url` VARCHAR(255) NOT NULL,
  `response_status` INT NULL,
  `response_body` TEXT NULL,
  `delivery_status` ENUM('pending', 'delivered', 'failed') DEFAULT 'pending',
  `attempts` INT DEFAULT 0,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`merchant_id`) REFERENCES `merchants`(`id`) ON DELETE CASCADE,
  INDEX `idx_owh_mch` (`merchant_id`),
  INDEX `idx_owh_status` (`delivery_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
