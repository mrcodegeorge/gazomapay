<!-- Hero Section with Ambient Ambient Glow -->
<section class="hero-section">
  <div class="hero-grid">
    
    <!-- Left Column: Value Proposition & CTAs -->
    <div class="hero-copy">
      
      <!-- Animated Badge -->
      <div class="hero-pill-badge">
        <span class="hero-pill-dot"></span>
        <span>✨ Next-Gen Financial Infrastructure for Africa</span>
      </div>

      <!-- Main Headline -->
      <h1 class="hero-title">
        Zero-Friction <span class="hero-title-gradient">Payment Infrastructure</span> &amp; Ledger Engine
      </h1>

      <!-- Subtitle -->
      <p class="hero-subtitle">
        Accept Mobile Money, Cards, and Bank Transfers with instant double-entry ledger security. Developer-first REST APIs, smart checkout links, and automated bank settlements.
      </p>

      <!-- CTA Action Buttons -->
      <div class="hero-ctas">
        <a href="/register" class="btn-landing-primary btn-hero-lg">
          <span>Create Merchant Account</span>
          <span style="font-weight: 900; font-size: 18px;">&rarr;</span>
        </a>
        <a href="/developers" class="btn-landing-login btn-hero-lg" style="display: inline-flex; align-items: center; gap: 8px;">
          <span style="font-family: var(--font-mono); font-weight: 800; color: var(--accent-cyan);">&lt;/&gt;</span>
          <span>Explore API Docs</span>
        </a>
      </div>

      <!-- Trust Badges -->
      <div class="hero-trust-list">
        <div class="hero-trust-item">
          <span style="color: var(--accent-emerald); font-weight: bold;">✓</span>
          <span>Bank-Grade Double Entry Ledger</span>
        </div>
        <div class="hero-trust-item">
          <span style="color: var(--accent-cyan); font-weight: bold;">🔒</span>
          <span>PCI-DSS Ready 256-Bit SSL</span>
        </div>
        <div class="hero-trust-item">
          <span style="color: var(--accent-amber); font-weight: bold;">⚡</span>
          <span>Real-time Webhook Dispatch</span>
        </div>
      </div>
    </div>

    <!-- Right Column: Interactive Sandbox Checkout Widget -->
    <div>
      <div class="demo-card-box">
        
        <div class="demo-card-header">
          <div class="demo-pulse-title">
            <span class="demo-pulse-dot"></span>
            <span>Interactive Sandbox Demo</span>
          </div>
          <span class="landing-badge-pill">Live Simulation</span>
        </div>

        <!-- Interactive Checkout Form -->
        <div style="display: flex; flex-direction: column; gap: 20px;">
          <div>
            <label class="demo-field-label">Simulated Order Amount</label>
            <div class="demo-input-group">
              <span class="demo-input-prefix">GH₵</span>
              <input type="number" id="heroDemoAmount" value="250.00" step="10" oninput="calculateHeroDemoFee()" class="demo-input-field">
            </div>
          </div>

          <!-- Fee Calculation Display -->
          <div class="demo-breakdown-box">
            <div class="demo-breakdown-row">
              <span>Gross Amount:</span>
              <span style="color: var(--text-white); font-weight: 700;" id="demoGrossDisplay">GH₵ 250.00</span>
            </div>
            <div class="demo-breakdown-row">
              <span>Platform Fee (1.5% + GH₵0.50):</span>
              <span style="color: var(--accent-amber); font-weight: 700;" id="demoFeeDisplay">- GH₵ 4.25</span>
            </div>
            <div class="demo-breakdown-row demo-breakdown-net">
              <span>Net Earnings to Merchant:</span>
              <span style="color: var(--accent-emerald); font-weight: 900;" id="demoNetDisplay">GH₵ 245.75</span>
            </div>
          </div>

          <!-- Payment Method Tabs -->
          <div>
            <label class="demo-field-label">Select Channel</label>
            <div class="demo-channel-tabs">
              <button type="button" onclick="selectHeroChannel('momo')" id="btnHeroMomo" class="demo-tab-btn active">
                <span style="font-size: 16px;">📱</span>
                <span>Mobile Money</span>
              </button>
              <button type="button" onclick="selectHeroChannel('card')" id="btnHeroCard" class="demo-tab-btn">
                <span style="font-size: 16px;">💳</span>
                <span>Card (3DS 2.0)</span>
              </button>
            </div>
          </div>

          <!-- Test Action Button -->
          <button type="button" onclick="runHeroDemoPayment()" id="heroPayBtn" class="btn-demo-charge">
            <span style="font-size: 18px;">⚡</span>
            <span>Test Instant Ledger Charge</span>
          </button>

          <!-- Result Feedback Box -->
          <div id="heroDemoResult" class="demo-result-box" style="display: none;">
            <div style="display: flex; align-items: center; gap: 8px; color: var(--accent-emerald); font-weight: 700;">
              <span style="font-size: 16px;">✓</span>
              <span>Ledger Posting Verified (PASS)</span>
            </div>
            <p style="font-family: var(--font-mono); font-size: 11px; color: var(--text-secondary);" id="heroDemoTxRef">Ref: GZM_DEMO_99812 | Debited Escrow, Credited Merchant Available.</p>
          </div>
        </div>

      </div>
    </div>

  </div>
</section>

<!-- Metrics & Benchmark Banner -->
<section class="metrics-banner">
  <div class="metrics-container">
    <div>
      <div class="metric-val">GH₵ 120M+</div>
      <div class="metric-lbl">Processed Volume</div>
    </div>
    <div>
      <div class="metric-val" style="color: #a5b4fc;">99.99%</div>
      <div class="metric-lbl">API Service Uptime</div>
    </div>
    <div>
      <div class="metric-val" style="color: var(--accent-cyan);">&lt; 0.2s</div>
      <div class="metric-lbl">Ledger Transaction Latency</div>
    </div>
    <div>
      <div class="metric-val" style="color: var(--accent-emerald);">27+</div>
      <div class="metric-lbl">Banks &amp; MoMo Carriers</div>
    </div>
  </div>
</section>

<!-- Core Pillars & Features Section -->
<section class="features-section">
  <div class="section-header">
    <h2 class="section-title">Built for Modern Merchants &amp; Scale</h2>
    <p class="section-desc">Everything you need to accept payments, manage financial accounting, automate payouts, and integrate APIs.</p>
  </div>

  <div class="features-grid">
    
    <!-- Feature 1 -->
    <div class="feature-card">
      <div class="feature-icon-box">
        <span style="font-size: 24px;">📱</span>
      </div>
      <h3 class="feature-title">Mobile Money STK Push</h3>
      <p class="feature-desc">Initiate direct handset PIN prompts for MTN Mobile Money, Telecel Cash (Vodafone), and AT Money (AirtelTigo) with real-time status polling.</p>
      <div style="font-family: var(--font-mono); font-size: 12px; color: #a5b4fc; margin-top: auto;">MTN &bull; Telecel &bull; AT Money</div>
    </div>

    <!-- Feature 2 -->
    <div class="feature-card">
      <div class="feature-icon-box" style="background: rgba(56, 189, 248, 0.12); color: var(--accent-cyan); border-color: rgba(56, 189, 248, 0.3);">
        <span style="font-size: 24px;">🏛️</span>
      </div>
      <h3 class="feature-title">Double-Entry Financial Ledger</h3>
      <p class="feature-desc">Immutable accounting engine (<code style="color: var(--accent-cyan);">LedgerEngine.php</code>). Every customer payment, refund reversal, and payout dispatches audit-ready debits &amp; credits.</p>
      <div style="font-family: var(--font-mono); font-size: 12px; color: var(--accent-cyan); margin-top: auto;">Zero-Drift Balance Integrity</div>
    </div>

    <!-- Feature 3 -->
    <div class="feature-card">
      <div class="feature-icon-box" style="background: rgba(16, 185, 129, 0.12); color: var(--accent-emerald); border-color: rgba(16, 185, 129, 0.3);">
        <span style="font-size: 24px;">🔗</span>
      </div>
      <h3 class="feature-title">No-Code Payment Links</h3>
      <p class="feature-desc">Generate reusable payment links with custom branding, instant QR codes, visitor view analytics, and automated email receipts.</p>
      <div style="font-family: var(--font-mono); font-size: 12px; color: var(--accent-emerald); margin-top: auto;">WhatsApp &amp; Social Checkout Ready</div>
    </div>

    <!-- Feature 4 -->
    <div class="feature-card">
      <div class="feature-icon-box" style="background: rgba(168, 85, 247, 0.12); color: #c084fc; border-color: rgba(168, 85, 247, 0.3);">
        <span style="font-size: 24px;">💳</span>
      </div>
      <h3 class="feature-title">Card Checkout &amp; 3DS 2.0</h3>
      <p class="feature-desc">Accept Visa, Mastercard, Amex, and Discover with 3D Secure bank OTP verification, automatic BIN detection, and card tokenization.</p>
      <div style="font-family: var(--font-mono); font-size: 12px; color: #c084fc; margin-top: auto;">Visa &bull; Mastercard &bull; Amex</div>
    </div>

    <!-- Feature 5 -->
    <div class="feature-card">
      <div class="feature-icon-box" style="background: rgba(245, 158, 11, 0.12); color: var(--accent-amber); border-color: rgba(245, 158, 11, 0.3);">
        <span style="font-size: 24px;">💸</span>
      </div>
      <h3 class="feature-title">Automated Settlements</h3>
      <p class="feature-desc">Request direct bank payouts to GCB, Ecobank, Stanbic, or MoMo wallets. Automated status tracking from pending to bank disbursement.</p>
      <div style="font-family: var(--font-mono); font-size: 12px; color: var(--accent-amber); margin-top: auto;">Instant GIP &amp; MoMo Disbursement</div>
    </div>

    <!-- Feature 6 -->
    <div class="feature-card">
      <div class="feature-icon-box" style="background: rgba(236, 72, 153, 0.12); color: #f472b6; border-color: rgba(236, 72, 153, 0.3);">
        <span style="font-size: 24px;">🧾</span>
      </div>
      <h3 class="feature-title">Subscriptions &amp; PDF Invoicing</h3>
      <p class="feature-desc">Manage recurring billing plans (daily, weekly, monthly, yearly), pause/cancel subscriber tiers, and generate custom PDF invoices.</p>
      <div style="font-family: var(--font-mono); font-size: 12px; color: #f472b6; margin-top: auto;">Automated PDF Receipt Dispatch</div>
    </div>

  </div>
</section>

<!-- Code Showcase Section -->
<section class="code-showcase-section">
  <div class="code-showcase-container">
    
    <div style="display: flex; flex-direction: column; gap: 20px;">
      <div class="landing-badge-pill" style="width: fit-content;">Developer Center API v1</div>
      <h2 class="section-title" style="text-align: left; font-size: 36px;">Integrate Payments in <span style="color: var(--accent-cyan);">Under 5 Lines of Code</span></h2>
      <p class="section-desc" style="text-align: left; font-size: 16px;">Our RESTful API supports standard Bearer token authentication, client idempotency replay protection (<code style="color: var(--accent-cyan);">Idempotency-Key</code>), and signed HMAC-SHA256 webhooks (<code style="color: var(--accent-cyan);">X-Gazoma-Signature</code>).</p>
      
      <div style="display: flex; flex-direction: column; gap: 12px; font-size: 14px; font-weight: 600; color: var(--text-primary);">
        <div style="display: flex; align-items: center; gap: 10px;">
          <span style="color: var(--accent-emerald); font-weight: bold;">✓</span>
          <span>Instant Idempotency Key Replay Protection</span>
        </div>
        <div style="display: flex; align-items: center; gap: 10px;">
          <span style="color: var(--accent-emerald); font-weight: bold;">✓</span>
          <span>HMAC SHA-256 Signed Webhook Payloads</span>
        </div>
        <div style="display: flex; align-items: center; gap: 10px;">
          <span style="color: var(--accent-emerald); font-weight: bold;">✓</span>
          <span>Sandbox &amp; Production Live Environment Keys</span>
        </div>
      </div>

      <a href="/developers" class="btn-landing-login" style="width: fit-content; margin-top: 8px; display: inline-flex; align-items: center; gap: 8px;">
        <span>Read Full REST API Reference</span>
        <span style="font-weight: bold;">&rarr;</span>
      </a>
    </div>

    <!-- Code Terminal Box -->
    <div class="code-box-container">
      <div class="code-box-header">
        <div class="code-tabs">
          <button type="button" onclick="selectCodeTab('curl')" id="tab_curl" class="code-tab-btn active">cURL</button>
          <button type="button" onclick="selectCodeTab('node')" id="tab_node" class="code-tab-btn">Node.js</button>
          <button type="button" onclick="selectCodeTab('php')" id="tab_php" class="code-tab-btn">PHP</button>
          <button type="button" onclick="selectCodeTab('python')" id="tab_python" class="code-tab-btn">Python</button>
        </div>
        <button type="button" onclick="copyCodeSnippet()" class="btn-landing-login" style="padding: 4px 10px; font-size: 11px; display: inline-flex; align-items: center; gap: 6px;">
          <span id="copyBtnText">Copy</span>
        </button>
      </div>

      <pre id="code_curl" class="code-snippet-pre"><code><span style="color: #818cf8;">curl</span> -X POST http://localhost:8000/api/v1/payments \
  -H <span style="color: #fde047;">"Authorization: Bearer gzm_live_pub_9a8b7c6d5e4f3a2b"</span> \
  -H <span style="color: #fde047;">"Idempotency-Key: 7b8c9d0e-1f2a-3b4c-5d6e-7f8a9b0c1d2e"</span> \
  -H <span style="color: #fde047;">"Content-Type: application/json"</span> \
  -d <span style="color: #38bdf8;">'{
    "amount": 250.00,
    "currency": "GHS",
    "customer_name": "Kofi Mensah",
    "customer_email": "kofi@example.com",
    "payment_method": "mobile_money",
    "provider": "mtn"
  }'</span></code></pre>

      <pre id="code_node" class="code-snippet-pre" style="display: none; color: #f3f4f6;"><code><span style="color: #c084fc;">const</span> response = <span style="color: #c084fc;">await</span> fetch(<span style="color: #fde047;">'http://localhost:8000/api/v1/payments'</span>, {
  method: <span style="color: #fde047;">'POST'</span>,
  headers: {
    <span style="color: #fde047;">'Authorization'</span>: <span style="color: #fde047;">'Bearer gzm_live_pub_9a8b7c6d5e4f3a2b'</span>,
    <span style="color: #fde047;">'Idempotency-Key'</span>: crypto.randomUUID(),
    <span style="color: #fde047;">'Content-Type'</span>: <span style="color: #fde047;">'application/json'</span>
  },
  body: JSON.stringify({
    amount: <span style="color: #38bdf8;">250.00</span>,
    customer_email: <span style="color: #fde047;">'kofi@example.com'</span>,
    payment_method: <span style="color: #fde047;">'card'</span>
  })
});
<span style="color: #c084fc;">const</span> data = <span style="color: #c084fc;">await</span> response.json();</code></pre>

      <pre id="code_php" class="code-snippet-pre" style="display: none; color: #f3f4f6;"><code><span style="color: #818cf8;">$ch</span> = curl_init(<span style="color: #fde047;">'http://localhost:8000/api/v1/payments'</span>);
curl_setopt_array(<span style="color: #818cf8;">$ch</span>, [
  CURLOPT_POST => true,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_HTTPHEADER => [
    <span style="color: #fde047;">'Authorization: Bearer gzm_live_pub_9a8b7c6d5e4f3a2b'</span>,
    <span style="color: #fde047;">'Idempotency-Key: '</span> . uniqid(),
    <span style="color: #fde047;">'Content-Type: application/json'</span>
  ],
  CURLOPT_POSTFIELDS => json_encode([
    <span style="color: #fde047;">'amount'</span> => <span style="color: #38bdf8;">250.00</span>,
    <span style="color: #fde047;">'payment_method'</span> => <span style="color: #fde047;">'mobile_money'</span>
  ])
]);
<span style="color: #818cf8;">$res</span> = curl_exec(<span style="color: #818cf8;">$ch</span>);</code></pre>

      <pre id="code_python" class="code-snippet-pre" style="display: none; color: #f3f4f6;"><code><span style="color: #c084fc;">import</span> requests, uuid

headers = {
    <span style="color: #fde047;">"Authorization"</span>: <span style="color: #fde047;">"Bearer gzm_live_pub_9a8b7c6d5e4f3a2b"</span>,
    <span style="color: #fde047;">"Idempotency-Key"</span>: str(uuid.uuid4()),
    <span style="color: #fde047;">"Content-Type"</span>: <span style="color: #fde047;">"application/json"</span>
}
payload = {
    <span style="color: #fde047;">"amount"</span>: <span style="color: #38bdf8;">250.00</span>,
    <span style="color: #fde047;">"payment_method"</span>: <span style="color: #fde047;">"mobile_money"</span>
}
res = requests.post(<span style="color: #fde047;">"http://localhost:8000/api/v1/payments"</span>, json=payload, headers=headers)</code></pre>

    </div>

  </div>
</section>

<!-- Final High-Converting CTA Section -->
<section style="padding: 96px 24px; max-width: 1000px; margin: 0 auto; text-align: center;">
  <div style="background: rgba(17, 24, 39, 0.8); border: 1px solid rgba(99, 102, 241, 0.4); border-radius: 32px; padding: 64px 32px; display: flex; flex-direction: column; gap: 24px; box-shadow: 0 20px 50px rgba(99, 102, 241, 0.15);">
    
    <h2 class="section-title" style="font-size: 40px;">Ready to Accept Frictionless Digital Payments?</h2>

    <p class="section-desc" style="max-width: 600px; margin: 0 auto;">
      Create your merchant account in 2 minutes. Start accepting Mobile Money and Card payments with instant double-entry ledger accounting today.
    </p>

    <div style="display: flex; justify-content: center; gap: 16px; flex-wrap: wrap; padding-top: 16px;">
      <a href="/register" class="btn-landing-primary btn-hero-lg">
        <span>Launch Merchant Account Free</span>
        <span style="font-weight: 900; font-size: 18px;">&rarr;</span>
      </a>
      <a href="/contact" class="btn-landing-login btn-hero-lg">
        <span>Talk to Sales</span>
      </a>
    </div>

  </div>
</section>

<!-- Interactive Scripts for Landing Page -->
<script>
let currentHeroChannel = 'momo';

function calculateHeroDemoFee() {
  const amtInput = document.getElementById('heroDemoAmount');
  let gross = parseFloat(amtInput.value) || 0;
  if (gross < 0) gross = 0;

  const fee = Math.round((gross * 0.015 + 0.50) * 100) / 100;
  const net = Math.round((gross - fee) * 100) / 100;

  document.getElementById('demoGrossDisplay').innerText = 'GH₵ ' + gross.toFixed(2);
  document.getElementById('demoFeeDisplay').innerText = '- GH₵ ' + fee.toFixed(2);
  document.getElementById('demoNetDisplay').innerText = 'GH₵ ' + net.toFixed(2);
}

function selectHeroChannel(channel) {
  currentHeroChannel = channel;
  const btnMomo = document.getElementById('btnHeroMomo');
  const btnCard = document.getElementById('btnHeroCard');

  if (channel === 'momo') {
    btnMomo.className = 'demo-tab-btn active';
    btnCard.className = 'demo-tab-btn';
  } else {
    btnCard.className = 'demo-tab-btn active';
    btnMomo.className = 'demo-tab-btn';
  }
}

function runHeroDemoPayment() {
  const btn = document.getElementById('heroPayBtn');
  btn.disabled = true;
  btn.innerHTML = '<span>⚡</span> Posting Ledger Entry...';

  setTimeout(() => {
    btn.disabled = false;
    btn.innerHTML = '<span>⚡</span> Test Instant Ledger Charge';
    
    const gross = parseFloat(document.getElementById('heroDemoAmount').value) || 250;
    const ref = 'GZM_DEMO_' + Math.floor(100000 + Math.random() * 900000);
    
    const resBox = document.getElementById('heroDemoResult');
    resBox.style.display = 'flex';
    document.getElementById('heroDemoTxRef').innerText = `Ref: ${ref} (${currentHeroChannel.toUpperCase()}) | Gross GH₵ ${gross.toFixed(2)} posted to Ledger.`;
  }, 700);
}

function selectCodeTab(tab) {
  ['curl', 'node', 'php', 'python'].forEach(t => {
    const btn = document.getElementById('tab_' + t);
    const code = document.getElementById('code_' + t);
    if (t === tab) {
      btn.className = 'code-tab-btn active';
      code.style.display = 'block';
    } else {
      btn.className = 'code-tab-btn';
      code.style.display = 'none';
    }
  });
}

function copyCodeSnippet() {
  const activeCode = document.querySelector('pre:not([style*="display: none"]) code');
  if (activeCode) {
    navigator.clipboard.writeText(activeCode.innerText);
    const copyText = document.getElementById('copyBtnText');
    copyText.innerText = 'Copied!';
    setTimeout(() => { copyText.innerText = 'Copy'; }, 2000);
  }
}

document.addEventListener('DOMContentLoaded', calculateHeroDemoFee);
</script>
