<?php $pageTitle = 'Browse Games - Video Game Shop'; ?>
<?php require __DIR__ . '/../layout/header.php'; ?>

<div class="mb-4">
    <h1 class="display-4">Browse Games</h1>
    <p class="lead">Discover and purchase your favorite video games</p>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <input type="text" class="form-control" id="searchInput" placeholder="Search games...">
            </div>
            <div class="col-md-4">
                <select class="form-select" id="genreFilter">
                    <option value="">All Genres</option>
                    <?php foreach ($genres as $genre): ?>
                        <option value="<?= htmlspecialchars($genre) ?>"><?= htmlspecialchars($genre) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <button class="btn btn-primary w-100" id="filterBtn">
                    <i class="bi bi-funnel"></i> Filter
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Games Grid -->
<div class="row row-cols-1 row-cols-md-3 row-cols-lg-4 g-4" id="gamesGrid">
    <?php foreach ($games as $game): ?>
        <div class="col game-item" data-title="<?= htmlspecialchars(strtolower($game['title'])) ?>" data-genre="<?= htmlspecialchars($game['genre'] ?? '') ?>">
            <div class="card game-card h-100">
                <?php if ($game['image_url']): ?>
                    <img src="<?= htmlspecialchars($game['image_url']) ?>" class="card-img-top" alt="<?= htmlspecialchars($game['title']) ?>" style="height: 200px; object-fit: fill;">
                <?php else: ?>
                    <div class="card-img-top bg-secondary d-flex align-items-center justify-content-center" style="height: 200px;">
                        <i class="bi bi-controller" style="font-size: 4rem; color: white;"></i>
                    </div>
                <?php endif; ?>
                
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title"><?= htmlspecialchars($game['title']) ?></h5>
                    
                    <?php if (isset($game['genre'])): ?>
                        <span class="badge bg-secondary mb-2"><?= htmlspecialchars($game['genre']) ?></span>
                    <?php endif; ?>
                    
                    <p class="card-text flex-grow-1"><?= htmlspecialchars(substr($game['description'] ?? '', 0, 100)) ?>...</p>
                    
                    <div class="mb-2">
                        <?php 
                        $rating = $game['average_rating'] ?? 0;
                        for ($i = 1; $i <= 5; $i++): ?>
                            <i class="bi bi-star<?= $i <= $rating ? '-fill' : '' ?> rating"></i>
                        <?php endfor; ?>
                        <span class="ms-2">(<?= number_format($rating, 1) ?>)</span>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="h5 mb-0 text-primary">$<?= number_format($game['price'], 2) ?></span>
                        <a href="/game/<?= $game['id'] ?>" class="btn btn-primary btn-sm">
                            <i class="bi bi-eye"></i> View
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<?php if (empty($games)): ?>
    <div class="alert alert-info text-center">
        <i class="bi bi-info-circle"></i> No games available at the moment.
    </div>
<?php endif; ?>

<script>
// Search and filter functionality
document.getElementById('filterBtn').addEventListener('click', filterGames);
document.getElementById('searchInput').addEventListener('keyup', function(e) {
    if (e.key === 'Enter') filterGames();
});

function filterGames() {
    const search = document.getElementById('searchInput').value.toLowerCase();
    const genre = document.getElementById('genreFilter').value.toLowerCase();
    const items = document.querySelectorAll('.game-item');
    
    items.forEach(item => {
        const title = item.getAttribute('data-title');
        const itemGenre = item.getAttribute('data-genre').toLowerCase();
        
        const matchesSearch = search === '' || title.includes(search);
        const matchesGenre = genre === '' || itemGenre === genre;
        
        if (matchesSearch && matchesGenre) {
            item.style.display = '';
        } else {
            item.style.display = 'none';
        }
    });
}
</script>

<?php require __DIR__ . '/../layout/footer.php'; ?>
