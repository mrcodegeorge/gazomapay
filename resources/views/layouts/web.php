<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($title ?? 'Gazoma Pay') ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/assets/css/app.css">
  <style>
    .web-header {
      background: #ffffff;
      border-bottom: 1px solid #e2e8f0;
      padding: 18px 48px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      position: sticky;
      top: 0;
      z-index: 100;
    }
    .web-nav {
      display: flex;
      gap: 32px;
    }
    .web-nav a {
      color: #475569;
      font-weight: 600;
      font-size: 15px;
      text-decoration: none;
    }
    .web-nav a:hover {
      color: #2563eb;
    }
    .web-footer {
      background: #0b1220;
      color: #94a3b8;
      padding: 64px 48px 32px;
    }
    .footer-grid {
      display: grid;
      grid-template-columns: 2fr repeat(3, 1fr);
      gap: 48px;
      margin-bottom: 48px;
    }
    .footer-col h4 {
      color: #fff;
      margin-bottom: 16px;
      font-size: 15px;
    }
    .footer-col ul {
      list-style: none;
    }
    .footer-col li {
      margin-bottom: 10px;
    }
    .footer-col a {
      color: #94a3b8;
      text-decoration: none;
    }
    .footer-col a:hover {
      color: #fff;
    }
    .hero-section {
      padding: 96px 48px 80px;
      background: linear-gradient(180deg, #f8fafc 0%, #eff6ff 100%);
      text-align: center;
    }
    .hero-title {
      font-size: 48px;
      font-weight: 800;
      color: #0f172a;
      letter-spacing: -1.2px;
      line-height: 1.15;
      max-width: 800px;
      margin: 0 auto 20px;
    }
    .hero-subtitle {
      font-size: 18px;
      color: #475569;
      max-width: 620px;
      margin: 0 auto 36px;
    }
    .hero-ctas {
      display: flex;
      gap: 16px;
      justify-content: center;
    }
  </style>
</head>
<body>
  <header class="web-header">
    <a href="/" class="brand-logo" style="color: #0f172a;">
      <div class="brand-icon">G</div>
      <span>Gazoma Pay</span>
    </a>
    <nav class="web-nav">
      <a href="/solutions">Solutions</a>
      <a href="/pricing">Pricing</a>
      <a href="/developers">Developers</a>
      <a href="/security">Security</a>
      <a href="/about">About</a>
    </nav>
    <div style="display: flex; gap: 12px;">
      <a href="/login" class="btn btn-outline">Log In</a>
      <a href="/register" class="btn btn-primary">Get Started</a>
    </div>
  </header>

  <main>
    <?= $content ?>
  </main>

  <footer class="web-footer">
    <div class="footer-grid">
      <div>
        <div class="brand-logo" style="color: #fff; margin-bottom: 16px;">
          <div class="brand-icon">G</div>
          <span>Gazoma Pay</span>
        </div>
        <p style="font-size: 14px; line-height: 1.6;">Empowering digital businesses across Africa with modern payment infrastructure, smart links, and automated settlements.</p>
      </div>
      <div class="footer-col">
        <h4>Products</h4>
        <ul>
          <li><a href="/solutions">Payment Links</a></li>
          <li><a href="/solutions">API Checkout</a></li>
          <li><a href="/solutions">Invoicing</a></li>
          <li><a href="/solutions">Subscriptions</a></li>
        </ul>
      </div>
      <div class="footer-col">
        <h4>Developers</h4>
        <ul>
          <li><a href="/developers">API Reference</a></li>
          <li><a href="/developers">Webhooks</a></li>
          <li><a href="/developers">Sandbox SDK</a></li>
        </ul>
      </div>
      <div class="footer-col">
        <h4>Company</h4>
        <ul>
          <li><a href="/about">About Us</a></li>
          <li><a href="/security">Security & Compliance</a></li>
          <li><a href="/contact">Contact Support</a></li>
        </ul>
      </div>
    </div>
    <div style="border-top: 1px solid rgba(255,255,255,0.08); padding-top: 24px; display: flex; justify-content: space-between; font-size: 13px;">
      <p>&copy; <?= date('Y') ?> Gazoma Pay Limited. All rights reserved.</p>
      <p>Ghana Payment Infrastructure Sandbox v1.0-beta</p>
    </div>
  </footer>
</body>
</html>
