<?php include 'views/partials/header.php'; ?>
<div class="auth-container">
    <div class="auth-card auth-card-large">
        <div style="text-align: center; margin-bottom: 24px;">
            <i class="ri-user-add-line" style="font-size: 48px; color: var(--gold-solid); text-shadow: var(--gold-glow);"></i>
        </div>
        <h2>Create an Account</h2>
        <p style="color: var(--text-secondary); font-size: 14px; margin-bottom: 24px; text-align: center;">Join Ethiopia's premier automotive agency</p>
        
        <?php if(!empty($error)): ?>
            <div class="alert danger">
                <i class="ri-error-warning-line" style="font-size: 18px;"></i>
                <span><?= htmlspecialchars($error) ?></span>
            </div>
        <?php endif; ?>
        
        <form action="index.php?action=postSignup" method="POST">
            <div class="form-group">
                <label><i class="ri-question-answer-line"></i> Select Your Role</label>
                <select name="role" required>
                    <option value="customer" selected>Customer (Buy cars)</option>
                    <option value="seller">Seller (Sell cars)</option>
                </select>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label><i class="ri-user-smile-line"></i> First Name</label>
                    <input type="text" name="first_name" placeholder="e.g. Matt" required>
                </div>
                <div class="form-group">
                    <label>Last Name</label>
                    <input type="text" name="last_name" placeholder="e.g. Mes" required>
                </div>
            </div>
            
            <div class="form-row align-end">
                <div class="form-group" style="flex: 2;">
                    <label><i class="ri-calendar-event-line"></i> Date of Birth</label>
                    <input type="date" name="dob" id="dob" required>
                </div>
                <div class="form-group" style="flex: 1;">
                    <label>Age</label>
                    <input type="number" id="age" name="age" readonly placeholder="Auto-calculated">
                </div>
            </div>
            
            <div class="form-group">
                <label><i class="ri-phone-line"></i> Phone Number</label>
                <input type="text" name="phone_number" placeholder="e.g. 0987279321" required>
            </div>
            
            <div class="form-group">
                <label><i class="ri-mail-line"></i> Email Address</label>
                <input type="email" name="email" placeholder="name@domain.com" required>
            </div>
            
            <div class="form-group">
                <label><i class="ri-lock-line"></i> Secure Password</label>
                <input type="password" name="password" placeholder="••••••••" required>
            </div>
            
            <button type="submit" class="btn btn-primary btn-block" style="margin-top: 12px;">
                <span>Create Premium Account</span> <i class="ri-arrow-right-line"></i>
            </button>
        </form>
        
        <p class="auth-link" style="margin-top: 24px; text-align: center; font-size: 14px; color: var(--text-secondary);">
            Already have an account? <a href="index.php?action=login" style="font-weight: 600;">Login</a>
        </p>
    </div>
</div>
<?php include 'views/partials/footer.php'; ?>
