<section class="hero-section">
  <h1 class="hero-title">Developer Documentation & API Gateway</h1>
  <p class="hero-subtitle">Integrate modern payment infrastructure with clean RESTful endpoints, idempotency headers, and signed HMAC webhooks.</p>
</section>

<section style="padding: 64px 48px; max-width: 1100px; margin: 0 auto;">
  <div style="margin-bottom: 48px;">
    <h2 style="font-size: 24px; font-weight: 800; color: #0f172a; margin-bottom: 12px;">Authentication</h2>
    <p style="color: #475569; font-size: 15px; margin-bottom: 16px;">Pass your API Key in the <code>Authorization</code> header as a Bearer token:</p>
    <pre style="background: #0b1220; color: #38bdf8; padding: 20px; border-radius: 10px; font-family: monospace; font-size: 14px;">Authorization: Bearer gzm_live_pub_9a8b7c6d5e4f3a2b</pre>
  </div>

  <div style="margin-bottom: 48px;">
    <h2 style="font-size: 24px; font-weight: 800; color: #0f172a; margin-bottom: 12px;">Create Payment (`POST /api/v1/payments`)</h2>
    <p style="color: #475569; font-size: 15px; margin-bottom: 16px;">Include an optional <code>Idempotency-Key</code> header to prevent duplicate charges during network retries.</p>

    <pre style="background: #0b1220; color: #f8fafc; padding: 24px; border-radius: 10px; font-family: monospace; font-size: 14px; line-height: 1.6;"><span style="color: #f43f5e;">curl</span> -X POST http://127.0.0.1:8000/api/v1/payments \
  -H <span style="color: #a3e635;">"Authorization: Bearer gzm_live_pub_9a8b7c6d5e4f3a2b"</span> \
  -H <span style="color: #a3e635;">"Idempotency-Key: 7b8c9d0e-1f2a-3b4c-5d6e-7f8a9b0c1d2e"</span> \
  -H <span style="color: #a3e635;">"Content-Type: application/json"</span> \
  -d <span style="color: #38bdf8;">'{
    "amount": 250.00,
    "currency": "GHS",
    "customer_name": "Kofi Mensah",
    "customer_email": "kofi@example.com",
    "payment_method": "mobile_money"
  }'</span></pre>
  </div>

  <div style="margin-bottom: 48px;">
    <h2 style="font-size: 24px; font-weight: 800; color: #0f172a; margin-bottom: 12px;">Signed Webhooks (`X-Gazoma-Signature`)</h2>
    <p style="color: #475569; font-size: 15px; margin-bottom: 16px;">Verify payload authenticity by computing HMAC SHA256 using your endpoint secret:</p>
    <pre style="background: #0b1220; color: #cbd5e1; padding: 20px; border-radius: 10px; font-family: monospace; font-size: 14px;">$computedSig = hash_hmac('sha256', "{$timestamp}.{$rawPayload}", $secret);</pre>
  </div>
</section>
