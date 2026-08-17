# Gazoma Pay — Golden Payment Path Test Specification

**Document Purpose:** Define and mandate the authoritative end-to-end payment lifecycle for Gazoma Pay.

---

## The Golden Payment Lifecycle

```text
1.  Merchant Authentication & Authorization
2.  Customer Creation / Lookup (`cust_...`)
3.  Payment Intent Creation (`POST /api/v1/payment-intents`) -> Status: `requires_payment_method` (`pay_...`)
4.  Integer Minor Unit Currency Conversion (e.g. GH₵ 100.50 -> 10050 pesewas)
5.  Checkout Session Initialization (`/checkout?payment_intent=pay_...`)
6.  Customer Payment Method Selection (Card BIN / MoMo Network carrier auto-detection)
7.  Payment Confirmation Request (`POST /api/v1/payment-intents/{id}/confirm`) -> Status: `processing`
8.  Risk & Fraud Pre-Authorization Engine Scoring (`RiskEngine::evaluate`)
9.  Payment Attempt Creation (`payment_attempts` table, `att_...`)
10. Payment Provider Abstraction Resolution (`PaymentProviderResolver::resolve`)
11. Provider Execution & Handshake (`Sandbox`, `Paystack`, or `Hubtel`)
12. Status Transition: `succeeded`, `failed`, or `requires_action` (3DS OTP / MoMo STK Push)
13. Webhook Receipt & HMAC SHA-256 Signature Verification (`hash_equals`)
14. Event Storage (`webhook_events`) & Deduplication Check (`uniq_provider_event`)
15. Duplicate Webhook Rejection (Second duplicate payload rejected with ZERO financial side-effects)
16. Transaction Database Update (`transactions`)
17. Double-Entry Ledger Journal Posting (`LedgerEngine::recordPayment`) -> `SUM(DEBITS) = SUM(CREDITS)`
18. Merchant Available Balance Update (Derived from ledger accounting entries)
19. Internal Payment Event Generation (`payment.succeeded` / `payment.failed`)
20. Outbound Merchant Webhook Delivery (`outbound_webhooks` queue with `X-Gazoma-Signature`)
21. Request Correlation Tracing (`request_id` -> `pay_...` -> `att_...` -> `wh_...` -> `ledger_...`)
22. Merchant Dashboard UI Real-Time Update
```

---

## Strict Rules & Invariants

1. **State Machine Integrity**: Status transitions are strictly validated. Forbidden transitions (e.g. `FAILED` -> `SUCCEEDED` or `CANCELED` -> `SUCCEEDED`) result in `INVALID_STATE_TRANSITION` errors.
2. **Idempotency Protection**: `Idempotency-Key` header with identical request payload returns cached JSON response. Reused key with different payload returns `HTTP 409 IDEMPOTENCY_KEY_REUSED`.
3. **No Fake Success in Live Mode**: When `GAZOMA_MODE=live`, unconfigured providers return `PROVIDER_NOT_CONFIGURED` instead of simulating success.
4. **Merchant Multi-Tenant Isolation**: Server-side verification ensures Merchant A cannot read or mutate Merchant B's payments, customers, or balances (`403 / 404`).
