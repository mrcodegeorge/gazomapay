# Gazoma Pay — Immutable Double-Entry Financial Ledger

## Core Principles

1. **Zero Fake Balances**: Merchant balances are calculated directly from double-entry postings.
2. **Immutable Entries**: Ledger journal entries cannot be deleted or updated. Reversals occur via compensating entries.
3. **Balanced Postings Constraint**: `SUM(DEBITS) = SUM(CREDITS)` for every transaction posting.

## Chart of Accounts

- **Assets**:
  - `customer_escrow`: Holds customer gross payment funds prior to settlement.
  - `bank_disbursement`: Settlement clearing account.
- **Liabilities**:
  - `merchant_available`: Merchant funds ready for withdrawal.
  - `merchant_pending`: Funds in settlement processing.
- **Revenue**:
  - `platform_fee`: Gazoma Pay platform earnings (1.5% + GH₵ 0.50).
- **Expenses**:
  - `provider_fee`: Direct payment gateway processing costs.

## Accounting Journal Flow (Sample GH₵ 1,000 Payment)

```text
DEBIT  customer_escrow     GH₵ 1,000.00
CREDIT merchant_available  GH₵   984.50
CREDIT platform_fee        GH₵    15.50
```
