<!-- Toolbar -->
<div class="flex justify-between items-center mb-6">
    <div>
        <h2 class="font-headline-lg text-headline-lg text-on-surface mb-1">Invoices</h2>
        <p class="font-body-sm text-body-sm text-on-surface-variant">Issue digital invoices, track due dates, and export PDF billing statements.</p>
    </div>
    <button class="px-4 py-2 bg-primary text-on-primary font-body-sm text-body-sm font-semibold rounded-lg hover:bg-primary/90 transition-colors flex items-center gap-2 cursor-pointer shadow-sm" onclick="openModal('createInvoiceModal')">
        <span class="material-symbols-outlined text-[18px]">add</span>
        Create Invoice
    </button>
</div>

<!-- Data Table Card -->
<div class="glass-card rounded-xl border border-outline-variant overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b border-outline-variant bg-surface-container-low/80 font-label-caps text-label-caps text-on-surface-variant uppercase">
                    <th class="py-3.5 px-6">Invoice #</th>
                    <th class="py-3.5 px-6">Customer</th>
                    <th class="py-3.5 px-6">Total Amount</th>
                    <th class="py-3.5 px-6">Due Date</th>
                    <th class="py-3.5 px-6">Status</th>
                    <th class="py-3.5 px-6 text-right">PDF</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant font-body-sm text-body-sm">
                <?php if (empty($invoices)): ?>
                    <tr>
                        <td colspan="6" class="text-center py-12 text-on-surface-variant">No invoices created yet.</td>
                    </tr>
                <?php else: foreach ($invoices as $inv): ?>
                    <tr class="hover:bg-surface-container-low/50 transition-colors">
                        <td class="py-4 px-6 font-data-mono font-bold text-secondary"><?= htmlspecialchars($inv['invoice_number']) ?></td>
                        <td class="py-4 px-6">
                            <div class="font-semibold text-on-surface"><?= htmlspecialchars($inv['customer_name']) ?></div>
                            <div class="font-label-caps text-[11px] text-on-surface-variant"><?= htmlspecialchars($inv['customer_email']) ?></div>
                        </td>
                        <td class="py-4 px-6 font-data-mono font-bold text-on-surface">GH₵ <?= number_format($inv['total'], 2) ?></td>
                        <td class="py-4 px-6 text-on-surface-variant"><?= Format::dateShort($inv['due_date']) ?></td>
                        <td class="py-4 px-6"><?= Format::statusBadge($inv['status']) ?></td>
                        <td class="py-4 px-6 text-right">
                            <a href="/invoices/<?= $inv['id'] ?>/pdf" target="_blank" class="px-3 py-1 border border-outline-variant rounded font-body-sm text-xs font-medium text-on-surface hover:bg-surface-container-low transition-colors inline-flex items-center gap-1">
                                <span class="material-symbols-outlined text-[16px]">picture_as_pdf</span>
                                PDF
                            </a>
                        </td>
                    </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Create Invoice Modal -->
<div class="modal-overlay" id="createInvoiceModal">
    <div class="modal-card max-w-[480px]">
        <div class="modal-header">
            <h3 class="modal-title">Create New Invoice</h3>
            <button class="modal-close" onclick="closeModal('createInvoiceModal')">&times;</button>
        </div>
        <form action="/invoices/create" method="POST">
            <input type="hidden" name="csrf_token" value="<?= Auth::generateCsrfToken() ?>">
            <div class="form-group mb-4">
                <label class="form-label block font-body-sm mb-1 text-on-surface">Select Customer</label>
                <select name="customer_id" class="w-full px-3 py-2 border border-outline-variant rounded bg-white font-body-sm" required>
                    <option value="">-- Choose Customer --</option>
                    <?php foreach ($customers as $c): ?>
                        <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?> (<?= htmlspecialchars($c['email']) ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group mb-4">
                <label class="form-label block font-body-sm mb-1 text-on-surface">Item Description</label>
                <input type="text" name="description" class="w-full px-3 py-2 border border-outline-variant rounded font-body-sm" placeholder="Software consultancy services..." required>
            </div>
            <div class="form-group mb-4">
                <label class="form-label block font-body-sm mb-1 text-on-surface">Total Amount (GHS)</label>
                <input type="number" step="0.01" name="amount" class="w-full px-3 py-2 border border-outline-variant rounded font-body-sm font-data-mono" placeholder="1500.00" required>
            </div>
            <div class="form-group mb-6">
                <label class="form-label block font-body-sm mb-1 text-on-surface">Due Date</label>
                <input type="date" name="due_date" class="w-full px-3 py-2 border border-outline-variant rounded font-body-sm" value="<?= date('Y-m-d', strtotime('+14 days')) ?>" required>
            </div>
            <div class="flex justify-end gap-3 pt-2">
                <button type="button" class="px-4 py-2 border border-outline-variant rounded font-body-sm text-on-surface hover:bg-surface-container-low" onclick="closeModal('createInvoiceModal')">Cancel</button>
                <button type="submit" class="px-5 py-2 bg-primary text-on-primary font-body-sm font-medium rounded hover:bg-primary/90 cursor-pointer">Generate Invoice</button>
            </div>
        </form>
    </div>
</div>
