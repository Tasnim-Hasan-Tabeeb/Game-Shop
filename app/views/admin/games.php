<?php $pageTitle = 'Manage Games - Admin - Video Game Shop'; ?>
<?php require __DIR__ . '/../layout/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="display-5">Manage Games</h1>
        <p class="lead">Add, edit, or remove games from your shop</p>
    </div>
    <a href="/admin/games/new" class="btn btn-primary btn-lg">
        <i class="bi bi-plus-circle"></i> Add New Game
    </a>
</div>

<div id="message"></div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Genre</th>
                        <th>Price</th>
                        <th>Publisher</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($games as $game): ?>
                        <tr>
                            <td><?= $game['id'] ?></td>
                            <td>
                                <strong><?= htmlspecialchars($game['title']) ?></strong>
                            </td>
                            <td>
                                <?php if ($game['genre']): ?>
                                    <span class="badge bg-secondary"><?= htmlspecialchars($game['genre']) ?></span>
                                <?php endif; ?>
                            </td>
                            <td>$<?= number_format($game['price'], 2) ?></td>
                            <td><?= htmlspecialchars($game['publisher'] ?? 'N/A') ?></td>
                            <td>
                                <?php if ($game['is_active']): ?>
                                    <span class="badge bg-success">Active</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="/game/<?= $game['id'] ?>" class="btn btn-info" title="View">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="/admin/games/edit/<?= $game['id'] ?>" class="btn btn-primary" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button class="btn btn-danger" onclick="deleteGame(<?= $game['id'] ?>)" title="Delete" data-confirm="Are you sure you want to delete this game?">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
async function deleteGame(gameId) {
    if (!confirm('Are you sure you want to delete this game?')) {
        return;
    }
    
    try {
        const response = await fetch(`/api/games/${gameId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json'
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            document.getElementById('message').innerHTML = '<div class="alert alert-success">Game deleted successfully!</div>';
            setTimeout(() => {
                location.reload();
            }, 1000);
        } else {
            document.getElementById('message').innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
        }
    } catch (error) {
        document.getElementById('message').innerHTML = '<div class="alert alert-danger">An error occurred</div>';
    }
}
</script>

<?php require __DIR__ . '/../layout/footer.php'; ?>
