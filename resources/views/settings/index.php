<!-- Flash Notifications -->
<?php if ($msg = Response::getFlash('success')): ?>
    <div class="p-4 mb-6 bg-emerald-50 border border-emerald-200 text-emerald-900 rounded-2xl font-body-sm text-sm flex items-center gap-3">
        <span class="material-symbols-outlined text-emerald-600 text-[20px]">check_circle</span>
        <span><?= htmlspecialchars($msg) ?></span>
    </div>
<?php endif; ?>

<?php if ($msg = Response::getFlash('error')): ?>
    <div class="p-4 mb-6 bg-rose-50 border border-rose-200 text-rose-900 rounded-2xl font-body-sm text-sm flex items-center gap-3">
        <span class="material-symbols-outlined text-rose-600 text-[20px]">error</span>
        <span><?= htmlspecialchars($msg) ?></span>
    </div>
<?php endif; ?>

<!-- Top Page Header -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
    <div>
        <h2 class="font-headline-lg text-headline-lg text-on-surface mb-1">Account & Business Settings</h2>
        <p class="font-body-sm text-body-sm text-on-surface-variant">Configure business profile, API credentials, webhook integration, team member access, and security settings.</p>
    </div>
    
    <!-- Environment Status Switcher Banner -->
    <div class="flex items-center gap-3 bg-surface-container-lowest p-2 rounded-xl border border-outline-variant shadow-sm">
        <div class="flex items-center gap-2 px-3 py-1 rounded-lg <?= ($user['environment'] ?? 'live') === 'live' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' ?> font-data-mono text-xs font-bold">
            <span class="w-2.5 h-2.5 rounded-full <?= ($user['environment'] ?? 'live') === 'live' ? 'bg-emerald-500 animate-pulse' : 'bg-amber-500' ?>"></span>
            <span>Active Mode: <?= strtoupper($user['environment'] ?? 'live') ?></span>
        </div>
        <form action="/settings/toggle-mode" method="POST">
            <input type="hidden" name="csrf_token" value="<?= Auth::generateCsrfToken() ?>">
            <button type="submit" class="px-3 py-1 bg-surface-container-high hover:bg-surface-container-highest text-on-surface font-body-sm text-xs font-bold rounded-lg border border-outline-variant transition-colors cursor-pointer flex items-center gap-1.5">
                <span class="material-symbols-outlined text-[16px]">sync_alt</span>
                <span>Switch to <?= ($user['environment'] ?? 'live') === 'live' ? 'Test' : 'Live' ?> Mode</span>
            </button>
        </form>
    </div>
</div>

<!-- Tab Navigation Bar -->
<div class="flex items-center gap-2 border-b border-outline-variant mb-8 overflow-x-auto pb-1">
    <button onclick="switchSettingsTab('profileTab', this)" class="settings-tab-btn px-4 py-2.5 border-b-2 border-primary text-primary font-bold text-sm flex items-center gap-2 transition-all cursor-pointer">
        <span class="material-symbols-outlined text-[18px]">storefront</span>
        <span>Business Profile</span>
    </button>
    <button onclick="switchSettingsTab('apiTab', this)" class="settings-tab-btn px-4 py-2.5 border-b-2 border-transparent text-on-surface-variant hover:text-on-surface font-semibold text-sm flex items-center gap-2 transition-all cursor-pointer">
        <span class="material-symbols-outlined text-[18px]">key</span>
        <span>API Credentials & Webhooks</span>
    </button>
    <button onclick="switchSettingsTab('teamTab', this)" class="settings-tab-btn px-4 py-2.5 border-b-2 border-transparent text-on-surface-variant hover:text-on-surface font-semibold text-sm flex items-center gap-2 transition-all cursor-pointer">
        <span class="material-symbols-outlined text-[18px]">group</span>
        <span>Team & Permissions</span>
    </button>
    <button onclick="switchSettingsTab('securityTab', this)" class="settings-tab-btn px-4 py-2.5 border-b-2 border-transparent text-on-surface-variant hover:text-on-surface font-semibold text-sm flex items-center gap-2 transition-all cursor-pointer">
        <span class="material-symbols-outlined text-[18px]">shield</span>
        <span>Security & Password</span>
    </button>
</div>

<!-- TAB 1: Business Profile -->
<div id="profileTab" class="settings-tab-content block">
    <div class="bg-surface-container-lowest rounded-xl border border-outline-variant p-6 mb-8 shadow-sm">
        <div class="mb-6 pb-4 border-b border-outline-variant">
            <h3 class="font-headline-md text-headline-md text-on-surface font-bold mb-1">Registered Business Profile</h3>
            <p class="font-body-sm text-xs text-on-surface-variant">Update organization information, contact phone numbers, and physical operational address.</p>
        </div>

        <form action="/settings/update-profile" method="POST">
            <input type="hidden" name="csrf_token" value="<?= Auth::generateCsrfToken() ?>">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block font-body-sm text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-1.5">Business / Legal Name *</label>
                    <input type="text" name="name" class="w-full px-3.5 py-2.5 bg-surface-container-low border border-outline-variant rounded-xl font-body-sm text-sm text-on-surface font-bold focus:bg-surface focus:ring-2 focus:ring-secondary" value="<?= htmlspecialchars($merchant['name'] ?? $user['merchant_name'] ?? 'Gazoma Tech') ?>" required>
                </div>

                <div>
                    <label class="block font-body-sm text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-1.5">Primary Contact Email *</label>
                    <input type="email" name="email" class="w-full px-3.5 py-2.5 bg-surface-container-low border border-outline-variant rounded-xl font-body-sm text-sm text-on-surface font-data-mono focus:bg-surface focus:ring-2 focus:ring-secondary" value="<?= htmlspecialchars($merchant['email'] ?? $user['email'] ?? 'admin@gazomapay.com') ?>" required>
                </div>

                <div>
                    <label class="block font-body-sm text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-1.5">Phone Number</label>
                    <input type="text" name="phone" class="w-full px-3.5 py-2.5 bg-surface-container-low border border-outline-variant rounded-xl font-body-sm text-sm text-on-surface font-data-mono focus:bg-surface focus:ring-2 focus:ring-secondary" value="<?= htmlspecialchars($merchant['phone'] ?? '+233 24 123 4567') ?>">
                </div>

                <div>
                    <label class="block font-body-sm text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-1.5">Business Structure Type</label>
                    <select name="business_type" class="w-full px-3.5 py-2.5 bg-surface-container-low border border-outline-variant rounded-xl font-body-sm text-sm text-on-surface focus:bg-surface focus:ring-2 focus:ring-secondary cursor-pointer">
                        <option value="limited_company" <?= ($merchant['business_type'] ?? '') === 'limited_company' ? 'selected' : '' ?>>Limited Liability Company (LLC)</option>
                        <option value="sole_proprietorship" <?= ($merchant['business_type'] ?? '') === 'sole_proprietorship' ? 'selected' : '' ?>>Sole Proprietorship</option>
                        <option value="partnership" <?= ($merchant['business_type'] ?? '') === 'partnership' ? 'selected' : '' ?>>Partnership</option>
                        <option value="non_profit" <?= ($merchant['business_type'] ?? '') === 'non_profit' ? 'selected' : '' ?>>Non-Profit / NGO</option>
                    </select>
                </div>

                <div>
                    <label class="block font-body-sm text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-1.5">Primary Settlement Currency</label>
                    <input type="text" class="w-full px-3.5 py-2.5 bg-surface-container-high border border-outline-variant rounded-xl font-body-sm text-sm text-on-surface-variant font-bold font-data-mono" value="GHS (Ghana Cedi)" readonly>
                </div>

                <div>
                    <label class="block font-body-sm text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-1.5">KYC / KYB Verification Status</label>
                    <div class="px-3.5 py-2 rounded-xl bg-emerald-50 text-emerald-800 border border-emerald-200 font-bold text-xs flex items-center gap-2">
                        <span class="material-symbols-outlined text-[18px] text-emerald-600">verified</span>
                        <span><?= strtoupper($merchant['kyc_status'] ?? 'approved') ?> (Full Payment Processing Enabled)</span>
                    </div>
                </div>

                <div class="md:col-span-2">
                    <label class="block font-body-sm text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-1.5">Physical Business Address</label>
                    <input type="text" name="address" class="w-full px-3.5 py-2.5 bg-surface-container-low border border-outline-variant rounded-xl font-body-sm text-sm text-on-surface focus:bg-surface focus:ring-2 focus:ring-secondary" value="<?= htmlspecialchars($merchant['address'] ?? '15 Independence Avenue, Ridge, Accra, Ghana') ?>">
                </div>
            </div>

            <div class="flex justify-end pt-4 border-t border-outline-variant">
                <button type="submit" class="px-6 py-2.5 bg-primary text-on-primary font-body-sm font-bold text-xs rounded-xl hover:bg-primary/90 transition-colors shadow-sm cursor-pointer flex items-center gap-2">
                    <span class="material-symbols-outlined text-[16px]">save</span>
                    <span>Save Profile Changes</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- TAB 2: API Credentials & Webhook Endpoints -->
<div id="apiTab" class="settings-tab-content hidden">
    <!-- Webhook URL Configuration Card -->
    <div class="bg-surface-container-lowest rounded-xl border border-outline-variant p-6 mb-8 shadow-sm">
        <div class="mb-6 pb-4 border-b border-outline-variant">
            <h3 class="font-headline-md text-headline-md text-on-surface font-bold mb-1">Webhook Endpoint Configuration</h3>
            <p class="font-body-sm text-xs text-on-surface-variant">Configure your production or staging server URL to receive real-time HTTP POST event webhooks.</p>
        </div>

        <form action="/settings/update-profile" method="POST" class="space-y-4">
            <input type="hidden" name="csrf_token" value="<?= Auth::generateCsrfToken() ?>">
            <input type="hidden" name="name" value="<?= htmlspecialchars($merchant['name'] ?? '') ?>">
            <input type="hidden" name="email" value="<?= htmlspecialchars($merchant['email'] ?? '') ?>">

            <div>
                <label class="block font-body-sm text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-1.5">Webhook Notification URL</label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-on-surface-variant text-[18px]">webhook</span>
                    <input type="url" name="webhook_url" value="<?= htmlspecialchars($merchant['webhook_url'] ?? 'https://api.yourdomain.com/webhooks/gazomapay') ?>" class="w-full pl-10 pr-4 py-2.5 bg-surface-container-low border border-outline-variant rounded-xl font-body-sm font-data-mono text-sm text-on-surface focus:bg-surface focus:ring-2 focus:ring-secondary" placeholder="https://yourdomain.com/api/webhook" required>
                </div>
            </div>

            <div class="p-4 bg-surface-container-low rounded-xl border border-outline-variant/60 font-body-sm text-xs text-on-surface-variant space-y-1">
                <div class="font-bold text-on-surface">HMAC Signature Verification:</div>
                <p>All webhook requests sent by Gazoma Pay include a <code class="font-data-mono bg-surface-container-high px-1.5 py-0.5 rounded text-primary">X-Gazoma-Signature</code> header generated using SHA-256 HMAC of your webhook secret.</p>
            </div>

            <div class="flex justify-end pt-2">
                <button type="submit" class="px-5 py-2 bg-primary text-on-primary font-body-sm font-bold text-xs rounded-xl hover:bg-primary/90 shadow-sm cursor-pointer flex items-center gap-2">
                    <span class="material-symbols-outlined text-[16px]">save</span>
                    <span>Save Webhook URL</span>
                </button>
            </div>
        </form>
    </div>

    <!-- API Keys & Webhook Secret Management Card -->
    <div class="bg-surface-container-lowest rounded-xl border border-outline-variant p-6 mb-8 shadow-sm">
        <div class="flex justify-between items-center mb-6 pb-4 border-b border-outline-variant">
            <div>
                <h3 class="font-headline-md text-headline-md text-on-surface font-bold mb-1">API Keys & Signing Secrets</h3>
                <p class="font-body-sm text-xs text-on-surface-variant">Use these keys to authenticate REST API calls and verify inbound webhook payloads.</p>
            </div>
            <form action="/settings/rotate-keys" method="POST" onsubmit="return confirm('Are you sure you want to regenerate all API keys? Previous keys will be invalidated!')">
                <input type="hidden" name="csrf_token" value="<?= Auth::generateCsrfToken() ?>">
                <button type="submit" class="px-3.5 py-2 bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-xs rounded-xl border border-rose-200 transition-colors flex items-center gap-1.5 cursor-pointer">
                    <span class="material-symbols-outlined text-[16px]">refresh</span>
                    <span>Roll & Rotate API Keys</span>
                </button>
            </form>
        </div>

        <div class="space-y-6">
            <!-- Test Mode Credentials -->
            <div class="p-4 bg-amber-500/5 border border-amber-200 rounded-xl space-y-4">
                <div class="flex items-center gap-2 text-amber-800 font-bold text-xs uppercase tracking-wider">
                    <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                    <span>Test Environment Keys (Sandbox)</span>
                </div>

                <div>
                    <label class="block font-body-sm text-xs text-on-surface-variant font-semibold mb-1">Test Public Key</label>
                    <div class="flex items-center gap-2">
                        <input type="text" readonly value="<?= htmlspecialchars($merchant['test_public_key'] ?? 'pk_test_89234710928374') ?>" class="w-full px-3.5 py-2 bg-surface border border-outline-variant rounded-xl font-data-mono text-xs text-on-surface">
                        <button type="button" onclick="navigator.clipboard.writeText('<?= htmlspecialchars($merchant['test_public_key'] ?? 'pk_test_89234710928374') ?>'); alert('Test Public Key copied!')" class="px-3 py-2 bg-surface-container-high hover:bg-surface-container-highest text-on-surface font-bold text-xs rounded-xl border border-outline-variant cursor-pointer">Copy</button>
                    </div>
                </div>

                <div>
                    <label class="block font-body-sm text-xs text-on-surface-variant font-semibold mb-1">Test Secret Key</label>
                    <div class="flex items-center gap-2">
                        <input type="password" id="testSecretInput" readonly value="<?= htmlspecialchars($merchant['test_secret_key'] ?? 'sk_test_892374982374982374') ?>" class="w-full px-3.5 py-2 bg-surface border border-outline-variant rounded-xl font-data-mono text-xs text-on-surface">
                        <button type="button" onclick="toggleSecretVisibility('testSecretInput')" class="px-3 py-2 bg-surface-container-high hover:bg-surface-container-highest text-on-surface font-bold text-xs rounded-xl border border-outline-variant cursor-pointer">Show</button>
                    </div>
                </div>
            </div>

            <!-- Live Production Credentials -->
            <div class="p-4 bg-emerald-500/5 border border-emerald-200 rounded-xl space-y-4">
                <div class="flex items-center gap-2 text-emerald-800 font-bold text-xs uppercase tracking-wider">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>Live Production Environment Keys</span>
                </div>

                <div>
                    <label class="block font-body-sm text-xs text-on-surface-variant font-semibold mb-1">Live Public Key</label>
                    <div class="flex items-center gap-2">
                        <input type="text" readonly value="<?= htmlspecialchars($merchant['live_public_key'] ?? 'pk_live_77283910293847') ?>" class="w-full px-3.5 py-2 bg-surface border border-outline-variant rounded-xl font-data-mono text-xs text-on-surface font-bold">
                        <button type="button" onclick="navigator.clipboard.writeText('<?= htmlspecialchars($merchant['live_public_key'] ?? 'pk_live_77283910293847') ?>'); alert('Live Public Key copied!')" class="px-3 py-2 bg-surface-container-high hover:bg-surface-container-highest text-on-surface font-bold text-xs rounded-xl border border-outline-variant cursor-pointer">Copy</button>
                    </div>
                </div>

                <div>
                    <label class="block font-body-sm text-xs text-on-surface-variant font-semibold mb-1">Live Secret Key</label>
                    <div class="flex items-center gap-2">
                        <input type="password" id="liveSecretInput" readonly value="<?= htmlspecialchars($merchant['live_secret_key'] ?? 'sk_live_992837498237498237') ?>" class="w-full px-3.5 py-2 bg-surface border border-outline-variant rounded-xl font-data-mono text-xs text-on-surface">
                        <button type="button" onclick="toggleSecretVisibility('liveSecretInput')" class="px-3 py-2 bg-surface-container-high hover:bg-surface-container-highest text-on-surface font-bold text-xs rounded-xl border border-outline-variant cursor-pointer">Show</button>
                    </div>
                </div>

                <div>
                    <label class="block font-body-sm text-xs text-on-surface-variant font-semibold mb-1">Webhook Signing Secret</label>
                    <div class="flex items-center gap-2">
                        <input type="text" readonly value="<?= htmlspecialchars($merchant['webhook_secret'] ?? 'whsec_77283910293847102938') ?>" class="w-full px-3.5 py-2 bg-surface border border-outline-variant rounded-xl font-data-mono text-xs text-secondary font-bold">
                        <button type="button" onclick="navigator.clipboard.writeText('<?= htmlspecialchars($merchant['webhook_secret'] ?? 'whsec_77283910293847102938') ?>'); alert('Webhook Secret copied!')" class="px-3 py-2 bg-surface-container-high hover:bg-surface-container-highest text-on-surface font-bold text-xs rounded-xl border border-outline-variant cursor-pointer">Copy</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- TAB 3: Team & Permissions -->
<div id="teamTab" class="settings-tab-content hidden">
    <div class="bg-surface-container-lowest rounded-xl border border-outline-variant overflow-hidden shadow-sm">
        <div class="p-6 border-b border-outline-variant flex justify-between items-center">
            <div>
                <h3 class="font-headline-md text-headline-md text-on-surface font-bold">Team Management & Access Roles</h3>
                <p class="font-body-sm text-xs text-on-surface-variant">Invite staff members and manage Role-Based Access Control (RBAC) permissions.</p>
            </div>
            <button class="px-4 py-2 bg-primary text-on-primary font-body-sm text-xs font-bold rounded-xl hover:bg-primary/90 transition-colors flex items-center gap-1.5 cursor-pointer shadow-sm" onclick="openModal('addTeamModal')">
                <span class="material-symbols-outlined text-[18px]">add</span>
                <span>Invite Staff Member</span>
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-outline-variant bg-surface-container-low/80 font-label-caps text-label-caps text-on-surface-variant uppercase text-xs">
                        <th class="py-3.5 px-6">User Name</th>
                        <th class="py-3.5 px-6">Email Address</th>
                        <th class="py-3.5 px-6">Role</th>
                        <th class="py-3.5 px-6">Account Status</th>
                        <th class="py-3.5 px-6">Last Active</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant font-body-sm text-body-sm">
                    <?php foreach ($team as $member): ?>
                        <tr class="hover:bg-surface-container-low/50 transition-colors">
                            <td class="py-4 px-6 font-bold text-on-surface"><?= htmlspecialchars($member['name']) ?></td>
                            <td class="py-4 px-6 text-on-surface-variant font-data-mono text-xs"><?= htmlspecialchars($member['email']) ?></td>
                            <td class="py-4 px-6">
                                <span class="px-2.5 py-1 rounded bg-secondary/10 text-secondary font-label-caps text-[11px] uppercase font-bold"><?= htmlspecialchars($member['role']) ?></span>
                            </td>
                            <td class="py-4 px-6"><?= Format::statusBadge($member['status']) ?></td>
                            <td class="py-4 px-6 text-on-surface-variant font-data-mono text-xs"><?= Format::date($member['last_login']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- TAB 4: Security & Password -->
<div id="securityTab" class="settings-tab-content hidden">
    <div class="bg-surface-container-lowest rounded-xl border border-outline-variant p-6 mb-8 shadow-sm max-w-2xl">
        <div class="mb-6 pb-4 border-b border-outline-variant">
            <h3 class="font-headline-md text-headline-md text-on-surface font-bold mb-1">Update Account Password</h3>
            <p class="font-body-sm text-xs text-on-surface-variant">Ensure your account uses a strong password (minimum 8 characters with numbers and special symbols).</p>
        </div>

        <form action="/settings/update-password" method="POST" class="space-y-4">
            <input type="hidden" name="csrf_token" value="<?= Auth::generateCsrfToken() ?>">

            <div>
                <label class="block font-body-sm text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-1.5">Current Password *</label>
                <input type="password" name="current_password" class="w-full px-3.5 py-2.5 bg-surface-container-low border border-outline-variant rounded-xl font-body-sm text-sm text-on-surface focus:bg-surface focus:ring-2 focus:ring-secondary" required>
            </div>

            <div>
                <label class="block font-body-sm text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-1.5">New Password *</label>
                <input type="password" name="new_password" class="w-full px-3.5 py-2.5 bg-surface-container-low border border-outline-variant rounded-xl font-body-sm text-sm text-on-surface focus:bg-surface focus:ring-2 focus:ring-secondary" required>
            </div>

            <div>
                <label class="block font-body-sm text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-1.5">Confirm New Password *</label>
                <input type="password" name="confirm_password" class="w-full px-3.5 py-2.5 bg-surface-container-low border border-outline-variant rounded-xl font-body-sm text-sm text-on-surface focus:bg-surface focus:ring-2 focus:ring-secondary" required>
            </div>

            <div class="flex justify-end pt-3">
                <button type="submit" class="px-5 py-2.5 bg-primary text-on-primary font-body-sm font-bold text-xs rounded-xl hover:bg-primary/90 shadow-sm cursor-pointer flex items-center gap-2">
                    <span class="material-symbols-outlined text-[16px]">lock_reset</span>
                    <span>Update Account Password</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Add Team Member Modal -->
<div class="modal-overlay hidden fixed inset-0 bg-slate-950/60 backdrop-blur-sm z-50 flex items-center justify-center p-4" id="addTeamModal">
    <div class="bg-surface-container-lowest border border-outline-variant rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4">
        <div class="flex justify-between items-center border-b border-outline-variant pb-3">
            <h3 class="font-headline-lg text-lg font-bold text-on-surface">Invite Staff Member</h3>
            <button class="text-on-surface-variant hover:text-on-surface text-xl cursor-pointer" onclick="closeModal('addTeamModal')">&times;</button>
        </div>
        <form action="/settings/team/add" method="POST" class="space-y-4">
            <input type="hidden" name="csrf_token" value="<?= Auth::generateCsrfToken() ?>">
            <div>
                <label class="block font-body-sm text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-1">Full Name *</label>
                <input type="text" name="name" class="w-full px-3.5 py-2.5 bg-surface-container-low border border-outline-variant rounded-xl font-body-sm text-sm text-on-surface focus:bg-surface focus:ring-2 focus:ring-secondary" placeholder="Kwame Nkrumah" required>
            </div>
            <div>
                <label class="block font-body-sm text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-1">Email Address *</label>
                <input type="email" name="email" class="w-full px-3.5 py-2.5 bg-surface-container-low border border-outline-variant rounded-xl font-body-sm text-sm text-on-surface font-data-mono focus:bg-surface focus:ring-2 focus:ring-secondary" placeholder="kwame@gazomatech.com" required>
            </div>
            <div>
                <label class="block font-body-sm text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-1">Role Level *</label>
                <select name="role" class="w-full px-3.5 py-2.5 bg-surface-container-low border border-outline-variant rounded-xl font-body-sm text-sm text-on-surface focus:bg-surface focus:ring-2 focus:ring-secondary cursor-pointer" required>
                    <option value="admin">Admin (Full Access)</option>
                    <option value="finance">Finance Manager (Settlements & Refunds)</option>
                    <option value="developer">Developer (API Keys & Webhooks)</option>
                    <option value="support">Support Agent (Disputes & Viewers)</option>
                    <option value="viewer">Viewer (Read-Only)</option>
                </select>
            </div>
            <div class="flex justify-end gap-3 pt-3 border-t border-outline-variant">
                <button type="button" class="px-4 py-2 bg-surface-container-high text-on-surface font-body-sm text-xs font-bold rounded-xl hover:bg-surface-container-highest" onclick="closeModal('addTeamModal')">Cancel</button>
                <button type="submit" class="px-5 py-2 bg-primary text-on-primary font-body-sm text-xs font-bold rounded-xl hover:bg-primary/90 shadow-sm cursor-pointer">Invite Staff Member</button>
            </div>
        </form>
    </div>
</div>

<script>
function switchSettingsTab(tabId, btn) {
    document.querySelectorAll('.settings-tab-content').forEach(el => {
        el.classList.add('hidden');
        el.classList.remove('block');
    });
    document.querySelectorAll('.settings-tab-btn').forEach(el => {
        el.classList.remove('border-primary', 'text-primary', 'font-bold');
        el.classList.add('border-transparent', 'text-on-surface-variant', 'font-semibold');
    });

    const target = document.getElementById(tabId);
    if (target) {
        target.classList.remove('hidden');
        target.classList.add('block');
    }
    if (btn) {
        btn.classList.remove('border-transparent', 'text-on-surface-variant', 'font-semibold');
        btn.classList.add('border-primary', 'text-primary', 'font-bold');
    }
}

function toggleSecretVisibility(inputId) {
    const input = document.getElementById(inputId);
    if (input) {
        input.type = (input.type === 'password') ? 'text' : 'password';
    }
}

function openModal(id) {
    const m = document.getElementById(id);
    if (m) {
        m.classList.remove('hidden');
    }
}
function closeModal(id) {
    const m = document.getElementById(id);
    if (m) {
        m.classList.add('hidden');
    }
}
</script>
