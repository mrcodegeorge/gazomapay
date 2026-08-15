<!-- Business Profile Card -->
<div class="glass-card rounded-xl border border-outline-variant p-6 mb-8">
    <div class="mb-6">
        <h3 class="font-headline-md text-headline-md text-on-surface font-semibold mb-1">Business Profile</h3>
        <p class="font-body-sm text-body-sm text-on-surface-variant">Update your registered merchant organization details and contact info.</p>
    </div>
    <form action="/settings/profile" method="POST">
        <input type="hidden" name="csrf_token" value="<?= Auth::generateCsrfToken() ?>">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div class="form-group">
                <label class="form-label block font-body-sm mb-1 text-on-surface">Business / Merchant Name</label>
                <input type="text" name="name" class="w-full px-3 py-2 border border-outline-variant rounded font-body-sm" value="<?= htmlspecialchars($user['merchant_name'] ?? 'Gazoma Tech') ?>" required>
            </div>
            <div class="form-group">
                <label class="form-label block font-body-sm mb-1 text-on-surface">Contact Email</label>
                <input type="email" name="email" class="w-full px-3 py-2 border border-outline-variant rounded font-body-sm" value="<?= htmlspecialchars($user['email'] ?? 'admin@gazomapay.com') ?>" required>
            </div>
            <div class="form-group">
                <label class="form-label block font-body-sm mb-1 text-on-surface">Phone Number</label>
                <input type="text" name="phone" class="w-full px-3 py-2 border border-outline-variant rounded font-body-sm" value="+233 24 123 4567">
            </div>
            <div class="form-group">
                <label class="form-label block font-body-sm mb-1 text-on-surface">Settlement Currency</label>
                <input type="text" class="w-full px-3 py-2 border border-outline-variant rounded bg-surface-container font-body-sm text-on-surface-variant font-medium" value="GHS (Ghana Cedi)" readonly>
            </div>
            <div class="form-group md:col-span-2">
                <label class="form-label block font-body-sm mb-1 text-on-surface">Physical Address</label>
                <input type="text" name="address" class="w-full px-3 py-2 border border-outline-variant rounded font-body-sm" value="15 Independence Avenue, Ridge, Accra">
            </div>
        </div>
        <button type="submit" class="px-5 py-2.5 bg-primary text-on-primary font-body-sm font-semibold rounded-lg hover:bg-primary/90 transition-colors cursor-pointer shadow-sm">Save Profile Changes</button>
    </form>
</div>

<!-- Team Management Card -->
<div class="glass-card rounded-xl border border-outline-variant overflow-hidden">
    <div class="p-6 border-b border-outline-variant bg-surface flex justify-between items-center">
        <div>
            <h3 class="font-headline-md text-headline-md text-on-surface font-semibold">Team Management & Access Roles</h3>
            <p class="font-body-sm text-body-sm text-on-surface-variant">Invite team members and manage RBAC permission roles.</p>
        </div>
        <button class="px-4 py-2 bg-primary text-on-primary font-body-sm text-body-sm font-semibold rounded-lg hover:bg-primary/90 transition-colors flex items-center gap-2 cursor-pointer shadow-sm" onclick="openModal('addTeamModal')">
            <span class="material-symbols-outlined text-[18px]">add</span>
            Add Team Member
        </button>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b border-outline-variant bg-surface-container-low/80 font-label-caps text-label-caps text-on-surface-variant uppercase">
                    <th class="py-3.5 px-6">User Name</th>
                    <th class="py-3.5 px-6">Email Address</th>
                    <th class="py-3.5 px-6">Role</th>
                    <th class="py-3.5 px-6">Status</th>
                    <th class="py-3.5 px-6">Last Login</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant font-body-sm text-body-sm">
                <?php foreach ($team as $member): ?>
                    <tr class="hover:bg-surface-container-low/50 transition-colors">
                        <td class="py-4 px-6 font-semibold text-on-surface"><?= htmlspecialchars($member['name']) ?></td>
                        <td class="py-4 px-6 text-on-surface-variant font-data-mono text-xs"><?= htmlspecialchars($member['email']) ?></td>
                        <td class="py-4 px-6">
                            <span class="px-2.5 py-1 rounded bg-secondary/10 text-secondary font-label-caps text-[11px] uppercase font-bold"><?= htmlspecialchars($member['role']) ?></span>
                        </td>
                        <td class="py-4 px-6"><?= Format::statusBadge($member['status']) ?></td>
                        <td class="py-4 px-6 text-on-surface-variant"><?= Format::date($member['last_login']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Add Team Member Modal -->
<div class="modal-overlay" id="addTeamModal">
    <div class="modal-card max-w-[480px]">
        <div class="modal-header">
            <h3 class="modal-title">Add Staff Member</h3>
            <button class="modal-close" onclick="closeModal('addTeamModal')">&times;</button>
        </div>
        <form action="/settings/team/add" method="POST">
            <input type="hidden" name="csrf_token" value="<?= Auth::generateCsrfToken() ?>">
            <div class="form-group mb-4">
                <label class="form-label block font-body-sm mb-1 text-on-surface">Full Name</label>
                <input type="text" name="name" class="w-full px-3 py-2 border border-outline-variant rounded font-body-sm" placeholder="Kwame Nkrumah" required>
            </div>
            <div class="form-group mb-4">
                <label class="form-label block font-body-sm mb-1 text-on-surface">Email Address</label>
                <input type="email" name="email" class="w-full px-3 py-2 border border-outline-variant rounded font-body-sm" placeholder="kwame@gazomatech.com" required>
            </div>
            <div class="form-group mb-6">
                <label class="form-label block font-body-sm mb-1 text-on-surface">Role Level</label>
                <select name="role" class="w-full px-3 py-2 border border-outline-variant rounded bg-white font-body-sm" required>
                    <option value="admin">Admin</option>
                    <option value="finance">Finance Manager</option>
                    <option value="developer">Developer</option>
                    <option value="support">Support Agent</option>
                    <option value="viewer">Viewer</option>
                </select>
            </div>
            <div class="flex justify-end gap-3 pt-2">
                <button type="button" class="px-4 py-2 border border-outline-variant rounded font-body-sm text-on-surface hover:bg-surface-container-low" onclick="closeModal('addTeamModal')">Cancel</button>
                <button type="submit" class="px-5 py-2 bg-primary text-on-primary font-body-sm font-medium rounded hover:bg-primary/90 cursor-pointer">Add Member</button>
            </div>
        </form>
    </div>
</div>
