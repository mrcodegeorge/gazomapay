<div class="metrics-grid">
    <div class="card">
        <div class="metric-title" style="margin-bottom: 8px;">Available Balance</div>
        <div class="metric-value">GH₵ <?= number_format($merchant['available_balance'], 2) ?></div>
        <a href="javascript:void(0)" onclick="openModal('requestPayoutModal')" class="withdraw-btn">Request Payout &rarr;</a>
    </div>

    <div class="card">
        <div class="metric-title" style="margin-bottom: 8px;">Pending Balance</div>
        <div class="metric-value" style="color: #a16207;">GH₵ <?= number_format($merchant['pending_balance'], 2) ?></div>
        <div class="metric-subtext">Awaiting bank settlement</div>
    </div>

    <div class="card" style="grid-column: span 2;">
        <div class="metric-title" style="margin-bottom: 8px;">Total Settled Balance</div>
        <div class="metric-value" style="color: #16a34a;">GH₵ <?= number_format($merchant['settled_balance'], 2) ?></div>
        <div class="metric-subtext">Disbursed to primary bank account</div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Settlement History</h3>
        <button class="btn btn-primary btn-sm" onclick="openModal('requestPayoutModal')">Request Settlement</button>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th>Settlement ID</th>
                <th>Gross Amount</th>
                <th>Fee</th>
                <th>Net Amount</th>
                <th>Bank Account</th>
                <th>Status</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($settlements)): ?>
                <tr>
                    <td colspan="7" style="text-align: center; padding: 40px; color: var(--text-muted);">No settlement requests recorded yet.</td>
                </tr>
            <?php else: foreach ($settlements as $s): ?>
                <tr>
                    <td style="font-family: monospace; font-weight: 700; color: var(--primary-blue);"><?= htmlspecialchars($s['reference']) ?></td>
                    <td style="font-weight: 700;">GH₵ <?= number_format($s['gross_amount'], 2) ?></td>
                    <td style="color: var(--text-muted);">GH₵ <?= number_format($s['fee'], 2) ?></td>
                    <td style="font-weight: 800; color: #16a34a;">GH₵ <?= number_format($s['net_amount'], 2) ?></td>
                    <td>
                        <div style="font-size: 13px; font-weight: 600;"><?= htmlspecialchars($s['bank_name']) ?></div>
                        <div style="font-size: 11px; color: var(--text-muted); font-family: monospace;"><?= htmlspecialchars($s['account_number']) ?></div>
                    </td>
                    <td><?= Format::statusBadge($s['status']) ?></td>
                    <td style="color: var(--text-muted); font-size: 13px;"><?= Format::date($s['created_at']) ?></td>
                </tr>
            <?php endforeach; endif; ?>
        </tbody>
    </table>
</div>

<!-- Request Payout Modal -->
<div class="modal-overlay" id="requestPayoutModal">
    <div class="modal-card">
        <div class="modal-header">
            <h3 class="modal-title">Request Bank Settlement</h3>
            <button class="modal-close" onclick="closeModal('requestPayoutModal')">&times;</button>
        </div>
        <form action="/settlements/request" method="POST">
            <input type="hidden" name="csrf_token" value="<?= Auth::generateCsrfToken() ?>">
            <div class="form-group">
                <label class="form-label">Available Balance</label>
                <input type="text" class="form-control" value="GH₵ <?= number_format($merchant['available_balance'], 2) ?>" readonly style="background: #f8fafc; font-weight: 700;">
            </div>
            <div class="form-group">
                <label class="form-label">Withdrawal Amount (GHS)</label>
                <input type="number" step="0.01" max="<?= $merchant['available_balance'] ?>" name="amount" class="form-control" value="<?= $merchant['available_balance'] ?>" required>
            </div>
            <div class="form-group">
                <label class="form-label">Destination Bank Account</label>
                <select name="bank_name" class="form-control" required>
                    <option value="GCB Bank Ghana - 1011129384728">GCB Bank Ghana (1011129384728)</option>
                    <option value="Stanbic Bank Ghana - 9040001827364">Stanbic Bank Ghana (9040001827364)</option>
                </select>
            </div>
            <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px;">
                <button type="button" class="btn btn-outline" onclick="closeModal('requestPayoutModal')">Cancel</button>
                <button type="submit" class="btn btn-primary">Confirm Payout Request</button>
            </div>
        </form>
    </div>
</div>
