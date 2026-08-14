<div class="card" style="margin-bottom: 28px;">
    <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 20px;">Business Profile</h3>
    <form action="/settings/profile" method="POST">
        <input type="hidden" name="csrf_token" value="<?= Auth::generateCsrfToken() ?>">
        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px;">
            <div class="form-group">
                <label class="form-label">Business / Merchant Name</label>
                <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($user['merchant_name'] ?? 'Gazoma Tech') ?>" required>
            </div>
            <div class="form-group">
                <label class="form-label">Contact Email</label>
                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email'] ?? 'admin@gazomapay.com') ?>" required>
            </div>
            <div class="form-group">
                <label class="form-label">Phone Number</label>
                <input type="text" name="phone" class="form-control" value="+233 24 123 4567">
            </div>
            <div class="form-group">
                <label class="form-label">Settlement Currency</label>
                <input type="text" class="form-control" value="GHS (Ghana Cedi)" readonly style="background: #f8fafc;">
            </div>
            <div class="form-group" style="grid-column: span 2;">
                <label class="form-label">Physical Address</label>
                <input type="text" name="address" class="form-control" value="15 Independence Avenue, Ridge, Accra">
            </div>
        </div>
        <button type="submit" class="btn btn-primary" style="margin-top: 10px;">Save Profile Changes</button>
    </form>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Team Management & Roles</h3>
        <button class="btn btn-primary btn-sm" onclick="openModal('addTeamModal')">Add Team Member</button>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th>User</th>
                <th>Email</th>
                <th>Role</th>
                <th>Status</th>
                <th>Last Login</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($team as $member): ?>
                <tr>
                    <td style="font-weight: 700;"><?= htmlspecialchars($member['name']) ?></td>
                    <td><?= htmlspecialchars($member['email']) ?></td>
                    <td style="text-transform: capitalize;"><span class="badge badge-info"><?= htmlspecialchars($member['role']) ?></span></td>
                    <td><?= Format::statusBadge($member['status']) ?></td>
                    <td style="color: var(--text-muted); font-size: 13px;"><?= Format::date($member['last_login']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Add Team Member Modal -->
<div class="modal-overlay" id="addTeamModal">
    <div class="modal-card">
        <div class="modal-header">
            <h3 class="modal-title">Add Staff Member</h3>
            <button class="modal-close" onclick="closeModal('addTeamModal')">&times;</button>
        </div>
        <form action="/settings/team/add" method="POST">
            <input type="hidden" name="csrf_token" value="<?= Auth::generateCsrfToken() ?>">
            <div class="form-group">
                <label class="form-label">Full Name</label>
                <input type="text" name="name" class="form-control" placeholder="Kwame Nkrumah" required>
            </div>
            <div class="form-group">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-control" placeholder="kwame@gazomatech.com" required>
            </div>
            <div class="form-group">
                <label class="form-label">Role</label>
                <select name="role" class="form-control" required>
                    <option value="admin">Admin</option>
                    <option value="finance">Finance Manager</option>
                    <option value="developer">Developer</option>
                    <option value="support">Support Agent</option>
                    <option value="viewer">Viewer</option>
                </select>
            </div>
            <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px;">
                <button type="button" class="btn btn-outline" onclick="closeModal('addTeamModal')">Cancel</button>
                <button type="submit" class="btn btn-primary">Add Member</button>
            </div>
        </form>
    </div>
</div>
