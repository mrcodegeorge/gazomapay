<!-- Flash Notifications -->
<?php if ($msg = Response::getFlash('success')): ?>
    <div class="p-4 mb-6 bg-emerald-50 border border-emerald-200 text-emerald-900 rounded-2xl font-body-sm text-sm flex items-center gap-3">
        <span class="material-symbols-outlined text-emerald-600 text-[20px]">check_circle</span>
        <span><?= htmlspecialchars($msg) ?></span>
    </div>
<?php endif; ?>

<!-- Header Toolbar -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
    <div>
        <h2 class="font-headline-lg text-headline-lg text-on-surface mb-1">Global Gateway & Fee Engine Configuration</h2>
        <p class="font-body-sm text-body-sm text-on-surface-variant">Update default processing fee rates, emergency maintenance mode, and payment gateway drivers.</p>
    </div>
</div>

<div class="glass-card rounded-xl p-8 border border-outline-variant max-w-2xl bg-surface">
    <form action="/admin/settings/update" method="POST" class="space-y-6">
        <input type="hidden" name="csrf_token" value="<?= Auth::generateCsrfToken() ?>">

        <div>
            <label class="block font-body-sm text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-2">Default Fee Percentage (%) *</label>
            <input type="number" step="0.01" name="platform_fee_percent" value="<?= htmlspecialchars($platformSettings['platform_fee_percent'] ?? '1.50') ?>" class="w-full px-4 py-3 bg-surface-container-low border border-outline-variant rounded-xl font-body-sm font-data-mono text-sm text-on-surface focus:bg-surface focus:ring-2 focus:ring-secondary" required>
            <p class="font-body-sm text-[11px] text-on-surface-variant mt-1">Standard transaction fee percentage applied across all merchants unless a custom rate is set.</p>
        </div>

        <div>
            <label class="block font-body-sm text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-2">Default Flat Fee (GHS) *</label>
            <input type="number" step="0.01" name="platform_fee_flat" value="<?= htmlspecialchars($platformSettings['platform_fee_flat'] ?? '0.50') ?>" class="w-full px-4 py-3 bg-surface-container-low border border-outline-variant rounded-xl font-body-sm font-data-mono text-sm text-on-surface focus:bg-surface focus:ring-2 focus:ring-secondary" required>
            <p class="font-body-sm text-[11px] text-on-surface-variant mt-1">Fixed per-transaction charge added to the percentage fee.</p>
        </div>

        <div>
            <label class="block font-body-sm text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-2">Global System State *</label>
            <select name="maintenance_mode" class="w-full px-4 py-3 bg-surface-container-low border border-outline-variant rounded-xl font-body-sm text-sm text-on-surface focus:bg-surface focus:ring-2 focus:ring-secondary cursor-pointer">
                <option value="0" <?= ($platformSettings['maintenance_mode'] ?? '0') === '0' ? 'selected' : '' ?>>🟢 Live Mode (Accepting Payments)</option>
                <option value="1" <?= ($platformSettings['maintenance_mode'] ?? '0') === '1' ? 'selected' : '' ?>>🔴 Maintenance Mode (Paused)</option>
            </select>
        </div>

        <div class="pt-4 border-t border-outline-variant flex justify-end">
            <button type="submit" class="px-6 py-3 bg-primary text-on-primary font-body-sm text-xs font-bold rounded-xl hover:bg-primary/90 transition-colors shadow-sm cursor-pointer flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px]">save</span>
                <span>Save Platform Settings</span>
            </button>
        </div>
    </form>
</div>
