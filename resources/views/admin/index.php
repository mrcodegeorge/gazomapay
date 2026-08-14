<div class="card" style="margin-bottom: 28px;">
    <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 16px;">Platform Merchants</h3>
    <table class="data-table">
        <thead>
            <tr>
                <th>Merchant Code</th>
                <th>Company Name</th>
                <th>Email</th>
                <th>Available Balance</th>
                <th>Status</th>
                <th>Created</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($merchants as $m): ?>
                <tr>
                    <td style="font-family: monospace; font-weight: 700; color: var(--primary-blue);"><?= htmlspecialchars($m['merchant_id']) ?></td>
                    <td style="font-weight: 700;"><?= htmlspecialchars($m['name']) ?></td>
                    <td><?= htmlspecialchars($m['email']) ?></td>
                    <td style="font-weight: 800; color: #16a34a;">GH₵ <?= number_format($m['available_balance'], 2) ?></td>
                    <td><?= Format::statusBadge($m['status']) ?></td>
                    <td style="color: var(--text-muted); font-size: 13px;"><?= Format::dateShort($m['created_at']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div class="card">
    <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 16px;">Global Platform Audit Logs</h3>
    <table class="data-table">
        <thead>
            <tr>
                <th>Action</th>
                <th>User</th>
                <th>IP Address</th>
                <th>Timestamp</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($systemLogs as $log): ?>
                <tr>
                    <td style="font-weight: 700; font-family: monospace; color: var(--primary-blue);"><?= htmlspecialchars($log['action']) ?></td>
                    <td><?= htmlspecialchars($log['user_email']) ?></td>
                    <td><code><?= htmlspecialchars($log['ip_address']) ?></code></td>
                    <td style="color: var(--text-muted); font-size: 13px;"><?= Format::date($log['created_at']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
