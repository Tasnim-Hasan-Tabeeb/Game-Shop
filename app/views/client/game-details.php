<?php $pageTitle = htmlspecialchars($game['title']) . ' - Video Game Shop'; ?>
<?php require __DIR__ . '/../layout/header.php'; ?>

<div class="row">
    <div class="col-lg-8">
        <div class="card mb-4">
            <?php if ($game['image_url']): ?>
                <img src="<?= htmlspecialchars($game['image_url']) ?>" class="card-img-top" alt="<?= htmlspecialchars($game['title']) ?>" style="max-height: 400px; object-fit: cover;">
            <?php else: ?>
                <div class="card-img-top bg-secondary d-flex align-items-center justify-content-center" style="height: 400px;">
                    <i class="bi bi-controller" style="font-size: 6rem; color: white;"></i>
                </div>
            <?php endif; ?>
            
            <div class="card-body">
                <h1 class="display-5"><?= htmlspecialchars($game['title']) ?></h1>
                
                <div class="mb-3">
                    <?php if (isset($game['genre'])): ?>
                        <span class="badge bg-primary"><?= htmlspecialchars($game['genre']) ?></span>
                    <?php endif; ?>
                    
                    <?php if (isset($game['publisher'])): ?>
                        <span class="badge bg-secondary"><?= htmlspecialchars($game['publisher']) ?></span>
                    <?php endif; ?>
                </div>
                
                <div class="mb-3">
                    <?php 
                    $rating = $averageRating ?? 0;
                    for ($i = 1; $i <= 5; $i++): ?>
                        <i class="bi bi-star<?= $i <= $rating ? '-fill' : '' ?> rating"></i>
                    <?php endfor; ?>
                    <span class="ms-2"><?= number_format($rating, 1) ?> (<?= count($reviews) ?> reviews)</span>
                </div>
                
                <h3 class="text-primary mb-4">$<?= number_format($game['price'], 2) ?></h3>
                
                <h4>Description</h4>
                <p><?= nl2br(htmlspecialchars($game['description'])) ?></p>
                
                <?php if (isset($game['release_date'])): ?>
                    <p><strong>Release Date:</strong> <?= date('M d, Y', strtotime($game['release_date'])) ?></p>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Reviews Section -->
        <div class="card">
            <div class="card-header">
                <h4><i class="bi bi-chat-square-text"></i> Reviews</h4>
            </div>
            <div class="card-body">
                <?php if ($userOwnsGame && !$hasReviewed && \App\Middleware\Auth::check()): ?>
                    <div class="mb-4">
                        <h5>Write a Review</h5>
                        <form id="reviewForm">
                            <?= \App\Middleware\CSRF::field() ?>
                            <input type="hidden" name="game_id" value="<?= $game['id'] ?>">
                            
                            <div class="mb-3">
                                <label class="form-label">Rating</label>
                                <div id="ratingStars">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <i class="bi bi-star star-rating" data-rating="<?= $i ?>" style="font-size: 2rem; cursor: pointer; color: #fbbf24;"></i>
                                    <?php endfor; ?>
                                </div>
                                <input type="hidden" name="rating" id="ratingInput" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="comment" class="form-label">Comment</label>
                                <textarea class="form-control" id="comment" name="comment" rows="3"></textarea>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-send"></i> Submit Review
                            </button>
                        </form>
                    </div>
                    <hr>
                <?php endif; ?>
                
                <?php if (!empty($reviews)): ?>
                    <?php foreach ($reviews as $review): ?>
                        <div class="mb-3 pb-3 border-bottom">
                            <div class="d-flex justify-content-between">
                                <h6><?= htmlspecialchars($review['username']) ?></h6>
                                <small class="text-muted"><?= date('M d, Y', strtotime($review['created_at'])) ?></small>
                            </div>
                            
                            <div class="mb-2">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <i class="bi bi-star<?= $i <= $review['rating'] ? '-fill' : '' ?> rating"></i>
                                <?php endfor; ?>
                            </div>
                            
                            <?php if ($review['comment']): ?>
                                <p><?= nl2br(htmlspecialchars($review['comment'])) ?></p>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-muted">No reviews yet. Be the first to review this game!</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card sticky-top" style="top: 20px;">
            <div class="card-body">
                <h3 class="text-primary mb-3">$<?= number_format($game['price'], 2) ?></h3>
                
                <?php if ($userOwnsGame): ?>
                    <div class="alert alert-success">
                        <i class="bi bi-check-circle"></i> You own this game
                    </div>
                    <a href="/dashboard" class="btn btn-success btn-lg w-100">
                        <i class="bi bi-collection"></i> Go to My Games
                    </a>
                <?php elseif (\App\Middleware\Auth::check()): ?>
                    <button id="buyBtn" class="btn btn-primary btn-lg w-100 mb-3">
                        <i class="bi bi-cart-plus"></i> Buy Now
                    </button>
                <?php else: ?>
                    <a href="/login" class="btn btn-primary btn-lg w-100 mb-3">
                        <i class="bi bi-box-arrow-in-right"></i> Login to Purchase
                    </a>
                <?php endif; ?>
                
                <div class="mt-3">
                    <h5>Features</h5>
                    <ul class="list-unstyled">
                        <li><i class="bi bi-check-circle text-success"></i> Instant download</li>
                        <li><i class="bi bi-check-circle text-success"></i> Lifetime access</li>
                        <li><i class="bi bi-check-circle text-success"></i> Play offline</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Rating stars interaction
const stars = document.querySelectorAll('.star-rating');
const ratingInput = document.getElementById('ratingInput');

stars.forEach(star => {
    star.addEventListener('click', function() {
        const rating = this.getAttribute('data-rating');
        ratingInput.value = rating;
        
        stars.forEach((s, i) => {
            if (i < rating) {
                s.classList.remove('bi-star');
                s.classList.add('bi-star-fill');
            } else {
                s.classList.remove('bi-star-fill');
                s.classList.add('bi-star');
            }
        });
    });
});

// Review form submission
const reviewForm = document.getElementById('reviewForm');
if (reviewForm) {
    reviewForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const data = {
            game_id: formData.get('game_id'),
            rating: parseInt(formData.get('rating')),
            comment: formData.get('comment')
        };
        
        if (!data.rating) {
            alert('Please select a rating');
            return;
        }
        
        try {
            const response = await fetch('/api/reviews', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': formData.get('csrf_token')
                },
                body: JSON.stringify(data)
            });
            
            const result = await response.json();
            
            if (result.success) {
                alert('Review submitted successfully!');
                location.reload();
            } else {
                alert(result.message);
            }
        } catch (error) {
            alert('An error occurred. Please try again.');
        }
    });
}

// Buy button
const buyBtn = document.getElementById('buyBtn');
if (buyBtn) {
    buyBtn.addEventListener('click', async function() {
        const gameId = <?= $game['id'] ?>;
        
        try {
            const response = await fetch('/api/purchases', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ game_id: gameId })
            });
            
            const data = await response.json();
            
            if (data.success) {
                window.location.href = data.data.payment_url;
            } else {
                alert(data.message);
            }
        } catch (error) {
            alert('An error occurred. Please try again.');
        }
    });
}
</script>

<?php require __DIR__ . '/../layout/footer.php'; ?>
