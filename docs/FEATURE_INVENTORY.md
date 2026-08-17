# Gazoma Pay — Feature Inventory & Verification Matrix

| Feature | Status | Verification Evidence | Operational Details & Remediation |
| :--- | :--- | :--- | :--- |
| **Merchant Registration** | `WORKING` | Form submission, password hash, merchant UUID generation | Verified |
| **Merchant Login & Auth** | `WORKING` | BCRYPT verify, session initialization | Verified |
| **Public Marketing Site** | `WORKING` | Landing page, solutions, pricing, security, contact pages | HTTP 200 OK |
| **Merchant Dashboard Overview** | `WORKING` | Live volume stats, balance metrics, Chart.js analytics | HTTP 200 OK |
| **Transactions Management** | `WORKING` | Filterable status table, CSV export, refund modal | Verified |
| **Customer Accounts** | `WORKING` | Customer directory, total spending tally | Scoped to merchant |
| **Payment Link Engine** | `WORKING` | Smart checkout link generator, QR code generator | Server-side validated |
| **Invoicing & PDF Receipts** | `WORKING` | Itemized invoice creation, PDF generator (`PdfGenerator.php`) | Verified |
| **Subscription Billing** | `WORKING` | Recurring plans (daily, weekly, monthly, yearly), active subscriber status | Verified |
| **Disputes & Chargebacks** | `WORKING` | Dispute resolution portal, evidence submission | Verified |
| **Automated Settlements** | `WORKING` | Payout request workflow, CLI worker (`process_settlements.php`) | Verified |
| **Financial Ledger Engine** | `WORKING` | Immutable double-entry ledger (`LedgerEngine.php`) | `SUM(DEBITS) = SUM(CREDITS)` |
| **Reconciliation Auditor** | `WORKING` | Audit runs, exception detection, `/admin/reconciliation` | Verified |
| **System Health Monitoring** | `WORKING` | `/admin/system-health` and `GET /api/v1/health` | Healthy |
| **Developer API & Console** | `WORKING` | REST API v1, Bearer auth, live in-browser testing console | Verified |
| **HMAC Webhook Security** | `WORKING` | `x-gazoma-signature` verification (`WebhookEngine.php`) | Verified |
| **Idempotency Key Control** | `WORKING` | `Idempotency-Key` header, replay protection, 409 conflict | Verified |
| **Risk & Fraud Engine** | `WORKING` | Pre-auth risk scoring (`RiskEngine.php`), BLOCK / REVIEW | Verified |
| **Stripe-Style Payment Objects** | `UPGRADING` | Upgrading to `payments` & `payment_attempts` tables | Phase 4 & 5 |
