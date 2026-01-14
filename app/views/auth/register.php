<?php $pageTitle = 'Register - Video Game Shop'; ?>
<?php require __DIR__ . '/../layout/header.php'; ?>

<div class="row justify-content-center mt-5">
    <div class="col-md-5">
        <div class="card shadow-sm">
            <div class="card-body p-5">
                <h2 class="text-center mb-4">
                    <i class="bi bi-person-plus"></i> Register
                </h2>
                
                <div id="registerMessage"></div>
                
                <form id="registerForm">
                    <?= $csrfToken ? \App\Middleware\CSRF::field() : '' ?>
                    
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required minlength="6">
                        <small class="text-muted">At least 6 characters</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Confirm Password</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100 mb-3">
                        <i class="bi bi-person-plus"></i> Register
                    </button>
                    
                    <div class="text-center">
                        Already have an account? <a href="/login">Login</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('registerForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const messageDiv = document.getElementById('registerMessage');
    
    // Check if passwords match
    if (formData.get('password') !== formData.get('confirm_password')) {
        messageDiv.innerHTML = '<div class="alert alert-danger">Passwords do not match</div>';
        return;
    }
    
    try {
        const response = await fetch('/api/register', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            messageDiv.innerHTML = '<div class="alert alert-success">Registration successful! Redirecting...</div>';
            setTimeout(() => {
                window.location.href = data.redirect || '/dashboard';
            }, 1000);
        } else {
            messageDiv.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
        }
    } catch (error) {
        messageDiv.innerHTML = '<div class="alert alert-danger">An error occurred. Please try again.</div>';
    }
});
</script>

<?php require __DIR__ . '/../layout/footer.php'; ?>
