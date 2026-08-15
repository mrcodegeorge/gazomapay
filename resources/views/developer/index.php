<!-- Developer Portal Sub-Navigation Tabs -->
<div class="flex items-center gap-2 border-b border-outline-variant mb-8">
    <button onclick="switchDevTab('overview')" id="tab-overview" class="dev-tab px-4 py-3 font-body-md text-body-md font-semibold text-secondary border-b-2 border-secondary flex items-center gap-2 transition-colors cursor-pointer">
        <span class="material-symbols-outlined">dashboard</span>
        Overview
    </button>
    <button onclick="switchDevTab('keys')" id="tab-keys" class="dev-tab px-4 py-3 font-body-md text-body-md font-medium text-on-surface-variant hover:text-on-surface flex items-center gap-2 transition-colors cursor-pointer">
        <span class="material-symbols-outlined">key</span>
        API Keys
    </button>
    <button onclick="switchDevTab('webhooks')" id="tab-webhooks" class="dev-tab px-4 py-3 font-body-md text-body-md font-medium text-on-surface-variant hover:text-on-surface flex items-center gap-2 transition-colors cursor-pointer">
        <span class="material-symbols-outlined">webhook</span>
        Webhooks & Logs
    </button>
    <button onclick="switchDevTab('docs')" id="tab-docs" class="dev-tab px-4 py-3 font-body-md text-body-md font-medium text-on-surface-variant hover:text-on-surface flex items-center gap-2 transition-colors cursor-pointer">
        <span class="material-symbols-outlined">menu_book</span>
        API Reference
    </button>
</div>

<!-- ========================================== -->
<!-- SECTION 1: DEVELOPER OVERVIEW -->
<!-- ========================================== -->
<div id="section-overview">
    <!-- API Status Banner -->
    <div class="glass-card rounded-xl p-4 mb-8 flex items-center justify-between border-l-4 border-l-[#10B981]">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-full bg-[#10B981]/10 flex items-center justify-center text-[#10B981]">
                <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">check_circle</span>
            </div>
            <div>
                <h3 class="font-body-md text-body-md font-medium text-on-surface">All Systems Operational</h3>
                <p class="font-body-sm text-body-sm text-on-surface-variant">Production REST API v1 & Sandbox Gateway running normally.</p>
            </div>
        </div>
        <a class="font-label-caps text-label-caps text-secondary hover:underline flex items-center gap-1" href="javascript:void(0)" onclick="switchDevTab('docs')">
            View API Reference <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
        </a>
    </div>

    <!-- Quick Stats Bento Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
        <!-- Total Requests -->
        <div class="glass-card rounded-xl p-6 flex flex-col justify-between h-36">
            <div class="flex items-center justify-between">
                <span class="font-label-caps text-label-caps text-on-surface-variant uppercase">TOTAL REQUESTS (24H)</span>
                <span class="material-symbols-outlined text-outline">data_usage</span>
            </div>
            <div>
                <div class="font-headline-lg text-headline-lg text-on-surface mb-1 font-semibold">1,248,910</div>
                <div class="flex items-center gap-1 font-body-sm text-body-sm text-[#10B981]">
                    <span class="material-symbols-outlined text-[16px]">trending_up</span>
                    <span>+5.2% vs last week</span>
                </div>
            </div>
        </div>

        <!-- Success Rate -->
        <div class="glass-card rounded-xl p-6 flex flex-col justify-between h-36">
            <div class="flex items-center justify-between">
                <span class="font-label-caps text-label-caps text-on-surface-variant uppercase">SUCCESS RATE</span>
                <span class="material-symbols-outlined text-outline">verified</span>
            </div>
            <div>
                <div class="font-headline-lg text-headline-lg text-on-surface mb-1 font-semibold">99.99%</div>
                <div class="flex items-center gap-1 font-body-sm text-body-sm text-on-surface-variant">
                    <span>SLA Target: 99.95%</span>
                </div>
            </div>
        </div>

        <!-- Average Latency -->
        <div class="glass-card rounded-xl p-6 flex flex-col justify-between h-36">
            <div class="flex items-center justify-between">
                <span class="font-label-caps text-label-caps text-on-surface-variant uppercase">AVG LATENCY</span>
                <span class="material-symbols-outlined text-outline">timer</span>
            </div>
            <div>
                <div class="font-headline-lg text-headline-lg text-on-surface mb-1 font-data-mono font-semibold">42ms</div>
                <div class="flex items-center gap-1 font-body-sm text-body-sm text-on-surface-variant">
                    <span>p99: 110ms</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Secret Keys Preview Card -->
    <h2 class="font-headline-md text-headline-md text-on-surface mb-4">Authentication Overview</h2>
    <div class="glass-card rounded-xl p-0 mb-10 overflow-hidden">
        <div class="p-6 border-b border-outline-variant flex justify-between items-center bg-surface-container-lowest">
            <div>
                <h3 class="font-body-lg text-body-lg text-on-surface font-medium">Active API Credentials</h3>
                <p class="font-body-sm text-body-sm text-on-surface-variant">Use your secret keys to authenticate REST requests with <code class="bg-surface-container px-1 rounded font-data-mono">Authorization: Bearer KEY</code>.</p>
            </div>
            <button onclick="switchDevTab('keys')" class="bg-primary text-on-primary px-4 py-2 rounded-lg font-body-sm text-body-sm font-medium hover:bg-on-surface-variant transition-colors cursor-pointer">
                Manage Keys
            </button>
        </div>
        <div class="divide-y divide-outline-variant">
            <?php if (empty($apiKeys)): ?>
                <div class="p-6 text-center text-on-surface-variant font-body-sm">No API keys generated yet. Click "Manage Keys" to generate your first key.</div>
            <?php else: foreach (array_slice($apiKeys, 0, 3) as $k): ?>
                <div class="p-5 flex items-center justify-between hover:bg-surface-container-low transition-colors">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-full bg-surface-variant flex items-center justify-center">
                            <span class="material-symbols-outlined text-on-surface-variant">key</span>
                        </div>
                        <div>
                            <div class="font-body-md text-body-md font-medium text-on-surface"><?= htmlspecialchars($k['name']) ?></div>
                            <div class="font-data-mono text-data-mono text-on-surface-variant mt-0.5 flex items-center gap-2">
                                <?= htmlspecialchars($k['public_key']) ?>
                                <button class="text-secondary hover:text-primary transition-colors cursor-pointer" onclick="copyToClipboard('<?= $k['public_key'] ?>', 'Public key copied!')" title="Copy public key">
                                    <span class="material-symbols-outlined text-[16px]">content_copy</span>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="font-label-caps text-label-caps px-2.5 py-1 <?= $k['key_type'] === 'live' ? 'bg-[#10B981]/10 text-[#10B981]' : 'bg-surface-variant text-on-surface-variant' ?> rounded-full uppercase">
                            <?= htmlspecialchars($k['key_type']) ?>
                        </span>
                        <div class="font-body-sm text-body-sm text-on-surface-variant mt-1"><?= Format::dateShort($k['created_at']) ?></div>
                    </div>
                </div>
            <?php endforeach; endif; ?>
        </div>
    </div>

    <!-- Getting Started Cards Grid -->
    <h2 class="font-headline-md text-headline-md text-on-surface mb-4">Developer Tools & Resources</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <a class="glass-card rounded-xl p-6 flex items-start gap-4 hover:-translate-y-1 transition-transform duration-200" href="javascript:void(0)" onclick="switchDevTab('docs')">
            <div class="w-12 h-12 rounded-lg bg-surface-variant flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-on-surface-variant text-[24px]">menu_book</span>
            </div>
            <div>
                <h3 class="font-body-lg text-body-lg text-on-surface font-medium mb-1">API Documentation</h3>
                <p class="font-body-sm text-body-sm text-on-surface-variant mb-4">Complete endpoints reference, authentication rules, parameters, and response schemas.</p>
                <span class="font-label-caps text-label-caps text-secondary flex items-center gap-1">
                    EXPLORE DOCS <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
                </span>
            </div>
        </a>

        <a class="glass-card rounded-xl p-6 flex items-start gap-4 hover:-translate-y-1 transition-transform duration-200" href="javascript:void(0)" onclick="switchDevTab('webhooks')">
            <div class="w-12 h-12 rounded-lg bg-surface-variant flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-on-surface-variant text-[24px]">webhook</span>
            </div>
            <div>
                <h3 class="font-body-lg text-body-lg text-on-surface font-medium mb-1">Webhooks & Event Logs</h3>
                <p class="font-body-sm text-body-sm text-on-surface-variant mb-4">Configure HTTPS webhook endpoints to listen for live payment events with retry support.</p>
                <span class="font-label-caps text-label-caps text-secondary flex items-center gap-1">
                    CONFIGURE WEBHOOKS <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
                </span>
            </div>
        </a>
    </div>
</div>

<!-- ========================================== -->
<!-- SECTION 2: API KEYS -->
<!-- ========================================== -->
<div id="section-keys" style="display: none;">
    <!-- Warning Banner -->
    <div class="mb-8 p-4 bg-error-container/20 border border-error-container rounded-xl flex items-start gap-4">
        <span class="material-symbols-outlined text-error mt-0.5">warning</span>
        <div>
            <h3 class="font-body-md text-body-md font-bold text-on-surface">Keep your secret keys secure</h3>
            <p class="font-body-sm text-body-sm text-on-surface-variant mt-1">Do not expose your secret keys in client-side code (like frontend JS or mobile apps). Always invoke Gazoma Pay endpoints strictly from your backend application server.</p>
        </div>
    </div>

    <!-- Action Toolbar -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="font-headline-md text-headline-md text-on-surface">API Credentials & Keys</h2>
            <p class="font-body-sm text-body-sm text-on-surface-variant">Active publishable and secret keys registered to your merchant account.</p>
        </div>
        <button class="bg-primary text-on-primary px-5 py-2.5 rounded-lg font-body-md text-body-md font-semibold hover:bg-primary/90 transition-colors flex items-center gap-2 cursor-pointer shadow-sm" onclick="openModal('createKeyModal')">
            <span class="material-symbols-outlined text-sm">add</span>
            Generate New Key
        </button>
    </div>

    <!-- API Keys List Table Card -->
    <div class="bg-surface-container-lowest border border-outline-variant rounded-xl overflow-hidden mb-8">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-surface-container-low border-b border-outline-variant">
                    <th class="py-3 px-6 font-label-caps text-label-caps text-on-surface-variant uppercase">Key Name</th>
                    <th class="py-3 px-6 font-label-caps text-label-caps text-on-surface-variant uppercase">Mode</th>
                    <th class="py-3 px-6 font-label-caps text-label-caps text-on-surface-variant uppercase">Public Key</th>
                    <th class="py-3 px-6 font-label-caps text-label-caps text-on-surface-variant uppercase">Secret Preview</th>
                    <th class="py-3 px-6 font-label-caps text-label-caps text-on-surface-variant uppercase">Status</th>
                    <th class="py-3 px-6 font-label-caps text-label-caps text-on-surface-variant uppercase text-right">Created</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant font-body-sm text-body-sm">
                <?php if (empty($apiKeys)): ?>
                    <tr>
                        <td colspan="6" class="py-8 text-center text-on-surface-variant">No API keys generated yet. Click "Generate New Key" above.</td>
                    </tr>
                <?php else: foreach ($apiKeys as $k): ?>
                    <tr class="hover:bg-surface-container-low/50 transition-colors">
                        <td class="py-4 px-6 font-medium text-on-surface"><?= htmlspecialchars($k['name']) ?></td>
                        <td class="py-4 px-6">
                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold <?= $k['key_type'] === 'live' ? 'bg-[#10B981]/10 text-[#10B981]' : 'bg-surface-variant text-on-surface-variant' ?>">
                                <?= strtoupper(htmlspecialchars($k['key_type'])) ?>
                            </span>
                        </td>
                        <td class="py-4 px-6 font-data-mono text-secondary">
                            <div class="flex items-center gap-2">
                                <span><?= htmlspecialchars($k['public_key']) ?></span>
                                <button class="text-on-surface-variant hover:text-primary transition-colors cursor-pointer" onclick="copyToClipboard('<?= $k['public_key'] ?>', 'Public Key copied!')" title="Copy">
                                    <span class="material-symbols-outlined text-sm">content_copy</span>
                                </button>
                            </div>
                        </td>
                        <td class="py-4 px-6 font-data-mono text-on-surface-variant"><?= htmlspecialchars($k['secret_key_preview']) ?></td>
                        <td class="py-4 px-6"><?= Format::statusBadge($k['status']) ?></td>
                        <td class="py-4 px-6 text-right text-on-surface-variant"><?= Format::dateShort($k['created_at']) ?></td>
                    </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- ========================================== -->
<!-- SECTION 3: WEBHOOKS & LOGS -->
<!-- ========================================== -->
<div id="section-webhooks" style="display: none;">
    <!-- Header Section -->
    <div class="flex justify-between items-start mb-8">
        <div>
            <h2 class="font-headline-lg text-headline-lg text-on-surface mb-1">Webhooks & Subscriptions</h2>
            <p class="font-body-md text-body-md text-on-surface-variant max-w-2xl">
                Configure HTTPS endpoints to listen for live payment events dispatched by Gazoma Pay.
            </p>
        </div>
        <button class="flex items-center gap-2 bg-primary text-on-primary px-5 py-2.5 rounded-lg font-body-md text-body-md font-semibold hover:bg-primary/90 transition-colors shadow-sm cursor-pointer" onclick="openModal('addWebhookModal')">
            <span class="material-symbols-outlined text-sm">add</span>
            Add Endpoint
        </button>
    </div>

    <!-- Bento Grid Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Endpoints List (Spans 2 columns) -->
        <div class="lg:col-span-2 bg-surface-container-lowest rounded-xl border border-outline-variant overflow-hidden flex flex-col">
            <div class="p-5 border-b border-outline-variant bg-surface-container-low flex justify-between items-center">
                <h3 class="font-headline-md text-headline-md text-on-surface">Configured Endpoints</h3>
                <span class="font-label-caps text-label-caps text-on-surface-variant"><?= count($webhooks) ?> Registered</span>
            </div>
            <div class="flex-1 divide-y divide-outline-variant">
                <?php if (empty($webhooks)): ?>
                    <div class="p-8 text-center text-on-surface-variant">No webhook endpoints configured. Click "Add Endpoint" to create one.</div>
                <?php else: foreach ($webhooks as $w): ?>
                    <div class="p-6 hover:bg-surface-container-low transition-colors">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <div class="flex items-center gap-3 mb-1">
                                    <span class="font-data-mono text-data-mono text-on-surface font-semibold"><?= htmlspecialchars($w['url']) ?></span>
                                    <span class="px-2.5 py-0.5 rounded-full <?= $w['status'] === 'active' ? 'bg-[#10B981]/10 text-[#10B981]' : 'bg-surface-variant text-on-surface-variant' ?> font-label-caps text-label-caps">
                                        <?= ucfirst(htmlspecialchars($w['status'])) ?>
                                    </span>
                                </div>
                                <p class="font-body-sm text-body-sm text-on-surface-variant">Created <?= Format::dateShort($w['created_at']) ?></p>
                            </div>
                        </div>
                        <div class="flex flex-wrap gap-2 mt-3">
                            <?php 
                            $eventsArr = json_decode($w['events'], true) ?: ['payment.success'];
                            foreach ($eventsArr as $ev): 
                            ?>
                                <span class="px-2 py-1 bg-surface-container rounded border border-outline-variant font-data-mono text-[12px] text-on-surface"><?= htmlspecialchars($ev) ?></span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; endif; ?>
            </div>
        </div>

        <!-- Stats / Webhook Secret -->
        <div class="space-y-6">
            <div class="bg-surface-container-lowest rounded-xl border border-outline-variant p-6">
                <h4 class="font-label-caps text-label-caps text-on-surface-variant uppercase mb-3">Delivery Rate (24h)</h4>
                <div class="flex items-baseline gap-2">
                    <span class="font-display text-display text-on-surface font-semibold">99.9%</span>
                    <span class="material-symbols-outlined text-[#10B981] text-sm">trending_up</span>
                </div>
                <div class="w-full bg-surface-variant h-1.5 rounded-full mt-4 overflow-hidden">
                    <div class="bg-[#10B981] w-[99.9%] h-full rounded-full"></div>
                </div>
            </div>

            <div class="bg-surface-container-lowest rounded-xl border border-outline-variant p-6">
                <h4 class="font-label-caps text-label-caps text-on-surface-variant uppercase mb-2">Webhook Signing Secret</h4>
                <p class="font-body-sm text-body-sm text-on-surface-variant mb-3">Paylods are signed using HMAC SHA256 header <code class="font-data-mono text-xs bg-surface-container px-1">X-Gazoma-Signature</code>.</p>
                <div class="flex items-center gap-2 bg-surface-container p-2.5 rounded border border-outline-variant">
                    <span class="font-data-mono text-data-mono text-on-surface flex-1 truncate">
                        <?= !empty($webhooks[0]['secret']) ? htmlspecialchars($webhooks[0]['secret']) : 'whsec_gazoma_default_signing_key' ?>
                    </span>
                    <button class="p-1 hover:bg-surface-variant rounded text-on-surface-variant cursor-pointer" onclick="copyToClipboard('<?= !empty($webhooks[0]['secret']) ? $webhooks[0]['secret'] : 'whsec_gazoma_default_signing_key' ?>', 'Signing secret copied!')" title="Copy secret">
                        <span class="material-symbols-outlined text-sm">content_copy</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Deliveries Table -->
    <div class="bg-surface-container-lowest rounded-xl border border-outline-variant overflow-hidden">
        <div class="p-5 border-b border-outline-variant bg-surface flex justify-between items-center">
            <h3 class="font-headline-md text-headline-md text-on-surface">Recent Delivery Logs</h3>
            <span class="font-body-sm text-body-sm text-on-surface-variant">Last 10 events</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-outline-variant bg-surface-container-low">
                        <th class="py-3 px-6 font-label-caps text-label-caps text-on-surface-variant uppercase">Event Type</th>
                        <th class="py-3 px-6 font-label-caps text-label-caps text-on-surface-variant uppercase">Endpoint</th>
                        <th class="py-3 px-6 font-label-caps text-label-caps text-on-surface-variant uppercase">HTTP Code</th>
                        <th class="py-3 px-6 font-label-caps text-label-caps text-on-surface-variant uppercase">Attempts</th>
                        <th class="py-3 px-6 font-label-caps text-label-caps text-on-surface-variant uppercase">Status</th>
                        <th class="py-3 px-6 font-label-caps text-label-caps text-on-surface-variant uppercase text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant font-body-sm text-body-sm">
                    <?php if (empty($webhookLogs)): ?>
                        <tr><td colspan="6" class="py-8 text-center text-on-surface-variant">No webhook delivery logs recorded yet.</td></tr>
                    <?php else: foreach ($webhookLogs as $l): ?>
                        <tr class="hover:bg-surface-container-low/50 transition-colors h-[60px]">
                            <td class="py-3 px-6 font-data-mono text-secondary font-semibold"><?= htmlspecialchars($l['event_type']) ?></td>
                            <td class="py-3 px-6 font-data-mono text-xs text-on-surface-variant max-w-[240px] truncate"><?= htmlspecialchars($l['url']) ?></td>
                            <td class="py-3 px-6">
                                <span class="px-2.5 py-1 rounded-full <?= $l['response_code'] == 200 ? 'bg-[#10B981]/10 text-[#10B981]' : 'bg-error-container text-on-error-container' ?> font-data-mono text-[12px]">
                                    <?= $l['response_code'] ?> <?= $l['response_code'] == 200 ? 'OK' : 'ERR' ?>
                                </span>
                            </td>
                            <td class="py-3 px-6 text-on-surface-variant"><?= $l['attempt_count'] ?></td>
                            <td class="py-3 px-6"><?= Format::statusBadge($l['status']) ?></td>
                            <td class="py-3 px-6 text-right">
                                <a href="/developer/webhook-log/<?= $l['id'] ?>/retry" class="px-3 py-1 bg-surface-container-high hover:bg-surface-variant text-on-surface font-body-sm text-xs rounded border border-outline-variant transition-colors inline-flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[14px]">autorenew</span> Retry
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- SECTION 4: API DOCUMENTATION -->
<!-- ========================================== -->
<div id="section-docs" style="display: none;">
    <div class="flex flex-col xl:flex-row gap-8">
        <!-- Center Column: Documentation & Endpoints -->
        <div class="flex-1 max-w-[800px] space-y-10">
            <div>
                <h1 class="font-display text-display text-on-surface mb-3">Gazoma Pay REST API v1 Reference</h1>
                <p class="font-body-lg text-body-lg text-on-surface-variant leading-relaxed">
                    Build seamless payment integrations with Gazoma Pay's high-performance REST API. Access mobile money STK pushes, card payments, ledger accounting balances, and chargeback dispute resolution.
                </p>
            </div>

            <!-- Security Callout -->
            <div class="bg-surface-container-low border border-outline-variant rounded-2xl p-6 flex gap-4">
                <span class="material-symbols-outlined text-secondary text-[24px]">security</span>
                <div>
                    <h4 class="font-body-md text-body-md font-bold text-on-surface mb-1">Server-Side Authorization Required</h4>
                    <p class="font-body-sm text-body-sm text-on-surface-variant">Your secret keys (<code class="font-data-mono text-xs bg-surface-container px-1.5 py-0.5 rounded text-secondary font-bold">gzm_live_sec_...</code>) carry full financial privileges. Never transmit secret tokens in client-side code or mobile apps.</p>
                </div>
            </div>

            <!-- Authentication & Headers Section -->
            <div>
                <h2 class="font-headline-md text-headline-md text-on-surface mb-4 border-b border-outline-variant pb-2">Authentication Headers</h2>
                <p class="font-body-md text-body-md text-on-surface-variant mb-6">
                    Authenticate API calls by passing your Secret Key in the HTTP <code class="font-data-mono text-data-mono bg-surface-container px-2 py-1 rounded">Authorization</code> header.
                </p>

                <div class="border border-outline-variant rounded-2xl overflow-hidden bg-surface-container-lowest mb-6 shadow-sm">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-surface-container-low border-b border-outline-variant">
                                <th class="py-3 px-4 font-label-caps text-label-caps text-on-surface-variant uppercase">Header</th>
                                <th class="py-3 px-4 font-label-caps text-label-caps text-on-surface-variant uppercase">Type</th>
                                <th class="py-3 px-4 font-label-caps text-label-caps text-on-surface-variant uppercase">Description</th>
                            </tr>
                        </thead>
                        <tbody class="font-body-sm text-body-sm text-on-surface">
                            <tr class="border-b border-outline-variant">
                                <td class="py-4 px-4 align-top">
                                    <div class="font-data-mono text-data-mono text-secondary font-semibold bg-surface-container px-2 py-1 rounded inline-block">Authorization</div>
                                    <div class="text-error font-semibold mt-1 text-xs uppercase tracking-wider">Required</div>
                                </td>
                                <td class="py-4 px-4 align-top text-on-surface-variant font-data-mono">string</td>
                                <td class="py-4 px-4 align-top">
                                    Bearer token credential. Example: <code class="font-data-mono text-xs bg-surface-container px-1">Bearer gzm_live_sec_...</code>
                                </td>
                            </tr>
                            <tr class="border-b border-outline-variant">
                                <td class="py-4 px-4 align-top">
                                    <div class="font-data-mono text-data-mono text-on-surface font-semibold bg-surface-container px-2 py-1 rounded inline-block">Idempotency-Key</div>
                                    <div class="text-on-surface-variant mt-1 text-xs uppercase tracking-wider">Optional</div>
                                </td>
                                <td class="py-4 px-4 align-top text-on-surface-variant font-data-mono">string</td>
                                <td class="py-4 px-4 align-top">
                                    Unique UUID string to guarantee idempotency and return cached payloads during network retries.
                                </td>
                            </tr>
                            <tr>
                                <td class="py-4 px-4 align-top">
                                    <div class="font-data-mono text-data-mono text-on-surface font-semibold bg-surface-container px-2 py-1 rounded inline-block">Content-Type</div>
                                    <div class="text-on-surface-variant mt-1 text-xs uppercase tracking-wider">Required</div>
                                </td>
                                <td class="py-4 px-4 align-top text-on-surface-variant font-data-mono">string</td>
                                <td class="py-4 px-4 align-top">Must be <code class="font-data-mono text-xs bg-surface-container px-1">application/json</code> for all POST request bodies.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Endpoint Directory -->
            <div>
                <h2 class="font-headline-md text-headline-md text-on-surface mb-4 border-b border-outline-variant pb-2">Complete REST Endpoint Reference</h2>
                
                <div class="space-y-4">
                    
                    <!-- 1. Gazoma MoMo Charge -->
                    <div class="p-5 border border-outline-variant rounded-2xl bg-surface-container-lowest hover:border-secondary/50 transition-all cursor-pointer" onclick="selectDocEndpoint('momo_charge')">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center gap-3">
                                <span class="px-2.5 py-1 bg-secondary/10 text-secondary font-data-mono font-bold text-xs rounded-lg">POST</span>
                                <code class="font-data-mono font-bold text-on-surface text-sm">/api/v1/momo/charge</code>
                            </div>
                            <span class="text-xs font-bold text-emerald-600 bg-emerald-50 px-2.5 py-0.5 rounded-full">MoMo STK Push</span>
                        </div>
                        <p class="font-body-sm text-xs text-on-surface-variant">Initiate a direct Gazoma Mobile Money STK push prompt for MTN, Telecel, or AT Money numbers.</p>
                    </div>

                    <!-- 2. Verify MoMo Status -->
                    <div class="p-5 border border-outline-variant rounded-2xl bg-surface-container-lowest hover:border-secondary/50 transition-all cursor-pointer" onclick="selectDocEndpoint('momo_verify')">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center gap-3">
                                <span class="px-2.5 py-1 bg-emerald-500/10 text-emerald-600 font-data-mono font-bold text-xs rounded-lg">GET</span>
                                <code class="font-data-mono font-bold text-on-surface text-sm">/api/v1/momo/verify/{reference}</code>
                            </div>
                            <span class="text-xs font-bold text-blue-600 bg-blue-50 px-2.5 py-0.5 rounded-full">Status Polling</span>
                        </div>
                        <p class="font-body-sm text-xs text-on-surface-variant">Poll real-time transaction verification status and settlement payload by reference.</p>
                    </div>

                    <!-- 3. Payments Charge API -->
                    <div class="p-5 border border-outline-variant rounded-2xl bg-surface-container-lowest hover:border-secondary/50 transition-all cursor-pointer" onclick="selectDocEndpoint('charge_payment')">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center gap-3">
                                <span class="px-2.5 py-1 bg-secondary/10 text-secondary font-data-mono font-bold text-xs rounded-lg">POST</span>
                                <code class="font-data-mono font-bold text-on-surface text-sm">/api/v1/payments</code>
                            </div>
                            <span class="text-xs font-bold text-slate-700 bg-slate-100 px-2.5 py-0.5 rounded-full">Card &amp; Direct</span>
                        </div>
                        <p class="font-body-sm text-xs text-on-surface-variant">Process an immediate card payment or direct wallet charge.</p>
                    </div>

                    <!-- 4. Disputes Evidence Submission -->
                    <div class="p-5 border border-outline-variant rounded-2xl bg-surface-container-lowest hover:border-secondary/50 transition-all cursor-pointer" onclick="selectDocEndpoint('dispute_evidence')">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center gap-3">
                                <span class="px-2.5 py-1 bg-amber-500/10 text-amber-700 font-data-mono font-bold text-xs rounded-lg">POST</span>
                                <code class="font-data-mono font-bold text-on-surface text-sm">/api/v1/disputes/{id}/evidence</code>
                            </div>
                            <span class="text-xs font-bold text-amber-700 bg-amber-50 px-2.5 py-0.5 rounded-full">Disputes</span>
                        </div>
                        <p class="font-body-sm text-xs text-on-surface-variant">Submit rebuttal text, waybill numbers, and delivery proof for an open chargeback claim.</p>
                    </div>

                    <!-- 5. Ledger Balance Query -->
                    <div class="p-5 border border-outline-variant rounded-2xl bg-surface-container-lowest hover:border-secondary/50 transition-all cursor-pointer" onclick="selectDocEndpoint('get_balance')">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center gap-3">
                                <span class="px-2.5 py-1 bg-emerald-500/10 text-emerald-600 font-data-mono font-bold text-xs rounded-lg">GET</span>
                                <code class="font-data-mono font-bold text-on-surface text-sm">/api/v1/balance</code>
                            </div>
                            <span class="text-xs font-bold text-indigo-600 bg-indigo-50 px-2.5 py-0.5 rounded-full">Double-Entry Ledger</span>
                        </div>
                        <p class="font-body-sm text-xs text-on-surface-variant">Query real-time available, pending, and settled balances directly calculated from double-entry financial ledger accounts.</p>
                    </div>

                </div>
            </div>

            <!-- Error Handling Matrix -->
            <div>
                <h2 class="font-headline-md text-headline-md text-on-surface mb-4 border-b border-outline-variant pb-2">HTTP Response &amp; Error Code Matrix</h2>
                <div class="border border-outline-variant rounded-2xl overflow-hidden bg-surface-container-lowest shadow-sm">
                    <table class="w-full text-left border-collapse font-body-sm text-xs">
                        <thead>
                            <tr class="bg-surface-container-low border-b border-outline-variant font-label-caps text-[11px] text-on-surface-variant uppercase">
                                <th class="py-3 px-4 font-bold">Code</th>
                                <th class="py-3 px-4 font-bold">Status</th>
                                <th class="py-3 px-4 font-bold">Description &amp; Resolution</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-outline-variant">
                            <tr>
                                <td class="py-3 px-4 font-data-mono font-bold text-emerald-600">200 OK</td>
                                <td class="py-3 px-4 font-bold">Success</td>
                                <td class="py-3 px-4 text-on-surface-variant">Request processed successfully. Body contains payload JSON.</td>
                            </tr>
                            <tr>
                                <td class="py-3 px-4 font-data-mono font-bold text-amber-600">400 Bad Request</td>
                                <td class="py-3 px-4 font-bold">Invalid Parameters</td>
                                <td class="py-3 px-4 text-on-surface-variant">Missing required payload fields or malformed JSON syntax.</td>
                            </tr>
                            <tr>
                                <td class="py-3 px-4 font-data-mono font-bold text-rose-600">401 Unauthorized</td>
                                <td class="py-3 px-4 font-bold">Auth Error</td>
                                <td class="py-3 px-4 text-on-surface-variant">Invalid or missing API key in `Authorization: Bearer` header.</td>
                            </tr>
                            <tr>
                                <td class="py-3 px-4 font-data-mono font-bold text-rose-600">422 Unprocessable</td>
                                <td class="py-3 px-4 font-bold">Declined / Failed</td>
                                <td class="py-3 px-4 text-on-surface-variant">Payment declined by issuing bank or insufficient mobile money funds.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <!-- Right Column: Interactive Code Examples Panel -->
        <div class="w-full xl:w-[440px] bg-slate-950 border border-slate-800 rounded-2xl overflow-hidden flex flex-col h-[650px] sticky top-24 shadow-2xl">
            
            <!-- Code Header & Language Tabs -->
            <div class="flex items-center justify-between border-b border-slate-800 bg-slate-900 px-4 pt-3">
                <div class="flex gap-1" id="codeLangTabs">
                    <button type="button" onclick="switchDocLang('curl')" id="lang-curl" class="doc-lang-tab px-3 py-1.5 font-data-mono text-xs text-secondary border-b-2 border-secondary font-bold cursor-pointer">cURL</button>
                    <button type="button" onclick="switchDocLang('node')" id="lang-node" class="doc-lang-tab px-3 py-1.5 font-data-mono text-xs text-slate-400 hover:text-slate-200 transition-colors cursor-pointer">Node.js</button>
                    <button type="button" onclick="switchDocLang('python')" id="lang-python" class="doc-lang-tab px-3 py-1.5 font-data-mono text-xs text-slate-400 hover:text-slate-200 transition-colors cursor-pointer">Python</button>
                    <button type="button" onclick="switchDocLang('php')" id="lang-php" class="doc-lang-tab px-3 py-1.5 font-data-mono text-xs text-slate-400 hover:text-slate-200 transition-colors cursor-pointer">PHP</button>
                </div>

                <button class="text-slate-400 hover:text-white transition-colors bg-slate-800 p-1.5 rounded-lg border border-slate-700 cursor-pointer text-xs flex items-center gap-1 mb-2" onclick="copyDocSnippet()" title="Copy snippet">
                    <span class="material-symbols-outlined text-[14px]">content_copy</span>
                    <span>Copy</span>
                </button>
            </div>

            <!-- Request Sample Snippet Body -->
            <div class="p-5 overflow-x-auto flex-1 bg-slate-950 font-data-mono text-xs text-slate-200 leading-relaxed relative">
                <pre><code id="docCodeSnippet"><span class="text-slate-500"># Charge Gazoma Mobile Money</span>
curl -X POST http://localhost:8000/api/v1/momo/charge \
  -H <span class="text-amber-200">"Authorization: Bearer gzm_live_sec_12345678"</span> \
  -H <span class="text-amber-200">"Content-Type: application/json"</span> \
  -d '{
    <span class="text-blue-300">"amount"</span>: <span class="text-amber-300">150.00</span>,
    <span class="text-blue-300">"phone"</span>: <span class="text-amber-200">"0241234567"</span>,
    <span class="text-blue-300">"provider"</span>: <span class="text-amber-200">"mtn"</span>,
    <span class="text-blue-300">"email"</span>: <span class="text-amber-200">"customer@example.com"</span>
  }'</code></pre>
            </div>

            <!-- Response Header -->
            <div class="flex border-b border-t border-slate-800 bg-slate-900 px-4 py-2.5 items-center justify-between">
                <span class="font-data-mono text-xs text-slate-400 font-bold uppercase tracking-wider">JSON Response Payload</span>
                <span class="font-data-mono text-xs text-emerald-400 font-bold flex items-center gap-1">
                    <span class="w-2 h-2 rounded-full bg-emerald-400"></span> 200 OK
                </span>
            </div>

            <!-- Response Sample Body -->
            <div class="p-5 overflow-x-auto h-[210px] bg-slate-950 font-data-mono text-xs text-slate-300 leading-relaxed">
                <pre><code id="docResponseSnippet">{
  <span class="text-blue-300">"success"</span>: <span class="text-emerald-400">true</span>,
  <span class="text-blue-300">"status"</span>: <span class="text-amber-200">"pending"</span>,
  <span class="text-blue-300">"reference"</span>: <span class="text-amber-200">"GZM_PS_97D1802A"</span>,
  <span class="text-blue-300">"instructions"</span>: <span class="text-amber-200">"Check phone handset and enter Mobile Money PIN"</span>
}</code></pre>
            </div>

        </div>
    </div>
</div>

<script>
let currentDocEndpoint = 'momo_charge';
let currentDocLang = 'curl';

const docSnippets = {
    momo_charge: {
        curl: `# Charge Gazoma Mobile Money STK Push
curl -X POST http://localhost:8000/api/v1/momo/charge \\
  -H "Authorization: Bearer YOUR_SECRET_KEY" \\
  -H "Content-Type: application/json" \\
  -d '{
    "amount": 150.00,
    "phone": "0241234567",
    "provider": "mtn",
    "email": "customer@example.com"
  }'`,
        node: `const axios = require('axios');

const response = await axios.post('http://localhost:8000/api/v1/momo/charge', {
  amount: 150.00,
  phone: '0241234567',
  provider: 'mtn',
  email: 'customer@example.com'
}, {
  headers: { 'Authorization': 'Bearer YOUR_SECRET_KEY' }
});

console.log(response.data);`,
        python: `import requests

url = "http://localhost:8000/api/v1/momo/charge"
headers = {"Authorization": "Bearer YOUR_SECRET_KEY"}
payload = {
    "amount": 150.00,
    "phone": "0241234567",
    "provider": "mtn",
    "email": "customer@example.com"
}

response = requests.post(url, json=payload, headers=headers)
print(response.json())`,
        php: `$ch = curl_init('http://localhost:8000/api/v1/momo/charge');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer YOUR_SECRET_KEY',
    'Content-Type: application/json'
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    'amount' => 150.00,
    'phone' => '0241234567',
    'provider' => 'mtn',
    'email' => 'customer@example.com'
]));

$res = json_decode(curl_exec($ch), true);
curl_close($ch);`
    },
    momo_verify: {
        curl: `# Verify Transaction Status
curl -X GET http://localhost:8000/api/v1/momo/verify/GZM_PS_97D1802A \\
  -H "Authorization: Bearer YOUR_SECRET_KEY"`,
        node: `const response = await axios.get('http://localhost:8000/api/v1/momo/verify/GZM_PS_97D1802A', {
  headers: { 'Authorization': 'Bearer YOUR_SECRET_KEY' }
});`,
        python: `response = requests.get('http://localhost:8000/api/v1/momo/verify/GZM_PS_97D1802A', headers=headers)`,
        php: `$res = file_get_contents('http://localhost:8000/api/v1/momo/verify/GZM_PS_97D1802A');`
    },
    get_balance: {
        curl: `# Query Double-Entry Financial Ledger Balances
curl -X GET http://localhost:8000/api/v1/balance \\
  -H "Authorization: Bearer YOUR_SECRET_KEY"`,
        node: `const response = await axios.get('http://localhost:8000/api/v1/balance', { headers });`,
        python: `response = requests.get('http://localhost:8000/api/v1/balance', headers=headers)`,
        php: `$res = file_get_contents('http://localhost:8000/api/v1/balance');`
    }
};

const docResponses = {
    momo_charge: `{
  "success": true,
  "status": "pending",
  "reference": "GZM_PS_97D1802A",
  "instructions": "Check phone handset and enter Mobile Money PIN"
}`,
    momo_verify: `{
  "success": true,
  "status": "successful",
  "reference": "GZM_PS_97D1802A",
  "amount": 150.00,
  "fee": 2.75,
  "net_amount": 147.25
}`,
    get_balance: `{
  "success": true,
  "currency": "GHS",
  "available_balance": 28560.00,
  "pending_balance": 4250.00,
  "settled_balance": 93750.00
}`
};

function selectDocEndpoint(epKey) {
    currentDocEndpoint = epKey;
    updateDocSnippet();
}

function switchDocLang(lang) {
    currentDocLang = lang;
    document.querySelectorAll('.doc-lang-tab').forEach(t => {
        t.classList.remove('text-secondary', 'border-b-2', 'border-secondary', 'font-bold');
        t.classList.add('text-slate-400');
    });
    const btn = document.getElementById('lang-' + lang);
    if (btn) {
        btn.classList.remove('text-slate-400');
        btn.classList.add('text-secondary', 'border-b-2', 'border-secondary', 'font-bold');
    }
    updateDocSnippet();
}

function updateDocSnippet() {
    const epData = docSnippets[currentDocEndpoint] || docSnippets.momo_charge;
    const snippet = epData[currentDocLang] || epData.curl;
    document.getElementById('docCodeSnippet').innerText = snippet;

    const resp = docResponses[currentDocEndpoint] || docResponses.momo_charge;
    document.getElementById('docResponseSnippet').innerText = resp;
}

function copyDocSnippet() {
    const text = document.getElementById('docCodeSnippet').innerText;
    navigator.clipboard.writeText(text);
    alert('Code snippet copied to clipboard!');
}
</script>

<!-- ========================================== -->
<!-- MODALS -->
<!-- ========================================== -->
<!-- Generate Key Modal -->
<div class="modal-overlay" id="createKeyModal">
    <div class="modal-card max-w-[480px]">
        <div class="modal-header">
            <h3 class="modal-title">Generate API Key</h3>
            <button class="modal-close" onclick="closeModal('createKeyModal')">&times;</button>
        </div>
        <form action="/developer/api-keys/create" method="POST">
            <input type="hidden" name="csrf_token" value="<?= Auth::generateCsrfToken() ?>">
            <div class="form-group mb-4">
                <label class="form-label block font-body-sm mb-1 text-on-surface">Key Name / Identifer</label>
                <input type="text" name="name" class="form-control w-full px-3 py-2 border border-outline-variant rounded" placeholder="Production Web Server Key" required>
            </div>
            <div class="form-group mb-6">
                <label class="form-label block font-body-sm mb-1 text-on-surface">Environment</label>
                <select name="key_type" class="form-control w-full px-3 py-2 border border-outline-variant rounded bg-white">
                    <option value="live">Live Mode</option>
                    <option value="test">Test Mode</option>
                </select>
            </div>
            <div class="flex justify-end gap-3 pt-2">
                <button type="button" class="px-4 py-2 border border-outline-variant rounded font-body-sm text-on-surface hover:bg-surface-container-low" onclick="closeModal('createKeyModal')">Cancel</button>
                <button type="submit" class="px-5 py-2 bg-primary text-on-primary font-body-sm font-medium rounded hover:bg-primary/90">Generate Key</button>
            </div>
        </form>
    </div>
</div>

<!-- Add Webhook Modal -->
<div class="modal-overlay" id="addWebhookModal">
    <div class="modal-card max-w-[480px]">
        <div class="modal-header">
            <h3 class="modal-title">Add Webhook Endpoint</h3>
            <button class="modal-close" onclick="closeModal('addWebhookModal')">&times;</button>
        </div>
        <form action="/developer/webhooks/create" method="POST">
            <input type="hidden" name="csrf_token" value="<?= Auth::generateCsrfToken() ?>">
            <div class="form-group mb-4">
                <label class="form-label block font-body-sm mb-1 text-on-surface">HTTPS Endpoint URL</label>
                <input type="url" name="url" class="form-control w-full px-3 py-2 border border-outline-variant rounded" placeholder="https://api.yourdomain.com/v1/webhooks/gazoma" required>
            </div>
            <div class="form-group mb-6">
                <label class="form-label block font-body-sm mb-1 text-on-surface">Event Subscriptions</label>
                <div class="space-y-2 mt-2">
                    <label class="flex items-center gap-2 font-body-sm text-on-surface">
                        <input type="checkbox" name="events[]" value="payment.success" checked class="rounded border-outline-variant text-secondary">
                        <code>payment.success</code> (Payment charged successfully)
                    </label>
                    <label class="flex items-center gap-2 font-body-sm text-on-surface">
                        <input type="checkbox" name="events[]" value="payment.failed" checked class="rounded border-outline-variant text-secondary">
                        <code>payment.failed</code> (Payment failed / declined)
                    </label>
                    <label class="flex items-center gap-2 font-body-sm text-on-surface">
                        <input type="checkbox" name="events[]" value="payment.refunded" class="rounded border-outline-variant text-secondary">
                        <code>payment.refunded</code> (Refund reversal completed)
                    </label>
                </div>
            </div>
            <div class="flex justify-end gap-3 pt-2">
                <button type="button" class="px-4 py-2 border border-outline-variant rounded font-body-sm text-on-surface hover:bg-surface-container-low" onclick="closeModal('addWebhookModal')">Cancel</button>
                <button type="submit" class="px-5 py-2 bg-primary text-on-primary font-body-sm font-medium rounded hover:bg-primary/90">Save Endpoint</button>
            </div>
        </form>
    </div>
</div>

<script>
function switchDevTab(tab) {
    document.querySelectorAll('.dev-tab').forEach(t => {
        t.classList.remove('text-secondary', 'border-b-2', 'border-secondary', 'font-semibold');
        t.classList.add('text-on-surface-variant', 'font-medium');
    });
    
    const activeBtn = document.getElementById('tab-' + tab);
    if (activeBtn) {
        activeBtn.classList.remove('text-on-surface-variant', 'font-medium');
        activeBtn.classList.add('text-secondary', 'border-b-2', 'border-secondary', 'font-semibold');
    }

    document.getElementById('section-overview').style.display = (tab === 'overview') ? 'block' : 'none';
    document.getElementById('section-keys').style.display = (tab === 'keys') ? 'block' : 'none';
    document.getElementById('section-webhooks').style.display = (tab === 'webhooks') ? 'block' : 'none';
    document.getElementById('section-docs').style.display = (tab === 'docs') ? 'block' : 'none';
}
</script>
