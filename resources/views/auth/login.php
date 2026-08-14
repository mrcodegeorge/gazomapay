<form action="/login" method="POST">
    <input type="hidden" name="csrf_token" value="<?= Auth::generateCsrfToken() ?>">
    
    <div class="form-group">
        <label class="form-label">Email Address</label>
        <input type="email" name="email" class="form-control" value="admin@gazomapay.com" required>
    </div>

    <div class="form-group">
        <label class="form-label">Password</label>
        <input type="password" name="password" class="form-control" value="password123" required>
    </div>

    <button type="submit" class="btn btn-primary" style="width: 100%; padding: 12px; margin-top: 8px;">
        Sign In to Dashboard
    </button>
</form>

<div style="text-align: center; margin-top: 24px; font-size: 13px; color: var(--text-muted);">
    Don't have an account? <a href="/register" style="font-weight: 700;">Create merchant account</a>
</div>
