<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($title ?? 'Gazoma Pay — Next-Gen Payment Infrastructure & Financial Ledger Engine') ?></title>
  <meta name="description" content="Gazoma Pay is Africa's modern payment infrastructure platform providing instant Mobile Money, Card payments, double-entry financial ledger accounting, and developer APIs.">
  
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Fira+Code:wght@400;500;600&display=swap" rel="stylesheet">
  
  <link rel="stylesheet" href="/assets/css/app.css?v=<?= time() ?>">
  <link rel="stylesheet" href="/assets/css/landing.css?v=<?= time() ?>">

  <style>
    /* Critical Inline Fallback Styles to guarantee instant render regardless of CDN/Cache state */
    body.dark-web-body {
      background-color: #030712 !important;
      color: #f3f4f6 !important;
      font-family: 'Plus Jakarta Sans', -apple-system, sans-serif !important;
      margin: 0;
      padding: 0;
    }
  </style>
</head>
<body class="dark-web-body">
  
  <!-- Navigation Header -->
  <header class="landing-header">
    <div class="landing-header-container">
      
      <!-- Brand Logo -->
      <a href="/" class="landing-brand">
        <div class="landing-brand-icon">G</div>
        <div class="landing-brand-text">
          <span class="landing-brand-name">Gazoma<span>Pay</span></span>
          <span class="landing-brand-tag">Infrastructure</span>
        </div>
      </a>

      <!-- Desktop Nav -->
      <nav class="landing-nav">
        <a href="/solutions" class="landing-nav-link">Solutions</a>
        <a href="/pricing" class="landing-nav-link">Pricing</a>
        <a href="/developers" class="landing-nav-link">
          <span>Developers</span>
          <span class="landing-badge-pill">API v1</span>
        </a>
        <a href="/security" class="landing-nav-link">Security &amp; Ledger</a>
        <a href="/about" class="landing-nav-link">About Us</a>
      </nav>

      <!-- Header CTAs -->
      <div class="landing-actions">
        <a href="/login" class="btn-landing-login">Sign In</a>
        <a href="/register" class="btn-landing-primary">
          <span>Get Started</span>
          <span style="font-weight: 900; font-size: 16px;">&rarr;</span>
        </a>
      </div>

    </div>
  </header>

  <!-- Main Content Area -->
  <main class="landing-main">
    <?= $content ?>
  </main>

  <!-- Footer -->
  <footer class="landing-footer">
    <div class="footer-container">
      <div class="footer-top-grid">
        
        <!-- Brand & Description -->
        <div style="display: flex; flex-direction: column; gap: 16px;">
          <a href="/" class="landing-brand">
            <div class="landing-brand-icon" style="width: 32px; height: 32px; font-size: 16px;">G</div>
            <span class="landing-brand-name" style="font-size: 18px;">GazomaPay</span>
          </a>
          <p style="font-size: 14px; color: var(--text-secondary); max-width: 360px; line-height: 1.6;">
            Production-grade financial engine offering instant Mobile Money collections, card processing, immutable double-entry ledger accounting, and developer APIs across West Africa.
          </p>

          <div style="display: inline-flex; align-items: center; gap: 8px; font-family: var(--font-mono); font-size: 12px; color: var(--accent-emerald); background: #060d1f; border: 1px solid var(--border-subtle); padding: 6px 14px; border-radius: 9999px; width: fit-content;">
            <span style="width: 8px; height: 8px; border-radius: 50%; background: var(--accent-emerald); box-shadow: 0 0 10px var(--accent-emerald);"></span>
            <span>All Systems Operational (99.99% Uptime)</span>
          </div>
        </div>

        <!-- Products -->
        <div>
          <h4 class="footer-col-title">Products</h4>
          <ul class="footer-links-list">
            <li><a href="/solutions">Payment Links</a></li>
            <li><a href="/solutions">Mobile Money STK Push</a></li>
            <li><a href="/solutions">Card Checkout (3DS 2.0)</a></li>
            <li><a href="/solutions">Subscription Billing</a></li>
            <li><a href="/solutions">Invoicing &amp; PDF Receipts</a></li>
          </ul>
        </div>

        <!-- Developers -->
        <div>
          <h4 class="footer-col-title">Developers</h4>
          <ul class="footer-links-list">
            <li><a href="/developers">API Documentation</a></li>
            <li><a href="/developers">HMAC Webhooks</a></li>
            <li><a href="/developers">Idempotency Guide</a></li>
            <li><a href="/developers">Sandbox SDK &amp; Postman</a></li>
          </ul>
        </div>

        <!-- Security & Legal -->
        <div>
          <h4 class="footer-col-title">Security &amp; Legal</h4>
          <ul class="footer-links-list">
            <li><a href="/security">Double-Entry Ledger</a></li>
            <li><a href="/security">PCI-DSS Ready Compliance</a></li>
            <li><a href="/about">About Gazoma Pay</a></li>
            <li><a href="/contact">Contact Support</a></li>
          </ul>
        </div>

      </div>

      <div class="footer-bottom-bar">
        <p>&copy; <?= date('Y') ?> Gazoma Pay Limited. All rights reserved.</p>
        <div style="display: flex; gap: 24px; font-family: var(--font-mono); font-size: 11px;">
          <span>Ghana Fintech Infrastructure Sandbox v1.0</span>
          <span>&bull;</span>
          <span style="color: var(--text-secondary);">256-Bit SSL Encrypted</span>
        </div>
      </div>
    </div>
  </footer>

</body>
</html>
