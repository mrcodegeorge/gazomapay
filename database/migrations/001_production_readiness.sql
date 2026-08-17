-- Gazoma Pay Production Readiness Migration 001

SET FOREIGN_KEY_CHECKS = 0;

-- Idempotency Records Table
CREATE TABLE IF NOT EXISTS `idempotency_records` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `idempotency_key` VARCHAR(191) NOT NULL,
  `merchant_id` INT NOT NULL,
  `endpoint` VARCHAR(191) NOT NULL,
  `request_hash` VARCHAR(64) NOT NULL,
  `response_status` INT NOT NULL DEFAULT 200,
  `response_body` LONGTEXT NOT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `expires_at` DATETIME NOT NULL,
  UNIQUE KEY `uniq_idempotency` (`merchant_id`, `idempotency_key`),
  INDEX `idx_idempotency_exp` (`expires_at`),
  FOREIGN KEY (`merchant_id`) REFERENCES `merchants`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Webhook Events Table
CREATE TABLE IF NOT EXISTS `webhook_events` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `uuid` VARCHAR(64) UNIQUE NOT NULL,
  `provider` VARCHAR(32) NOT NULL,
  `event_id` VARCHAR(191) NOT NULL,
  `event_type` VARCHAR(128) NOT NULL,
  `signature` VARCHAR(255) NULL,
  `payload` LONGTEXT NOT NULL,
  `status` ENUM('received', 'processing', 'processed', 'failed', 'ignored') DEFAULT 'received',
  `retry_count` INT DEFAULT 0,
  `max_retries` INT DEFAULT 5,
  `last_error` TEXT NULL,
  `received_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `processed_at` DATETIME NULL,
  UNIQUE KEY `uniq_provider_event` (`provider`, `event_id`),
  INDEX `idx_webhook_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Transaction Status History Table (State Machine Log)
CREATE TABLE IF NOT EXISTS `transaction_status_history` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `transaction_id` INT NOT NULL,
  `from_state` VARCHAR(32) NULL,
  `to_state` VARCHAR(32) NOT NULL,
  `reason` VARCHAR(255) NULL,
  `performed_by` VARCHAR(191) DEFAULT 'system',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`transaction_id`) REFERENCES `transactions`(`id`) ON DELETE CASCADE,
  INDEX `idx_tx_hist` (`transaction_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Reconciliation Runs Table
CREATE TABLE IF NOT EXISTS `reconciliation_runs` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `run_code` VARCHAR(64) UNIQUE NOT NULL,
  `started_by` VARCHAR(191) DEFAULT 'system',
  `period_start` DATETIME NOT NULL,
  `period_end` DATETIME NOT NULL,
  `total_transactions` INT DEFAULT 0,
  `total_gross_amount` DECIMAL(15,2) DEFAULT 0.00,
  `total_ledger_amount` DECIMAL(15,2) DEFAULT 0.00,
  `discrepancy_count` INT DEFAULT 0,
  `discrepancy_amount` DECIMAL(15,2) DEFAULT 0.00,
  `status` ENUM('running', 'completed', 'exceptions_found', 'failed') DEFAULT 'completed',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Reconciliation Items Table
CREATE TABLE IF NOT EXISTS `reconciliation_items` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `reconciliation_run_id` INT NOT NULL,
  `transaction_id` INT NULL,
  `reference` VARCHAR(64) NOT NULL,
  `discrepancy_type` ENUM('missing_ledger', 'missing_provider', 'amount_mismatch', 'status_mismatch', 'duplicate') NOT NULL,
  `expected_amount` DECIMAL(15,2) DEFAULT 0.00,
  `actual_amount` DECIMAL(15,2) DEFAULT 0.00,
  `details` TEXT NULL,
  `resolution_status` ENUM('unresolved', 'resolved', 'ignored') DEFAULT 'unresolved',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`reconciliation_run_id`) REFERENCES `reconciliation_runs`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Risk Events Table
CREATE TABLE IF NOT EXISTS `risk_events` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `merchant_id` INT NOT NULL,
  `transaction_id` INT NULL,
  `ip_address` VARCHAR(45) NULL,
  `risk_score` INT NOT NULL DEFAULT 0,
  `risk_decision` ENUM('APPROVE', 'REVIEW', 'BLOCK') DEFAULT 'APPROVE',
  `risk_reasons` TEXT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`merchant_id`) REFERENCES `merchants`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- KYC Profiles Table
CREATE TABLE IF NOT EXISTS `kyc_profiles` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `merchant_id` INT UNIQUE NOT NULL,
  `account_type` ENUM('individual', 'business') DEFAULT 'business',
  `tax_identification_number` VARCHAR(64) NULL,
  `identity_document_type` ENUM('ghana_card', 'passport', 'voters_id', 'drivers_license') NULL,
  `identity_document_number` VARCHAR(64) NULL,
  `document_proof_url` VARCHAR(255) NULL,
  `utility_bill_url` VARCHAR(255) NULL,
  `verification_notes` TEXT NULL,
  `submitted_at` DATETIME NULL,
  `reviewed_at` DATETIME NULL,
  `reviewed_by` VARCHAR(191) NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`merchant_id`) REFERENCES `merchants`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
