# Gazoma Pay — Production-Ready PHP Fintech Platform

Gazoma Pay is a production-oriented fintech payment infrastructure & merchant management platform built with modern PHP 8, MySQL, HTML5, Vanilla CSS, and JavaScript.

---

## Technical Stack & Features

- **PHP 8.0+** clean MVC architecture (Controllers, Models, Services, Middleware, Helpers)
- **MySQL 8+** normalized database schema with prepared PDO queries and transactions
- **Visual Design**: Recreates the visual language, typography, dark navy sidebar, cards, and tables from the Gazoma Pay UI mockup.
- **Sandbox Payment Gateway**: Full simulated payment flow for Card, Mobile Money (MTN, Telecel, AT), Bank Transfer, and Wallet.
- **Public Payment Links (`/pay/{token}`)**: Customer checkout page with real-time payment processing, instant merchant balance update, and receipt generation.
- **Transactions & Refunds**: Filter, search, paginate, export CSV, and issue instant sandbox refunds.
- **Settlements (Payouts)**: Available balance management, automated fee calculations, bank payout requests.
- **Invoices & PDF Generation**: Invoice creation with multi-item line breakdown and PDF download.
- **Subscriptions & Recurring Plans**: Flexible billing intervals (daily, weekly, monthly, yearly) and active subscriber list.
- **Developer Portal & REST API**: API keys (live/test), webhooks endpoint setup, delivery log retries, and REST API endpoints (`/api/v1/payments`, `/api/v1/customers`).
- **Security & Multi-Tenancy**: CSRF protection tokens, password hashing via `password_hash()`, role-based access control (RBAC), multi-merchant data isolation, audit logging.

---

## Directory Overview

```text
gazoma-pay/
├── app/
│   ├── Controllers/   # Page & API logic handlers
│   ├── Models/        # Database models
│   ├── Services/      # Sandbox Payment Gateway, Fee Engine, Webhook Dispatcher, PDF & CSV Exporter
│   ├── Middleware/    # Auth, CSRF, and API Bearer Token Middleware
│   └── Helpers/       # View, Auth, Format, and Response helpers
├── config/            # App and Database singleton configuration
├── database/          # schema.sql and seed.php
├── public/            # Front controller index.php, app.css, app.js, charts.js, logo.svg
├── resources/         # Blade-style PHP views matching mockup layout
└── router.php         # Local PHP dev server router
```

---

## Default Credentials

- **Merchant Owner Login**:
  - **Email**: `admin@gazomapay.com`
  - **Password**: `password123`
- **Platform Super Admin**:
  - **Email**: `superadmin@gazomapay.com`
  - **Password**: `password123`

---

## Getting Started Locally

1. Ensure PHP 8 and MySQL are running on your system (e.g. via XAMPP).
2. Seed Database:
   ```bash
   php database/seed.php
   ```
3. Start the PHP server:
   ```bash
   php -S localhost:8000 router.php
   ```
4. Open your web browser and navigate to:
   [http://localhost:8000](http://localhost:8000)

---

## Testing Sandbox Payments

1. Go to **Payment Links** tab in the dashboard.
2. Click **Open Checkout Page** or open `/pay/PL_1234567890`.
3. Enter customer details (e.g., Ama Serwaa, `ama@example.com`, `0241234567`).
4. Select payment method (Card, Mobile Money, Bank Transfer, or Wallet).
5. Click **Pay GH₵ 6,500.00**.
6. The sandbox gateway will process the transaction, update the database, increase merchant available balance, update payment link analytics, and dispatch webhooks!
