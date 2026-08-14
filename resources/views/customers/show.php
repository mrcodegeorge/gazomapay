<div style="margin-bottom: 20px;">
    <a href="/customers" style="font-size: 13px; font-weight: 600;">&larr; Back to Customers</a>
</div>

<div class="card" style="margin-bottom: 24px;">
    <div style="display: flex; justify-content: space-between; align-items: flex-start;">
        <div style="display: flex; gap: 16px; align-items: center;">
            <div style="width: 56px; height: 56px; border-radius: 50%; background: #eff6ff; color: var(--primary-blue); font-weight: 800; font-size: 22px; display: flex; align-items: center; justify-content: center;">
                <?= strtoupper(substr($customer['name'], 0, 1)) ?>
            </div>
            <div>
                <h2 style="font-size: 22px; font-weight: 800; color: var(--text-main);"><?= htmlspecialchars($customer['name']) ?></h2>
                <p style="color: var(--text-muted); font-size: 13px;"><?= htmlspecialchars($customer['email']) ?> • <?= htmlspecialchars($customer['phone'] ?: 'No Phone') ?></p>
            </div>
        </div>

        <?= Format::statusBadge($customer['status']) ?>
    </div>

    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-top: 24px; padding-top: 20px; border-top: 1px solid var(--border-color);">
        <div>
            <div style="font-size: 12px; color: var(--text-muted);">Total Transactions</div>
            <div style="font-size: 20px; font-weight: 800;"><?= number_format($customer['total_transactions']) ?></div>
        </div>
        <div>
            <div style="font-size: 12px; color: var(--text-muted);">Total Spending</div>
            <div style="font-size: 20px; font-weight: 800; color: var(--primary-blue);">GH₵ <?= number_format($customer['total_spending'], 2) ?></div>
        </div>
        <div>
            <div style="font-size: 12px; color: var(--text-muted);">Successful Payments</div>
            <div style="font-size: 20px; font-weight: 800; color: #16a34a;"><?= number_format($customer['successful_payments']) ?></div>
        </div>
        <div>
            <div style="font-size: 12px; color: var(--text-muted);">Failed Payments</div>
            <div style="font-size: 20px; font-weight: 800; color: #dc2626;"><?= number_format($customer['failed_payments']) ?></div>
        </div>
    </div>
</div>

<div class="card">
    <h3 style="font-size: 16px; font-weight: 700; margin-bottom: 16px;">Customer Transaction History</h3>
    <table class="data-table">
        <thead>
            <tr>
                <th>Reference</th>
                <th>Amount</th>
                <th>Payment Method</th>
                <th>Status</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($transactions)): ?>
                <tr>
                    <td colspan="5" style="text-align: center; padding: 30px; color: var(--text-muted);">No transaction history for this customer.</td>
                </tr>
            <?php else: foreach ($transactions as $t): ?>
                <tr>
                    <td style="font-family: monospace; font-weight: 600; color: var(--primary-blue);">
                        <a href="/transactions/<?= $t['id'] ?>"><?= htmlspecialchars($t['reference']) ?></a>
                    </td>
                    <td style="font-weight: 700;">GH₵ <?= number_format($t['amount'], 2) ?></td>
                    <td><?= strtoupper($t['payment_method']) ?></td>
                    <td><?= Format::statusBadge($t['status']) ?></td>
                    <td style="color: var(--text-muted); font-size: 13px;"><?= Format::date($t['created_at']) ?></td>
                </tr>
            <?php endforeach; endif; ?>
        </tbody>
    </table>
</div>
