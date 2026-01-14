<?php $pageTitle = 'Reset Password - Video Game Shop'; ?>
<?php require __DIR__ . '/../layout/header.php'; ?>

<div class="row justify-content-center mt-5">
    <div class="col-md-5">
        <div class="card shadow-sm">
            <div class="card-body p-5">
                <h2 class="text-center mb-4">
                    <i class="bi bi-key"></i> Reset Password
                </h2>
                
                <p class="text-center text-muted mb-4">
                    Enter your email and choose a new password.
                </p>
                
                <div id="resetMessage"></div>
                
                <form id="resetForm">
                    <?= $csrfToken ? \App\Middleware\CSRF::field() : '' ?>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="new_password" class="form-label">New Password</label>
                        <input type="password" class="form-control" id="new_password" name="new_password" required minlength="6">
                        <small class="text-muted">At least 6 characters</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Confirm New Password</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100 mb-3">
                        <i class="bi bi-lock-fill"></i> Reset Password
                    </button>
                    
                    <div class="text-center">
                        <a href="/login">Back to Login</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('resetForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const messageDiv = document.getElementById('resetMessage');
    
    // Validate passwords match
    const newPassword = formData.get('new_password');
    const confirmPassword = formData.get('confirm_password');
    
    if (newPassword !== confirmPassword) {
        messageDiv.innerHTML = '<div class="alert alert-danger">Passwords do not match</div>';
        return;
    }
    
    // Validate password length
    if (newPassword.length < 6) {
        messageDiv.innerHTML = '<div class="alert alert-danger">Password must be at least 6 characters</div>';
        return;
    }
    
    try {
        const response = await fetch('/api/reset-password', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            messageDiv.innerHTML = '<div class="alert alert-success">' + data.message + '</div>';
            this.reset();
            
            // Redirect to login after 2 seconds
            setTimeout(() => {
                window.location.href = '/login';
            }, 2000);
        } else {
            messageDiv.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
        }
    } catch (error) {
        messageDiv.innerHTML = '<div class="alert alert-danger">An error occurred. Please try again.</div>';
    }
});
</script>

<?php require __DIR__ . '/../layout/footer.php'; ?>
