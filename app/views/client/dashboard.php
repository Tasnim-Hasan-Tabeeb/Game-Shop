<?php $pageTitle = 'My Games - Video Game Shop'; ?>
<?php require __DIR__ . '/../layout/header.php'; ?>

<div class="mb-4">
    <h1 class="display-4">My Games</h1>
    <p class="lead">Your purchased games library</p>
</div>

<?php if (!empty($purchases)): ?>
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
        <?php foreach ($purchases as $purchase): ?>
            <div class="col">
                <div class="card game-card h-100">
                    <?php if ($purchase['image_url']): ?>
                        <img src="<?= htmlspecialchars($purchase['image_url']) ?>" class="card-img-top" alt="<?= htmlspecialchars($purchase['game_title']) ?>">
                    <?php else: ?>
                        <div class="card-img-top bg-secondary d-flex align-items-center justify-content-center" style="height: 200px;">
                            <i class="bi bi-controller" style="font-size: 4rem; color: white;"></i>
                        </div>
                    <?php endif; ?>
                    
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title"><?= htmlspecialchars($purchase['game_title']) ?></h5>
                        
                        <p class="card-text text-muted">
                            Purchased on <?= date('M d, Y', strtotime($purchase['purchase_date'])) ?>
                        </p>
                        
                        <p class="card-text flex-grow-1">
                            <?= htmlspecialchars(substr($purchase['description'] ?? '', 0, 100)) ?>...
                        </p>
                        
                        <div class="d-grid gap-2">
                            <?php if ($purchase['download_url']): ?>
                                <a href="<?= htmlspecialchars($purchase['download_url']) ?>" class="btn btn-success">
                                    <i class="bi bi-download"></i> Download Game
                                </a>
                            <?php else: ?>
                                <button class="btn btn-secondary" disabled>
                                    <i class="bi bi-hourglass-split"></i> Coming Soon
                                </button>
                            <?php endif; ?>
                            
                            <a href="/game/<?= $purchase['game_id'] ?>" class="btn btn-outline-primary">
                                <i class="bi bi-info-circle"></i> View Details
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="bi bi-inbox" style="font-size: 4rem; color: #cbd5e1;"></i>
            <h4 class="mt-3">No Games Yet</h4>
            <p class="text-muted">You haven't purchased any games. Start browsing our collection!</p>
            <a href="/" class="btn btn-primary">
                <i class="bi bi-shop"></i> Browse Games
            </a>
        </div>
    </div>
<?php endif; ?>

<?php require __DIR__ . '/../layout/footer.php'; ?>
