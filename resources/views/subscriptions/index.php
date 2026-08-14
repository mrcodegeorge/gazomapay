<div class="toolbar">
    <div style="font-size: 16px; font-weight: 700;">Subscription Plans</div>
    <button class="btn btn-primary" onclick="openModal('createPlanModal')">
        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Create Plan
    </button>
</div>

<!-- Plans Grid -->
<div class="metrics-grid" style="margin-bottom: 32px;">
    <?php if (empty($plans)): ?>
        <p style="color: var(--text-muted);">No subscription plans available.</p>
    <?php else: foreach ($plans as $p): ?>
        <div class="card">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px;">
                <h4 style="font-size: 16px; font-weight: 700; color: var(--text-main);"><?= htmlspecialchars($p['name']) ?></h4>
                <?= Format::statusBadge($p['status']) ?>
            </div>
            <div style="font-size: 24px; font-weight: 800; color: var(--primary-blue);">GH₵ <?= number_format($p['amount'], 2) ?> <span style="font-size: 12px; font-weight: 600; color: var(--text-muted);">/ <?= $p['billing_interval'] ?></span></div>
        </div>
    <?php endforeach; endif; ?>
</div>

<div class="card">
    <h3 style="font-size: 16px; font-weight: 700; margin-bottom: 16px;">Active Subscribers</h3>
    <table class="data-table">
        <thead>
            <tr>
                <th>Customer</th>
                <th>Plan Name</th>
                <th>Amount</th>
                <th>Billing Interval</th>
                <th>Status</th>
                <th>Next Billing Date</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($subscriptions)): ?>
                <tr>
                    <td colspan="6" style="text-align: center; padding: 40px; color: var(--text-muted);">No active subscribers yet.</td>
                </tr>
            <?php else: foreach ($subscriptions as $s): ?>
                <tr>
                    <td>
                        <div style="font-weight: 700;"><?= htmlspecialchars($s['customer_name']) ?></div>
                        <div style="font-size: 11px; color: var(--text-muted);"><?= htmlspecialchars($s['customer_email']) ?></div>
                    </td>
                    <td style="font-weight: 600;"><?= htmlspecialchars($s['plan_name']) ?></td>
                    <td style="font-weight: 700;">GH₵ <?= number_format($s['amount'], 2) ?></td>
                    <td style="text-transform: capitalize;"><?= htmlspecialchars($s['billing_interval']) ?></td>
                    <td><?= Format::statusBadge($s['status']) ?></td>
                    <td style="color: var(--text-muted); font-size: 13px;"><?= Format::dateShort($s['next_billing_date']) ?></td>
                </tr>
            <?php endforeach; endif; ?>
        </tbody>
    </table>
</div>

<!-- Create Plan Modal -->
<div class="modal-overlay" id="createPlanModal">
    <div class="modal-card">
        <div class="modal-header">
            <h3 class="modal-title">Create Subscription Plan</h3>
            <button class="modal-close" onclick="closeModal('createPlanModal')">&times;</button>
        </div>
        <form action="/subscriptions/plan/create" method="POST">
            <input type="hidden" name="csrf_token" value="<?= Auth::generateCsrfToken() ?>">
            <div class="form-group">
                <label class="form-label">Plan Name</label>
                <input type="text" name="name" class="form-control" placeholder="Pro Monthly Tier" required>
            </div>
            <div class="form-group">
                <label class="form-label">Amount (GHS)</label>
                <input type="number" step="0.01" name="amount" class="form-control" placeholder="250.00" required>
            </div>
            <div class="form-group">
                <label class="form-label">Billing Interval</label>
                <select name="billing_interval" class="form-control" required>
                    <option value="daily">Daily</option>
                    <option value="weekly">Weekly</option>
                    <option value="monthly" selected>Monthly</option>
                    <option value="quarterly">Quarterly</option>
                    <option value="yearly">Yearly</option>
                </select>
            </div>
            <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px;">
                <button type="button" class="btn btn-outline" onclick="closeModal('createPlanModal')">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Plan</button>
            </div>
        </form>
    </div>
</div>
