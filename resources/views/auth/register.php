<form action="/register" method="POST">
    <input type="hidden" name="csrf_token" value="<?= Auth::generateCsrfToken() ?>">

    <div class="form-group">
        <label class="form-label">Business / Company Name</label>
        <input type="text" name="company_name" class="form-control" placeholder="Gazoma Tech Ltd" required>
    </div>

    <div class="form-group">
        <label class="form-label">Full Name</label>
        <input type="text" name="name" class="form-control" placeholder="John Mensah" required>
    </div>

    <div class="form-group">
        <label class="form-label">Email Address</label>
        <input type="email" name="email" class="form-control" placeholder="admin@gazomatech.com" required>
    </div>

    <div class="form-group">
        <label class="form-label">Password</label>
        <input type="password" name="password" class="form-control" placeholder="••••••••" required>
    </div>

    <button type="submit" class="btn btn-primary" style="width: 100%; padding: 12px; margin-top: 8px;">
        Register Merchant Platform
    </button>
</form>

<div style="text-align: center; margin-top: 24px; font-size: 13px; color: var(--text-muted);">
    Already registered? <a href="/login" style="font-weight: 700;">Sign in here</a>
</div>
