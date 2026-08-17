<!-- Hero Section -->
<section class="hero-section">
  <div style="max-w-4xl mx-auto text-center space-y-4">
    <div class="landing-badge-pill" style="margin: 0 auto;">Developer Portal &amp; REST API v1</div>
    <h1 class="hero-title">Developer Documentation &amp; API Reference</h1>
    <p class="hero-subtitle" style="margin: 0 auto;">
      Complete integration guides, REST endpoints, HMAC webhook signature verification, idempotency controls, and interactive API console.
    </p>
  </div>
</section>

<!-- Base Setup & Authentication Grid -->
<section class="features-section" style="padding-top: 40px;">
  <div class="features-grid" style="grid-template-columns: repeat(3, 1fr);">
    
    <div class="feature-card">
      <div class="feature-icon-box">
        <span style="font-family: var(--font-mono); font-weight: bold;">01</span>
      </div>
      <h3 class="feature-title">Environment URLs</h3>
      <p class="feature-desc">
        <strong>Sandbox Base URL:</strong><br>
        <code style="color: var(--accent-cyan);">http://127.0.0.1:8000/api/v1</code><br><br>
        <strong>Production Base URL:</strong><br>
        <code style="color: var(--accent-emerald);">https://api.gazomapay.com/v1</code>
      </p>
    </div>

    <div class="feature-card">
      <div class="feature-icon-box" style="background: rgba(56, 189, 248, 0.12); color: var(--accent-cyan); border-color: rgba(56, 189, 248, 0.3);">
        <span style="font-family: var(--font-mono); font-weight: bold;">02</span>
      </div>
      <h3 class="feature-title">Bearer Authentication</h3>
      <p class="feature-desc">
        Authenticate requests via standard HTTP Bearer token:<br><br>
        <code style="color: #fde047;">Authorization: Bearer gzm_live_pub_9a8b...</code><br><br>
        Keep secret keys secure on your backend server.
      </p>
    </div>

    <div class="feature-card">
      <div class="feature-icon-box" style="background: rgba(16, 185, 129, 0.12); color: var(--accent-emerald); border-color: rgba(16, 185, 129, 0.3);">
        <span style="font-family: var(--font-mono); font-weight: bold;">03</span>
      </div>
      <h3 class="feature-title">Idempotency Protection</h3>
      <p class="feature-desc">
        Pass a unique UUID header in charge calls:<br><br>
        <code style="color: var(--accent-emerald);">Idempotency-Key: 7b8c9d0e-1f2a...</code><br><br>
        Prevents double-charging if network timeouts occur.
      </p>
    </div>

  </div>
</section>

<!-- API Endpoints Directory -->
<section class="features-section" style="padding-top: 0;">
  <div class="section-header" style="text-align: left; max-width: 100%;">
    <h2 class="section-title">Core API v1 Endpoint Reference</h2>
    <p class="section-desc">Click any endpoint below to view full request headers, JSON request body, and sample HTTP 200 response payloads.</p>
  </div>

  <div style="display: flex; flex-direction: column; gap: 24px;">
    
    <!-- Endpoint 1: POST /api/v1/card/charge -->
    <div class="feature-card" style="padding: 24px; gap: 20px;">
      <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
        <div style="display: flex; align-items: center; gap: 12px; font-family: var(--font-mono);">
          <span style="background: rgba(16, 185, 129, 0.2); color: var(--accent-emerald); padding: 4px 10px; border-radius: 8px; font-weight: bold; font-size: 13px;">POST</span>
          <span style="color: var(--text-white); font-weight: bold; font-size: 16px;">/api/v1/card/charge</span>
        </div>
        <span style="font-size: 13px; color: var(--text-secondary);">Execute Card Charge with 3DS State Machine</span>
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
        <div>
          <div style="font-family: var(--font-mono); font-size: 11px; text-transform: uppercase; color: var(--text-secondary); margin-bottom: 6px;">Request Body (JSON)</div>
          <pre class="code-snippet-pre" style="background: #020617; padding: 16px; border-radius: 12px; font-size: 12px; margin: 0;"><code>{
  <span style="color: #fde047;">"amount"</span>: <span style="color: #38bdf8;">150.00</span>,
  <span style="color: #fde047;">"currency"</span>: <span style="color: #fde047;">"GHS"</span>,
  <span style="color: #fde047;">"card_number"</span>: <span style="color: #fde047;">"4000 0000 0000 9010"</span>,
  <span style="color: #fde047;">"card_exp"</span>: <span style="color: #fde047;">"12/28"</span>,
  <span style="color: #fde047;">"card_cvv"</span>: <span style="color: #fde047;">"123"</span>,
  <span style="color: #fde047;">"customer_email"</span>: <span style="color: #fde047;">"customer@example.com"</span>
}</code></pre>
        </div>

        <div>
          <div style="font-family: var(--font-mono); font-size: 11px; text-transform: uppercase; color: var(--text-secondary); margin-bottom: 6px;">Response (200 OK)</div>
          <pre class="code-snippet-pre" style="background: #020617; padding: 16px; border-radius: 12px; font-size: 12px; margin: 0;"><code>{
  <span style="color: #fde047;">"status"</span>: <span style="color: #fde047;">"pending_3ds"</span>,
  <span style="color: #fde047;">"reference"</span>: <span style="color: #fde047;">"GZM_CARD_88912"</span>,
  <span style="color: #fde047;">"brand"</span>: <span style="color: #fde047;">"Visa"</span>,
  <span style="color: #fde047;">"masked_card"</span>: <span style="color: #fde047;">"**** **** **** 9010"</span>,
  <span style="color: #fde047;">"next_action"</span>: <span style="color: #fde047;">"verify_3ds_otp"</span>
}</code></pre>
        </div>
      </div>
    </div>

    <!-- Endpoint 2: POST /api/v1/card/verify-3ds -->
    <div class="feature-card" style="padding: 24px; gap: 20px;">
      <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
        <div style="display: flex; align-items: center; gap: 12px; font-family: var(--font-mono);">
          <span style="background: rgba(56, 189, 248, 0.2); color: var(--accent-cyan); padding: 4px 10px; border-radius: 8px; font-weight: bold; font-size: 13px;">POST</span>
          <span style="color: var(--text-white); font-weight: bold; font-size: 16px;">/api/v1/card/verify-3ds</span>
        </div>
        <span style="font-size: 13px; color: var(--text-secondary);">Verify Bank 3D Secure OTP Code</span>
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
        <div>
          <div style="font-family: var(--font-mono); font-size: 11px; text-transform: uppercase; color: var(--text-secondary); margin-bottom: 6px;">Request Body (JSON)</div>
          <pre class="code-snippet-pre" style="background: #020617; padding: 16px; border-radius: 12px; font-size: 12px; margin: 0;"><code>{
  <span style="color: #fde047;">"reference"</span>: <span style="color: #fde047;">"GZM_CARD_88912"</span>,
  <span style="color: #fde047;">"otp"</span>: <span style="color: #fde047;">"123456"</span>
}</code></pre>
        </div>

        <div>
          <div style="font-family: var(--font-mono); font-size: 11px; text-transform: uppercase; color: var(--text-secondary); margin-bottom: 6px;">Response (200 OK)</div>
          <pre class="code-snippet-pre" style="background: #020617; padding: 16px; border-radius: 12px; font-size: 12px; margin: 0;"><code>{
  <span style="color: #fde047;">"status"</span>: <span style="color: #fde047;">"success"</span>,
  <span style="color: #fde047;">"reference"</span>: <span style="color: #fde047;">"GZM_CARD_88912"</span>,
  <span style="color: #fde047;">"gross_amount"</span>: <span style="color: #38bdf8;">150.00</span>,
  <span style="color: #fde047;">"fee"</span>: <span style="color: #38bdf8;">2.75</span>,
  <span style="color: #fde047;">"net_amount"</span>: <span style="color: #38bdf8;">147.25</span>,
  <span style="color: #fde047;">"ledger_posted"</span>: <span style="color: #10b981;">true</span>
}</code></pre>
        </div>
      </div>
    </div>

    <!-- Endpoint 3: POST /api/v1/momo/simulate-approval -->
    <div class="feature-card" style="padding: 24px; gap: 20px;">
      <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
        <div style="display: flex; align-items: center; gap: 12px; font-family: var(--font-mono);">
          <span style="background: rgba(245, 158, 11, 0.2); color: var(--accent-amber); padding: 4px 10px; border-radius: 8px; font-weight: bold; font-size: 13px;">POST</span>
          <span style="color: var(--text-white); font-weight: bold; font-size: 16px;">/api/v1/momo/simulate-approval</span>
        </div>
        <span style="font-size: 13px; color: var(--text-secondary);">Simulate Handset Mobile Money PIN Approval</span>
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
        <div>
          <div style="font-family: var(--font-mono); font-size: 11px; text-transform: uppercase; color: var(--text-secondary); margin-bottom: 6px;">Request Body (JSON)</div>
          <pre class="code-snippet-pre" style="background: #020617; padding: 16px; border-radius: 12px; font-size: 12px; margin: 0;"><code>{
  <span style="color: #fde047;">"reference"</span>: <span style="color: #fde047;">"PL_1234567890"</span>,
  <span style="color: #fde047;">"phone"</span>: <span style="color: #fde047;">"0241234567"</span>,
  <span style="color: #fde047;">"network"</span>: <span style="color: #fde047;">"mtn"</span>
}</code></pre>
        </div>

        <div>
          <div style="font-family: var(--font-mono); font-size: 11px; text-transform: uppercase; color: var(--text-secondary); margin-bottom: 6px;">Response (200 OK)</div>
          <pre class="code-snippet-pre" style="background: #020617; padding: 16px; border-radius: 12px; font-size: 12px; margin: 0;"><code>{
  <span style="color: #fde047;">"success"</span>: <span style="color: #10b981;">true</span>,
  <span style="color: #fde047;">"status"</span>: <span style="color: #fde047;">"completed"</span>,
  <span style="color: #fde047;">"provider"</span>: <span style="color: #fde047;">"MTN Mobile Money"</span>,
  <span style="color: #fde047;">"net_amount"</span>: <span style="color: #38bdf8;">98.00</span>
}</code></pre>
        </div>
      </div>
    </div>

  </div>
</section>

<!-- Interactive Live API Test Console Widget -->
<section class="code-showcase-section">
  <div style="max-w-5xl mx-auto space-y-8">
    <div style="text-align: center; space-y-2;">
      <div class="landing-badge-pill" style="margin: 0 auto;">Live Sandbox Test Console</div>
      <h2 class="section-title">Test API Request Payloads Inline</h2>
      <p class="section-desc">Select an API action below and test sending real JSON payloads to your local Gazoma Pay engine.</p>
    </div>

    <div class="code-box-container" style="padding: 24px;">
      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
        
        <!-- Left: Test Payload Form -->
        <div style="display: flex; flex-direction: column; gap: 16px;">
          <div style="display: flex; justify-content: space-between; align-items: center;">
            <label style="font-family: var(--font-mono); font-size: 12px; color: var(--text-secondary); font-weight: bold; text-transform: uppercase;">Select Endpoint</label>
            <select id="consoleEndpoint" onchange="switchConsoleEndpoint()" style="background: #0b1329; border: 1px solid var(--border-subtle); color: var(--text-white); padding: 6px 12px; border-radius: 8px; font-family: var(--font-mono); font-size: 12px; outline: none;">
              <option value="card_charge">POST /api/v1/card/charge</option>
              <option value="verify_3ds">POST /api/v1/card/verify-3ds</option>
              <option value="momo_simulate">POST /api/v1/momo/simulate-approval</option>
            </select>
          </div>

          <div>
            <label style="font-family: var(--font-mono); font-size: 12px; color: var(--text-secondary); font-weight: bold; text-transform: uppercase; margin-bottom: 6px; display: block;">JSON Payload</label>
            <textarea id="consolePayload" rows="10" style="width: 100%; background: #0b1329; border: 1px solid var(--border-subtle); color: #38bdf8; font-family: var(--font-mono); font-size: 12px; padding: 14px; border-radius: 12px; outline: none; line-height: 1.5;"></textarea>
          </div>

          <button type="button" onclick="executeConsoleRequest()" id="btnExecuteConsole" class="btn-landing-primary" style="justify-content: center; width: 100%; padding: 14px;">
            <span>Execute API Request</span>
            <span style="font-weight: bold;">&rarr;</span>
          </button>
        </div>

        <!-- Right: Real-time Response Output -->
        <div style="display: flex; flex-direction: column; gap: 16px;">
          <div style="display: flex; justify-content: space-between; align-items: center;">
            <label style="font-family: var(--font-mono); font-size: 12px; color: var(--text-secondary); font-weight: bold; text-transform: uppercase;">HTTP Response</label>
            <span id="consoleStatusBadge" class="landing-badge-pill" style="background: rgba(16, 185, 129, 0.2); color: var(--accent-emerald);">HTTP 200 OK</span>
          </div>

          <pre id="consoleOutput" class="code-snippet-pre" style="background: #0b1329; padding: 16px; border-radius: 12px; font-size: 12px; height: 260px; overflow-y: auto; margin: 0; border: 1px solid var(--border-subtle);"><code>{
  "message": "Click 'Execute API Request' above to test endpoint."
}</code></pre>
        </div>

      </div>
    </div>
  </div>
</section>

<!-- Webhook Signature Guide -->
<section class="features-section">
  <div class="feature-card" style="padding: 36px; gap: 24px;">
    <div style="display: flex; align-items: center; gap: 12px;">
      <div class="feature-icon-box" style="background: rgba(16, 185, 129, 0.12); color: var(--accent-emerald); border-color: rgba(16, 185, 129, 0.3);">
        <span style="font-weight: bold;">HMAC</span>
      </div>
      <div>
        <h3 class="feature-title">Webhook HMAC-SHA256 Signature Verification</h3>
        <p class="feature-desc">All outbound webhook events include the <code style="color: var(--accent-emerald);">X-Gazoma-Signature</code> HTTP header. Verify payload integrity on your server before processing.</p>
      </div>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
      <div>
        <div style="font-family: var(--font-mono); font-size: 11px; text-transform: uppercase; color: var(--text-secondary); margin-bottom: 8px;">PHP Signature Check</div>
        <pre class="code-snippet-pre" style="background: #020617; padding: 16px; border-radius: 12px; font-size: 12px; margin: 0;"><code><span style="color: #818cf8;">$payload</span> = file_get_contents(<span style="color: #fde047;">'php://input'</span>);
<span style="color: #818cf8;">$headerSig</span> = <span style="color: #818cf8;">$_SERVER</span>[<span style="color: #fde047;">'HTTP_X_GAZOMA_SIGNATURE'</span>];
<span style="color: #818cf8;">$secret</span> = <span style="color: #fde047;">'whsec_9a8b7c6d5e4f3a2b'</span>;

<span style="color: #818cf8;">$calcSig</span> = hash_hmac(<span style="color: #fde047;">'sha256'</span>, <span style="color: #818cf8;">$payload</span>, <span style="color: #818cf8;">$secret</span>);
<span style="color: #c084fc;">if</span> (hash_equals(<span style="color: #818cf8;">$calcSig</span>, <span style="color: #818cf8;">$headerSig</span>)) {
    <span style="color: #5eead4;">// Signature verified — process event</span>
}</code></pre>
      </div>

      <div>
        <div style="font-family: var(--font-mono); font-size: 11px; text-transform: uppercase; color: var(--text-secondary); margin-bottom: 8px;">Node.js Express Signature Check</div>
        <pre class="code-snippet-pre" style="background: #020617; padding: 16px; border-radius: 12px; font-size: 12px; margin: 0;"><code><span style="color: #c084fc;">const</span> crypto = require(<span style="color: #fde047;">'crypto'</span>);

<span style="color: #c084fc;">const</span> signature = req.headers[<span style="color: #fde047;">'x-gazoma-signature'</span>];
<span style="color: #c084fc;">const</span> calcSig = crypto.createHmac(<span style="color: #fde047;">'sha256'</span>, secret)
  .update(JSON.stringify(req.body)).digest(<span style="color: #fde047;">'hex'</span>);

<span style="color: #c084fc;">if</span> (signature === calcSig) {
  <span style="color: #5eead4;">// Signature verified — process event</span>
}</code></pre>
      </div>
    </div>
  </div>
</section>

<!-- Interactive Console JavaScript -->
<script>
const payloads = {
  card_charge: `{\n  "amount": 250.00,\n  "currency": "GHS",\n  "card_number": "4000000000009010",\n  "card_exp": "12/28",\n  "card_cvv": "123",\n  "customer_email": "developer@gazomapay.com"\n}`,
  verify_3ds: `{\n  "reference": "GZM_CARD_88912",\n  "otp": "123456"\n}`,
  momo_simulate: `{\n  "reference": "PL_1234567890",\n  "phone": "0241234567",\n  "network": "mtn"\n}`
};

function switchConsoleEndpoint() {
  const ep = document.getElementById('consoleEndpoint').value;
  document.getElementById('consolePayload').value = payloads[ep] || '{}';
}

function executeConsoleRequest() {
  const ep = document.getElementById('consoleEndpoint').value;
  const btn = document.getElementById('btnExecuteConsole');
  btn.disabled = true;
  btn.innerText = 'Executing...';

  let url = '/api/v1/card/charge';
  if (ep === 'verify_3ds') url = '/api/v1/card/verify-3ds';
  if (ep === 'momo_simulate') url = '/api/v1/momo/simulate-approval';

  const bodyRaw = document.getElementById('consolePayload').value;

  fetch(url, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Authorization': 'Bearer gzm_live_pub_9a8b7c6d5e4f3a2b'
    },
    body: bodyRaw
  })
  .then(res => res.json())
  .then(data => {
    btn.disabled = false;
    btn.innerHTML = '<span>Execute API Request</span> <span style="font-weight: bold;">&rarr;</span>';
    document.getElementById('consoleOutput').innerHTML = '<code>' + JSON.stringify(data, null, 2) + '</code>';
  })
  .catch(err => {
    btn.disabled = false;
    btn.innerHTML = '<span>Execute API Request</span> <span style="font-weight: bold;">&rarr;</span>';
    document.getElementById('consoleOutput').innerHTML = '<code>{\n  "error": "Failed to parse API response",\n  "details": "' + err.message + '"\n}</code>';
  });
}

document.addEventListener('DOMContentLoaded', switchConsoleEndpoint);
</script>
