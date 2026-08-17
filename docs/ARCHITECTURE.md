# Gazoma Pay — System Architecture Specification

## Overview & Design Principles

Gazoma Pay is designed as a high-throughput, provider-agnostic, auditable financial payment infrastructure platform.

```text
                 GAZOMA PAY INFRASTRUCTURE
                             │
                  ┌──────────▼──────────┐
                  │ Authentication/RBAC │
                  └──────────┬──────────┘
                             │
                  ┌──────────▼──────────┐
                  │   Developer API     │
                  └──────────┬──────────┘
                             │
                  ┌──────────▼──────────┐
                  │   Payment Service   │
                  └──────────┬──────────┘
                             │
                     Provider Resolver
                             │
       ┌─────────────────────┼─────────────────────┐
       ▼                     ▼                     ▼
 PaystackProvider     HubtelProvider     SandboxProvider
       │                     │                     │
       └─────────────────────┼─────────────────────┘
                             │
                       Webhook Engine
                             │
                     Transaction State
                             │
                      Ledger Engine
                             │
        ┌────────────────────┼────────────────────┐
        ▼                    ▼                    ▼
   Settlements         Reconciliation        Risk Engine
        │                    │                    │
        └────────────────────┼────────────────────┘
                             │
                      Merchant Balance
```

## Layer Responsibilities

1. **API Router & Auth Middleware**: Handles Bearer Auth, Rate Limiting (`RateLimiter.php`), Request ID injection (`RequestId.php`), and Idempotency key evaluation (`IdempotencyService.php`).
2. **Payment Service & Provider Resolver**: `PaymentProviderResolver.php` delegates payment actions to `PaymentProviderInterface` implementations (`PaystackPaymentProvider`, `HubtelPaymentProvider`, `SandboxPaymentProvider`).
3. **Double-Entry Ledger Engine**: Implements immutable debit & credit postings (`LedgerEngine.php`).
4. **Risk & Fraud Engine**: Evaluates velocity, transaction thresholds, and IP reputation before charge authorization (`RiskEngine.php`).
5. **Reconciliation & Settlements**: Automated reconciliation auditor (`ReconciliationService.php`) and payout settlement processor (`process_settlements.php`).
