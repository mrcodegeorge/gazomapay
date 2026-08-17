# Gazoma Pay — Production-Ready Fintech Infrastructure & Merchant Platform

Gazoma Pay is a production-ready, full-stack PHP 8 fintech web application and payment infrastructure platform designed for merchant management, payment link checkout, automated bank payouts, double-entry financial ledger accounting, and developer API integrations.

---

## Technical Stack & Architecture

- **Backend**: PHP 8.0+ (MVC Architecture)
- **Database**: MySQL 8.0+ (Normalized relational schema with immutable financial double-entry ledger)
- **Frontend**: HTML5, Vanilla CSS (`app.css` design system based on mockup visual language), Vanilla JS, Chart.js
- **Payment Engine**: `SandboxPaymentGateway` with transaction state machine, 1.5% + GH₵0.50 fee calculations, and atomic row locking (`SELECT ... FOR UPDATE`)
- **Security & Integrity**:
  - Immutable Double-Entry Ledger Engine (`LedgerEngine.php`)
  - Client API Idempotency (`Idempotency-Key` header)
  - Signed HMAC SHA256 Webhooks (`X-Gazoma-Signature`)
  - Rate Limiting (`RateLimiter.php`)
  - Prepared PDO SQL statements & XSS escaping
  - Password hashing via `password_hash()` (BCRYPT)

---

## Product Screenshots & User Interface

### 1. Merchant Financial Overview Dashboard
![Merchant Financial Overview Dashboard](docs/images/financial_overview_dashboard.png)

### 2. Multi-Channel Payment Link Checkout (Mobile Money & Cards)
![Payment Link Checkout Interface](docs/images/checkout_checkout.png)

### 3. Real-Time Transaction Management & Ledger Logs
![Real-Time Transactions Dashboard](docs/images/transactions_dashboard.png)

### 4. Merchant Customer Accounts & Spending History
![Merchant Customer Accounts](docs/images/customers_dashboard.png)

---

## Directory Structure

```text
gazoma-pay/
├── app/
│   ├── Controllers/          # Auth, Dashboard, Transactions, Customers, PaymentLinks, Pay, Invoices, Subscriptions, Analytics, Developer, Settings, Admin, Api, PublicWeb
│   ├── Services/             # LedgerEngine, IdempotencyService, SandboxPaymentGateway, FeeEngine, WebhookDispatcher, ReconciliationService, AuditLogger, PdfGenerator, CsvExporter
│   ├── Middleware/           # AuthMiddleware, CsrfMiddleware, ApiAuthMiddleware, RateLimiter
│   └── Helpers/              # Auth, View, Response, Format, RateLimiter
├── config/                   # app.php, database.php
├── database/
│   ├── schema.sql            # MySQL Database Schema with 18 normalized tables
│   └── seed.php              # Hardened seeder for merchants, ledger entries & transactions
├── public/
│   ├── index.php             # Front Controller router for web, dashboard & REST API v1
│   └── assets/               # Custom stylesheets, JavaScript, Chart.js & SVGs
├── resources/
│   └── views/                # Dashboard, Web marketing, Checkout, Invoices, Subscriptions, Admin views
├── tests/
│   └── run.php               # Automated Unit, Integration & Reconciliation Test Runner
├── router.php                # Static asset server & router for PHP CLI development
└── README.md
```

---

## Quickstart & Local Setup

### 1. Requirements
- PHP 8.0 or higher (with PDO MySQL and cURL extensions enabled)
- MySQL Daemon running on `127.0.0.1:3306` (Default user: `root`, no password)

### 2. Database Initialization
Run the database migration and seeder script:
```bash
php database/seed.php
```
This automatically creates the `gazoma_pay` MySQL database, applies `schema.sql`, populates initial merchant data ("Gazoma Tech", Merchant ID `GZM_123456`), and posts initial double-entry ledger entries.

### 3. Launch Development Server
```bash
php -S localhost:8000 router.php
```

### 4. Open in Browser
- **Public Marketing Site**: [http://localhost:8000/](http://localhost:8000/)
- **Merchant Dashboard**: [http://localhost:8000/dashboard](http://localhost:8000/dashboard)

**Default Credentials**:
- **Email**: `admin@gazomapay.com`
- **Password**: `password123`

---

## Financial Ledger Engine & Accounting

Gazoma Pay features an **Immutable Double-Entry Financial Ledger** (`LedgerEngine.php`). Financial events are recorded as immutable debit/credit entries:

- **Customer Payment**:
  - Debit `customer_escrow`: Gross Amount (e.g. GH₵ 1,000.00)
  - Credit `merchant_available`: Net Amount (e.g. GH₵ 984.50)
  - Credit `platform_fee`: Fee Amount (e.g. GH₵ 15.50)
- **Refund Reversal**:
  - Debits `merchant_available` & `platform_fee`
  - Credits `customer_escrow`
- **Settlement Payout**:
  - Debits `merchant_available` & Credits `merchant_pending` on request
  - Debits `merchant_pending` & Credits `bank_disbursement` on completion

Balances are calculated directly from double-entry ledger postings, guaranteeing financial reconciliation integrity.

---

## API & Idempotency Key Guide

All payment POST requests support the `Idempotency-Key` HTTP header. Replayed requests return cached JSON responses to prevent duplicate charges during network timeouts.

### Example Charge Request (cURL)
```bash
curl -X POST http://localhost:8000/api/v1/payments \
  -H "Authorization: Bearer gzm_live_pub_9a8b7c6d5e4f3a2b" \
  -H "Idempotency-Key: 7b8c9d0e-1f2a-3b4c-5d6e-7f8a9b0c1d2e" \
  -H "Content-Type: application/json" \
  -d '{
    "amount": 250.00,
    "currency": "GHS",
    "customer_name": "Kofi Mensah",
    "customer_email": "kofi@example.com",
    "payment_method": "mobile_money"
  }'
```

---

## Automated Test Suite

Execute the automated test suite covering Unit tests, Integration tests, and Financial Reconciliation Audits:
```bash
php tests/run.php
```

### Test Coverage:
- `FeeEngine`: 1.5% + GH₵0.50 formula accuracy
- `LedgerEngine`: Double-entry posting & balance calculation
- `IdempotencyService`: Replay protection & cached payload return
- `SandboxPaymentGateway`: Charge and refund execution
- `ReconciliationService`: Database vs Ledger balance verification (`PASS`)

---

## Security Controls

- **Multi-Tenant Isolation**: All database queries are strictly scoped to the authenticated merchant ID (`WHERE merchant_id = ?`).
- **CSRF Tokens**: Form submissions require valid `csrf_token` headers.
- **Signed HMAC Webhooks**: Webhook delivery payloads include `X-Gazoma-Signature: t={timestamp},v1={hash}` generated via `hash_hmac('sha256', ...)`.
- **IP Rate Limiting**: Endpoint login & charge attempts are capped at 5 attempts / minute per IP.

---

## Fintech Disclaimer

This software is a technical sandbox/beta platform demonstration. Do NOT process real customer funds or represent Gazoma Pay as a licensed payment service provider without independent legal, regulatory, and payment provider partnerships. Keep gateway operations in sandbox mode.
