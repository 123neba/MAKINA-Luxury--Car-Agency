<?php include 'views/partials/header.php'; ?>
<div class="auth-container">
    <div class="auth-card">
        <div style="text-align: center; margin-bottom: 24px;">
            <i class="ri-shield-user-line" style="font-size: 48px; color: var(--gold-solid); text-shadow: var(--gold-glow);"></i>
        </div>
        <h2>Welcome Back</h2>
        <p style="color: var(--text-secondary); font-size: 14px; margin-bottom: 24px; text-align: center;">Sign in to your premium Makina workspace</p>
        
        <?php if(!empty($error)): ?>
            <div class="alert danger">
                <i class="ri-error-warning-line" style="font-size: 18px;"></i>
                <span><?= htmlspecialchars($error) ?></span>
            </div>
        <?php endif; ?>
        
        <form action="index.php?action=postLogin" method="POST">
            <div class="form-group">
                <label><i class="ri-mail-line"></i> Email Address</label>
                <input type="email" name="email" required placeholder="name@domain.com">
            </div>
            <div class="form-group">
                <label><i class="ri-lock-line"></i> Password</label>
                <input type="password" name="password" required placeholder="••••••••">
            </div>
            <button type="submit" class="btn btn-primary btn-block">
                <span>Sign In</span> <i class="ri-arrow-right-line"></i>
            </button>
        </form>
        <p class="auth-link" style="margin-top: 24px; text-align: center; font-size: 14px; color: var(--text-secondary);">
            Don't have an account? <a href="index.php?action=signup" style="font-weight: 600;">Sign up</a>
        </p>
    </div>
</div>
<?php include 'views/partials/footer.php'; ?>
