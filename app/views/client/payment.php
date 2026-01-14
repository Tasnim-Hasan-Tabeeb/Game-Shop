<?php $pageTitle = 'Payment - Video Game Shop'; ?>
<?php require __DIR__ . '/../layout/header.php'; ?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h4><i class="bi bi-credit-card"></i> Complete Your Purchase</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5>Order Summary</h5>
                        <hr>
                        
                        <div class="mb-3">
                            <strong>Game:</strong> <?= htmlspecialchars($game['title']) ?>
                        </div>
                        
                        <div class="mb-3">
                            <strong>Price:</strong> $<?= number_format($purchase['amount'], 2) ?>
                        </div>
                        
                        <div class="mb-3">
                            <strong>Transaction ID:</strong> <small><?= htmlspecialchars($purchase['transaction_id']) ?></small>
                        </div>
                        
                        <hr>
                        
                        <h4>Total: $<?= number_format($purchase['amount'], 2) ?></h4>
                    </div>
                    
                    <div class="col-md-6">
                        <h5>Payment Information</h5>
                        <hr>
                        
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i> This is a demo payment gateway. In production, integrate with Stripe, PayPal, or other payment processors.
                        </div>
                        
                        <form id="paymentForm" method="POST" action="/payment/<?= htmlspecialchars($purchase['transaction_id']) ?>/process">
                            <?= \App\Middleware\CSRF::field() ?>
                            
                            <div class="mb-3">
                                <label for="cardNumber" class="form-label">Card Number</label>
                                <input type="text" class="form-control" id="cardNumber" placeholder="1234 5678 9012 3456" required>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="expiry" class="form-label">Expiry Date</label>
                                    <input type="text" class="form-control" id="expiry" placeholder="MM/YY" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="cvv" class="form-label">CVV</label>
                                    <input type="text" class="form-control" id="cvv" placeholder="123" required>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="cardName" class="form-label">Cardholder Name</label>
                                <input type="text" class="form-control" id="cardName" placeholder="John Doe" required>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-success btn-lg">
                                    <i class="bi bi-lock"></i> Pay $<?= number_format($purchase['amount'], 2) ?>
                                </button>
                                <a href="/game/<?= $game['id'] ?>" class="btn btn-outline-secondary">
                                    Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="text-center mt-3 text-muted">
            <i class="bi bi-shield-check"></i> Secure payment processing
        </div>
    </div>
</div>

<script>
// Simple card number formatting
document.getElementById('cardNumber').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\s+/g, '').replace(/[^0-9]/gi, '');
    let formattedValue = value.match(/.{1,4}/g)?.join(' ') || value;
    e.target.value = formattedValue;
});

// Expiry date formatting
document.getElementById('expiry').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\s+/g, '').replace(/[^0-9]/gi, '');
    if (value.length >= 2) {
        e.target.value = value.slice(0, 2) + '/' + value.slice(2, 4);
    } else {
        e.target.value = value;
    }
});

// CVV formatting
document.getElementById('cvv').addEventListener('input', function(e) {
    e.target.value = e.target.value.replace(/[^0-9]/gi, '').slice(0, 3);
});
</script>

<?php require __DIR__ . '/../layout/footer.php'; ?>
