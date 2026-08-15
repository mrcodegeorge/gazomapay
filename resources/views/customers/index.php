<!-- Toolbar -->
<div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
    <form action="/customers" method="GET" class="relative w-full md:w-96">
        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-[20px]">search</span>
        <input type="text" name="search" class="w-full bg-surface-container-low border border-outline-variant rounded-lg pl-10 pr-4 py-2 font-body-sm text-body-sm focus:ring-2 focus:ring-secondary focus:bg-white transition-all" placeholder="Search customer name, email, phone..." value="<?= htmlspecialchars($search) ?>">
    </form>

    <button class="px-4 py-2 bg-primary text-on-primary font-body-sm text-body-sm font-semibold rounded-lg hover:bg-primary/90 transition-colors flex items-center gap-2 cursor-pointer shadow-sm" onclick="openModal('addCustomerModal')">
        <span class="material-symbols-outlined text-[18px]">add</span>
        Add Customer
    </button>
</div>

<!-- Data Table Card -->
<div class="glass-card rounded-xl border border-outline-variant overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b border-outline-variant bg-surface-container-low/80 font-label-caps text-label-caps text-on-surface-variant uppercase">
                    <th class="py-3.5 px-6">Customer Name</th>
                    <th class="py-3.5 px-6">Phone Number</th>
                    <th class="py-3.5 px-6">Country</th>
                    <th class="py-3.5 px-6">Tx Count</th>
                    <th class="py-3.5 px-6">Total Spending</th>
                    <th class="py-3.5 px-6">Status</th>
                    <th class="py-3.5 px-6 text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant font-body-sm text-body-sm">
                <?php if (empty($customers)): ?>
                    <tr>
                        <td colspan="7" class="text-center py-12 text-on-surface-variant">
                            No customers found matching your criteria.
                        </td>
                    </tr>
                <?php else: foreach ($customers as $c): ?>
                    <tr class="hover:bg-surface-container-low/50 transition-colors">
                        <td class="py-4 px-6">
                            <a href="/customers/<?= $c['id'] ?>" class="font-semibold text-on-surface hover:text-secondary transition-colors block"><?= htmlspecialchars($c['name']) ?></a>
                            <div class="font-label-caps text-[11px] text-on-surface-variant"><?= htmlspecialchars($c['email']) ?></div>
                        </td>
                        <td class="py-4 px-6 text-on-surface-variant font-data-mono text-xs"><?= htmlspecialchars($c['phone'] ?: 'N/A') ?></td>
                        <td class="py-4 px-6 text-on-surface-variant"><?= htmlspecialchars($c['country']) ?></td>
                        <td class="py-4 px-6 font-semibold text-on-surface"><?= number_format($c['total_transactions']) ?></td>
                        <td class="py-4 px-6 font-data-mono font-bold text-secondary">GH₵ <?= number_format($c['total_spending'], 2) ?></td>
                        <td class="py-4 px-6"><?= Format::statusBadge($c['status']) ?></td>
                        <td class="py-4 px-6 text-right">
                            <a href="/customers/<?= $c['id'] ?>" class="px-3 py-1 border border-outline-variant rounded font-body-sm text-xs font-medium text-on-surface hover:bg-surface-container-low transition-colors">View Profile</a>
                        </td>
                    </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Add Customer Modal -->
<div class="modal-overlay" id="addCustomerModal">
    <div class="modal-card max-w-[480px]">
        <div class="modal-header">
            <h3 class="modal-title">Add New Customer</h3>
            <button class="modal-close" onclick="closeModal('addCustomerModal')">&times;</button>
        </div>
        <form action="/customers/create" method="POST">
            <input type="hidden" name="csrf_token" value="<?= Auth::generateCsrfToken() ?>">
            <div class="form-group mb-4">
                <label class="form-label block font-body-sm mb-1 text-on-surface">Full Name</label>
                <input type="text" name="name" class="w-full px-3 py-2 border border-outline-variant rounded font-body-sm" placeholder="Kofi Mensah" required>
            </div>
            <div class="form-group mb-4">
                <label class="form-label block font-body-sm mb-1 text-on-surface">Email Address</label>
                <input type="email" name="email" class="w-full px-3 py-2 border border-outline-variant rounded font-body-sm" placeholder="kofi@example.com" required>
            </div>
            <div class="form-group mb-6">
                <label class="form-label block font-body-sm mb-1 text-on-surface">Phone Number</label>
                <input type="tel" name="phone" class="w-full px-3 py-2 border border-outline-variant rounded font-body-sm" placeholder="+233 24 123 4567">
            </div>
            <div class="flex justify-end gap-3 pt-2">
                <button type="button" class="px-4 py-2 border border-outline-variant rounded font-body-sm text-on-surface hover:bg-surface-container-low" onclick="closeModal('addCustomerModal')">Cancel</button>
                <button type="submit" class="px-5 py-2 bg-primary text-on-primary font-body-sm font-medium rounded hover:bg-primary/90 cursor-pointer">Save Customer</button>
            </div>
        </form>
    </div>
</div>
