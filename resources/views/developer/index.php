<div class="nav-tabs">
    <a href="javascript:void(0)" onclick="switchDevTab('keys')" id="tab-keys" class="tab-item active">API Keys</a>
    <a href="javascript:void(0)" onclick="switchDevTab('webhooks')" id="tab-webhooks" class="tab-item">Webhooks & Logs</a>
    <a href="javascript:void(0)" onclick="switchDevTab('docs')" id="tab-docs" class="tab-item">API Documentation</a>
</div>

<!-- Section 1: API Keys -->
<div id="section-keys">
    <div class="toolbar">
        <div style="font-size: 16px; font-weight: 700;">Merchant API Keys</div>
        <button class="btn btn-primary" onclick="openModal('createKeyModal')">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Generate New Key
        </button>
    </div>

    <div class="table-container" style="margin-bottom: 32px;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Key Name</th>
                    <th>Environment</th>
                    <th>Public Key</th>
                    <th>Secret Key Preview</th>
                    <th>Status</th>
                    <th>Created</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($apiKeys as $k): ?>
                    <tr>
                        <td style="font-weight: 700;"><?= htmlspecialchars($k['name']) ?></td>
                        <td>
                            <span class="badge <?= $k['key_type'] === 'live' ? 'badge-success' : 'badge-warning' ?>">
                                <?= strtoupper($k['key_type']) ?>
                            </span>
                        </td>
                        <td style="font-family: monospace; color: var(--primary-blue); font-size: 13px;">
                            <?= htmlspecialchars($k['public_key']) ?>
                            <button class="btn btn-outline btn-sm" onclick="copyToClipboard('<?= $k['public_key'] ?>', 'Public key copied!')" style="margin-left: 6px;">Copy</button>
                        </td>
                        <td style="font-family: monospace; color: var(--text-muted); font-size: 13px;"><?= htmlspecialchars($k['secret_key_preview']) ?></td>
                        <td><?= Format::statusBadge($k['status']) ?></td>
                        <td style="color: var(--text-muted); font-size: 13px;"><?= Format::dateShort($k['created_at']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Section 2: Webhooks -->
<div id="section-webhooks" style="display: none;">
    <div class="toolbar">
        <div style="font-size: 16px; font-weight: 700;">Webhook Endpoints</div>
        <button class="btn btn-primary" onclick="openModal('addWebhookModal')">Add Webhook Endpoint</button>
    </div>

    <div class="table-container" style="margin-bottom: 32px;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>URL Endpoint</th>
                    <th>Signing Secret</th>
                    <th>Subscribed Events</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($webhooks)): ?>
                    <tr><td colspan="4" style="text-align: center; padding: 30px;">No webhook endpoints configured.</td></tr>
                <?php else: foreach ($webhooks as $w): ?>
                    <tr>
                        <td style="font-family: monospace; font-weight: 600;"><?= htmlspecialchars($w['url']) ?></td>
                        <td style="font-family: monospace; color: var(--text-muted);"><?= htmlspecialchars($w['secret']) ?></td>
                        <td><code><?= htmlspecialchars(implode(', ', json_decode($w['events'], true) ?: [])) ?></code></td>
                        <td><?= Format::statusBadge($w['status']) ?></td>
                    </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>

    <h3 style="font-size: 16px; font-weight: 700; margin-bottom: 16px;">Recent Webhook Delivery Logs</h3>
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Event</th>
                    <th>Endpoint</th>
                    <th>HTTP Code</th>
                    <th>Attempts</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th style="text-align: right;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($webhookLogs)): ?>
                    <tr><td colspan="7" style="text-align: center; padding: 30px;">No webhook delivery logs.</td></tr>
                <?php else: foreach ($webhookLogs as $l): ?>
                    <tr>
                        <td style="font-weight: 700; font-family: monospace; color: var(--primary-blue);"><?= htmlspecialchars($l['event_type']) ?></td>
                        <td style="font-family: monospace; font-size: 12px;"><?= htmlspecialchars($l['url']) ?></td>
                        <td><span class="badge badge-success"><?= $l['response_code'] ?> OK</span></td>
                        <td><?= $l['attempt_count'] ?></td>
                        <td><?= Format::statusBadge($l['status']) ?></td>
                        <td style="color: var(--text-muted); font-size: 12px;"><?= Format::date($l['created_at']) ?></td>
                        <td style="text-align: right;">
                            <a href="/developer/webhook-log/<?= $l['id'] ?>/retry" class="btn btn-outline btn-sm">Retry Delivery</a>
                        </td>
                    </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Section 3: API Documentation -->
<div id="section-docs" style="display: none;">
    <div class="card" style="margin-bottom: 24px;">
        <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 8px;">Gazoma Pay REST API Reference (v1)</h3>
        <p style="color: var(--text-muted); font-size: 14px; margin-bottom: 20px;">Use your merchant API key in the HTTP Authorization header: <code>Authorization: Bearer YOUR_API_KEY</code></p>

        <div style="background: #0f172a; color: #f8fafc; border-radius: 8px; padding: 20px; font-family: monospace; font-size: 13px; line-height: 1.6; margin-bottom: 20px;">
            <div style="color: #93c5fd; font-weight: 700;"># Create Payment Charge (POST /api/v1/payments)</div>
            curl -X POST http://localhost:8000/api/v1/payments \<br>
            &nbsp;&nbsp;-H "Authorization: Bearer gzm_live_pub_9a8b7c6d5e4f3a2b" \<br>
            &nbsp;&nbsp;-H "Content-Type: application/json" \<br>
            &nbsp;&nbsp;-d '{<br>
            &nbsp;&nbsp;&nbsp;&nbsp;"amount": 250.00,<br>
            &nbsp;&nbsp;&nbsp;&nbsp;"customer_name": "Ama Serwaa",<br>
            &nbsp;&nbsp;&nbsp;&nbsp;"customer_email": "ama@example.com",<br>
            &nbsp;&nbsp;&nbsp;&nbsp;"payment_method": "card"<br>
            &nbsp;&nbsp;}'
        </div>

        <div style="background: #0f172a; color: #f8fafc; border-radius: 8px; padding: 20px; font-family: monospace; font-size: 13px; line-height: 1.6;">
            <div style="color: #93c5fd; font-weight: 700;"># Get Transaction Status (GET /api/v1/payments/{id})</div>
            curl -X GET http://localhost:8000/api/v1/payments/GZM_00012345 \<br>
            &nbsp;&nbsp;-H "Authorization: Bearer gzm_live_pub_9a8b7c6d5e4f3a2b"
        </div>
    </div>
</div>

<!-- Create Key Modal -->
<div class="modal-overlay" id="createKeyModal">
    <div class="modal-card">
        <div class="modal-header">
            <h3 class="modal-title">Generate API Key</h3>
            <button class="modal-close" onclick="closeModal('createKeyModal')">&times;</button>
        </div>
        <form action="/developer/api-keys/create" method="POST">
            <input type="hidden" name="csrf_token" value="<?= Auth::generateCsrfToken() ?>">
            <div class="form-group">
                <label class="form-label">Key Name</label>
                <input type="text" name="name" class="form-control" placeholder="Production Server Key" required>
            </div>
            <div class="form-group">
                <label class="form-label">Environment</label>
                <select name="key_type" class="form-control">
                    <option value="live">Live Mode</option>
                    <option value="test">Test Mode</option>
                </select>
            </div>
            <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px;">
                <button type="button" class="btn btn-outline" onclick="closeModal('createKeyModal')">Cancel</button>
                <button type="submit" class="btn btn-primary">Generate Keys</button>
            </div>
        </form>
    </div>
</div>

<!-- Add Webhook Modal -->
<div class="modal-overlay" id="addWebhookModal">
    <div class="modal-card">
        <div class="modal-header">
            <h3 class="modal-title">Add Webhook Endpoint</h3>
            <button class="modal-close" onclick="closeModal('addWebhookModal')">&times;</button>
        </div>
        <form action="/developer/webhooks/create" method="POST">
            <input type="hidden" name="csrf_token" value="<?= Auth::generateCsrfToken() ?>">
            <div class="form-group">
                <label class="form-label">Endpoint URL</label>
                <input type="url" name="url" class="form-control" placeholder="https://yourdomain.com/webhooks/gazoma" required>
            </div>
            <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px;">
                <button type="button" class="btn btn-outline" onclick="closeModal('addWebhookModal')">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Endpoint</button>
            </div>
        </form>
    </div>
</div>

<script>
function switchDevTab(tab) {
    document.querySelectorAll('.tab-item').forEach(t => t.classList.remove('active'));
    document.getElementById('tab-' + tab).classList.add('active');
    
    document.getElementById('section-keys').style.display = (tab === 'keys') ? 'block' : 'none';
    document.getElementById('section-webhooks').style.display = (tab === 'webhooks') ? 'block' : 'none';
    document.getElementById('section-docs').style.display = (tab === 'docs') ? 'block' : 'none';
}
</script>
