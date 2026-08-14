<div class="toolbar">
    <div style="font-size: 16px; font-weight: 700;">Invoices</div>
    <button class="btn btn-primary" onclick="openModal('createInvoiceModal')">
        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Create Invoice
    </button>
</div>

<div class="table-container">
    <table class="data-table">
        <thead>
            <tr>
                <th>Invoice Number</th>
                <th>Customer</th>
                <th>Total Amount</th>
                <th>Due Date</th>
                <th>Status</th>
                <th style="text-align: right;">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($invoices)): ?>
                <tr>
                    <td colspan="6" style="text-align: center; padding: 40px; color: var(--text-muted);">No invoices created yet.</td>
                </tr>
            <?php else: foreach ($invoices as $inv): ?>
                <tr>
                    <td style="font-weight: 700; font-family: monospace; color: var(--primary-blue);"><?= htmlspecialchars($inv['invoice_number']) ?></td>
                    <td>
                        <div style="font-weight: 600;"><?= htmlspecialchars($inv['customer_name']) ?></div>
                        <div style="font-size: 11px; color: var(--text-muted);"><?= htmlspecialchars($inv['customer_email']) ?></div>
                    </td>
                    <td style="font-weight: 800;">GH₵ <?= number_format($inv['total'], 2) ?></td>
                    <td style="color: var(--text-muted); font-size: 13px;"><?= Format::dateShort($inv['due_date']) ?></td>
                    <td><?= Format::statusBadge($inv['status']) ?></td>
                    <td style="text-align: right;">
                        <a href="/invoices/<?= $inv['id'] ?>/pdf" target="_blank" class="btn btn-outline btn-sm">
                            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            PDF
                        </a>
                    </td>
                </tr>
            <?php endforeach; endif; ?>
        </tbody>
    </table>
</div>

<!-- Create Invoice Modal -->
<div class="modal-overlay" id="createInvoiceModal">
    <div class="modal-card">
        <div class="modal-header">
            <h3 class="modal-title">Create New Invoice</h3>
            <button class="modal-close" onclick="closeModal('createInvoiceModal')">&times;</button>
        </div>
        <form action="/invoices/create" method="POST">
            <input type="hidden" name="csrf_token" value="<?= Auth::generateCsrfToken() ?>">
            <div class="form-group">
                <label class="form-label">Select Customer</label>
                <select name="customer_id" class="form-control" required>
                    <option value="">-- Choose Customer --</option>
                    <?php foreach ($customers as $c): ?>
                        <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?> (<?= htmlspecialchars($c['email']) ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Item Description</label>
                <input type="text" name="description" class="form-control" placeholder="Software consultancy services..." required>
            </div>
            <div class="form-group">
                <label class="form-label">Total Amount (GHS)</label>
                <input type="number" step="0.01" name="amount" class="form-control" placeholder="1500.00" required>
            </div>
            <div class="form-group">
                <label class="form-label">Due Date</label>
                <input type="date" name="due_date" class="form-control" value="<?= date('Y-m-d', strtotime('+14 days')) ?>" required>
            </div>
            <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px;">
                <button type="button" class="btn btn-outline" onclick="closeModal('createInvoiceModal')">Cancel</button>
                <button type="submit" class="btn btn-primary">Generate Invoice</button>
            </div>
        </form>
    </div>
</div>
