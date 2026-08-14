<div class="toolbar">
    <form action="/customers" method="GET" class="search-box">
        <span class="search-icon">
            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        </span>
        <input type="text" name="search" class="search-input" placeholder="Search customers..." value="<?= htmlspecialchars($search) ?>">
    </form>

    <button class="btn btn-primary" onclick="openModal('addCustomerModal')">
        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Add Customer
    </button>
</div>

<div class="table-container">
    <table class="data-table">
        <thead>
            <tr>
                <th>Customer</th>
                <th>Phone</th>
                <th>Country</th>
                <th>Transactions</th>
                <th>Total Spending</th>
                <th>Status</th>
                <th style="text-align: right;">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($customers)): ?>
                <tr>
                    <td colspan="7" style="text-align: center; padding: 40px; color: var(--text-muted);">
                        No customers recorded.
                    </td>
                </tr>
            <?php else: foreach ($customers as $c): ?>
                <tr>
                    <td>
                        <div style="font-weight: 700;">
                            <a href="/customers/<?= $c['id'] ?>" style="color: var(--text-main);"><?= htmlspecialchars($c['name']) ?></a>
                        </div>
                        <div style="font-size: 11px; color: var(--text-muted);"><?= htmlspecialchars($c['email']) ?></div>
                    </td>
                    <td style="color: var(--text-muted);"><?= htmlspecialchars($c['phone'] ?: 'N/A') ?></td>
                    <td><?= htmlspecialchars($c['country']) ?></td>
                    <td style="font-weight: 600;"><?= number_format($c['total_transactions']) ?></td>
                    <td style="font-weight: 700; color: var(--primary-blue);">GH₵ <?= number_format($c['total_spending'], 2) ?></td>
                    <td><?= Format::statusBadge($c['status']) ?></td>
                    <td style="text-align: right;">
                        <a href="/customers/<?= $c['id'] ?>" class="btn btn-outline btn-sm">View Profile</a>
                    </td>
                </tr>
            <?php endforeach; endif; ?>
        </tbody>
    </table>
</div>

<!-- Add Customer Modal -->
<div class="modal-overlay" id="addCustomerModal">
    <div class="modal-card">
        <div class="modal-header">
            <h3 class="modal-title">Add New Customer</h3>
            <button class="modal-close" onclick="closeModal('addCustomerModal')">&times;</button>
        </div>
        <form action="/customers/create" method="POST">
            <input type="hidden" name="csrf_token" value="<?= Auth::generateCsrfToken() ?>">
            <div class="form-group">
                <label class="form-label">Full Name</label>
                <input type="text" name="name" class="form-control" placeholder="Kofi Mensah" required>
            </div>
            <div class="form-group">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-control" placeholder="kofi@example.com" required>
            </div>
            <div class="form-group">
                <label class="form-label">Phone Number</label>
                <input type="tel" name="phone" class="form-control" placeholder="+233 24 123 4567">
            </div>
            <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px;">
                <button type="button" class="btn btn-outline" onclick="closeModal('addCustomerModal')">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Customer</button>
            </div>
        </form>
    </div>
</div>
