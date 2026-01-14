<?php $pageTitle = 'Payment Successful - Video Game Shop'; ?>
<?php require __DIR__ . '/../layout/header.php'; ?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card text-center">
            <div class="card-body py-5">
                <i class="bi bi-check-circle-fill text-success" style="font-size: 5rem;"></i>
                
                <h2 class="mt-4">Payment Successful!</h2>
                
                <p class="lead">Thank you for your purchase.</p>
                
                <?php if ($purchase): ?>
                    <div class="alert alert-info">
                        <strong>Transaction ID:</strong><br>
                        <small><?= htmlspecialchars($purchase['transaction_id']) ?></small>
                    </div>
                    
                    <p>Your game has been added to your library and is ready to download.</p>
                <?php endif; ?>
                
                <div class="d-grid gap-2 mt-4">
                    <a href="/dashboard" class="btn btn-primary btn-lg">
                        <i class="bi bi-collection"></i> Go to My Games
                    </a>
                    <a href="/" class="btn btn-outline-secondary">
                        <i class="bi bi-house"></i> Browse More Games
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
