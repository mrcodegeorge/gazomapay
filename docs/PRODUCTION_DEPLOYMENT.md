# Gazoma Pay — Production Deployment & Operational Runbook

## System Requirements

- **PHP**: 8.0 or higher with extensions: `pdo_mysql`, `curl`, `json`, `mbstring`, `openssl`.
- **MySQL**: 8.0 or higher running on `127.0.0.1:3306`.
- **Web Server**: Nginx or Apache configured to route all non-static requests to `public/index.php`.

## Deployment Checklist

1. Clone repository to server directory: `c:\Users\georg\Desktop\gazoma system` (or Linux `/var/www/gazoma`).
2. Copy environment template: `cp .env.example .env`.
3. Configure live credentials in `.env`:
   - `GAZOMA_PAYMENT_MODE=live`
   - `PAYSTACK_ENABLED=true`
   - `PAYSTACK_SECRET_KEY=sk_live_...`
   - `HUBTEL_ENABLED=true`
4. Run Database Seeder & Migrations: `php database/seed.php`.
5. Configure Cron Background Workers:
   ```cron
   * * * * * php /var/www/gazoma/cli/process_webhooks.php >> /var/log/gazoma_webhooks.log 2>&1
   */5 * * * * php /var/www/gazoma/cli/process_settlements.php >> /var/log/gazoma_settlements.log 2>&1
   0 * * * * php /var/www/gazoma/cli/process_reconciliation.php >> /var/log/gazoma_reconciliation.log 2>&1
   ```
6. Verify Subsystem Health: `curl http://localhost:8000/api/v1/health`.
