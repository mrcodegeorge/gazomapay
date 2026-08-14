<div style="margin-bottom: 20px;">
    <a href="/transactions" style="font-size: 13px; font-weight: 600;">&larr; Back to Transactions</a>
</div>

<div class="card" style="margin-bottom: 24px;">
    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; border-bottom: 1px solid var(--border-color); padding-bottom: 20px;">
        <div>
            <h2 style="font-size: 24px; font-weight: 800; font-family: monospace; color: var(--primary-blue);"><?= htmlspecialchars($tx['reference']) ?></h2>
            <p style="color: var(--text-muted); font-size: 13px; margin-top: 4px;">Processed on <?= Format::date($tx['created_at']) ?></p>
        </div>
        <div style="display: flex; gap: 12px; align-items: center;">
            <?= Format::statusBadge($tx['status']) ?>
            <?php if ($tx['status'] === 'successful'): ?>
                <button class="btn btn-outline" onclick="openModal('refundModal')">Refund</button>
            <?php endif; ?>
            <button class="btn btn-primary" onclick="window.print()">Download Receipt</button>
        </div>
    </div>

    <!-- Details Grid -->
    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px;">
        <div>
            <div style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Financial Breakdown</div>
            <div style="margin-top: 8px;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 6px; font-size: 14px;">
                    <span>Gross Amount:</span> <span style="font-weight: 700;">GH₵ <?= number_format($tx['amount'], 2) ?></span>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 6px; font-size: 14px; color: var(--text-muted);">
                    <span>Platform Fee (1.5%):</span> <span>- GH₵ <?= number_format($tx['fee'], 2) ?></span>
                </div>
                <div style="display: flex; justify-content: space-between; margin-top: 10px; padding-top: 10px; border-top: 1px dashed var(--border-color); font-size: 16px; font-weight: 800; color: var(--primary-blue);">
                    <span>Net Amount:</span> <span>GH₵ <?= number_format($tx['net_amount'], 2) ?></span>
                </div>
            </div>
        </div>

        <div>
            <div style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Customer Information</div>
            <div style="margin-top: 8px; font-size: 14px;">
                <div style="font-weight: 700;"><?= htmlspecialchars($tx['customer_name'] ?? 'Guest Customer') ?></div>
                <div style="color: var(--text-muted);"><?= htmlspecialchars($tx['customer_email'] ?? 'N/A') ?></div>
                <div style="color: var(--text-muted);"><?= htmlspecialchars($tx['customer_phone'] ?? 'N/A') ?></div>
            </div>
        </div>

        <div>
            <div style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Payment Details</div>
            <div style="margin-top: 8px; font-size: 14px;">
                <div><span style="color: var(--text-muted);">Method:</span> <strong><?= strtoupper($tx['payment_method']) ?></strong></div>
                <div><span style="color: var(--text-muted);">Provider:</span> <strong><?= htmlspecialchars($tx['provider']) ?></strong></div>
                <div><span style="color: var(--text-muted);">IP Address:</span> <code><?= htmlspecialchars($tx['ip_address'] ?? '127.0.0.1') ?></code></div>
            </div>
        </div>
    </div>
</div>

<!-- Refund Modal -->
<div class="modal-overlay" id="refundModal">
    <div class="modal-card">
        <div class="modal-header">
            <h3 class="modal-title">Initiate Refund</h3>
            <button class="modal-close" onclick="closeModal('refundModal')">&times;</button>
        </div>
        <form action="/transactions/<?= $tx['id'] ?>/refund" method="POST">
            <input type="hidden" name="csrf_token" value="<?= Auth::generateCsrfToken() ?>">
            <div class="form-group">
                <label class="form-label">Transaction Reference</label>
                <input type="text" class="form-control" value="<?= htmlspecialchars($tx['reference']) ?>" readonly style="background: #f8fafc;">
            </div>
            <div class="form-group">
                <label class="form-label">Refund Amount (GHS)</label>
                <input type="number" step="0.01" max="<?= $tx['amount'] ?>" name="amount" class="form-control" value="<?= $tx['amount'] ?>" required>
            </div>
            <div class="form-group">
                <label class="form-label">Reason for Refund</label>
                <textarea name="reason" class="form-control" rows="3" placeholder="Specify reason..." required></textarea>
            </div>
            <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px;">
                <button type="button" class="btn btn-outline" onclick="closeModal('refundModal')">Cancel</button>
                <button type="submit" class="btn btn-primary" style="background: #dc2626; border-color: #dc2626;">Process Refund</button>
            </div>
        </form>
    </div>
</div>
