<?php $pageTitle = 'Login - Video Game Shop'; ?>
<?php require __DIR__ . '/../layout/header.php'; ?>

<div class="row justify-content-center mt-5">
    <div class="col-md-5">
        <div class="card shadow-sm">
            <div class="card-body p-5">
                <h2 class="text-center mb-4">
                    <i class="bi bi-controller"></i> Login
                </h2>
                
                <div id="loginMessage"></div>
                
                <form id="loginForm">
                    <?= $csrfToken ? \App\Middleware\CSRF::field() : '' ?>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    
                    <div class="mb-3">
                        <a href="/forgot-password" class="text-decoration-none">Reset password</a>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100 mb-3">
                        <i class="bi bi-box-arrow-in-right"></i> Login
                    </button>
                    
                    <div class="text-center">
                        Don't have an account? <a href="/register">Register</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('loginForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const messageDiv = document.getElementById('loginMessage');
    
    try {
        const response = await fetch('/api/login', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            messageDiv.innerHTML = '<div class="alert alert-success">Login successful! Redirecting...</div>';
            setTimeout(() => {
                window.location.href = data.redirect || '/';
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
