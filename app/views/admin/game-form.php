<?php $pageTitle = ($isEdit ? 'Edit' : 'Add New') . ' Game - Admin - Video Game Shop'; ?>
<?php require __DIR__ . '/../layout/header.php'; ?>

<div class="mb-4">
    <h1 class="display-5"><?= $isEdit ? 'Edit' : 'Add New' ?> Game</h1>
    <p class="lead">Fill in the game details below</p>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <div id="message"></div>
                
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success"><?= htmlspecialchars($_SESSION['success']) ?></div>
                    <?php unset($_SESSION['success']); ?>
                <?php endif; ?>
                
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($_SESSION['error']) ?></div>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>
                
                <form id="gameForm" method="POST" action="<?= $isEdit ? "/admin/games/save/{$game['id']}" : '/admin/games/save' ?>" enctype="multipart/form-data">
                    <?= \App\Middleware\CSRF::field() ?>
                    <input type="hidden" name="existing_image_url" value="<?= htmlspecialchars($game['image_url'] ?? '') ?>">
                    
                    <div class="mb-3">
                        <label for="title" class="form-label">Title *</label>
                        <input type="text" class="form-control" id="title" name="title" value="<?= htmlspecialchars($game['title'] ?? '') ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="5"><?= htmlspecialchars($game['description'] ?? '') ?></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="price" class="form-label">Price *</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" class="form-control" id="price" name="price" step="0.01" min="0" value="<?= $game['price'] ?? '' ?>" required>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="genre" class="form-label">Genre</label>
                            <input type="text" class="form-control" id="genre" name="genre" value="<?= htmlspecialchars($game['genre'] ?? '') ?>">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="publisher" class="form-label">Publisher</label>
                            <input type="text" class="form-control" id="publisher" name="publisher" value="<?= htmlspecialchars($game['publisher'] ?? '') ?>">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="release_date" class="form-label">Release Date</label>
                            <input type="date" class="form-control" id="release_date" name="release_date" value="<?= $game['release_date'] ?? '' ?>">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <?php if (!empty($game['image_url'])): ?>
                            <div class="mb-2">
                                <img src="<?= htmlspecialchars($game['image_url']) ?>" alt="Current image" class="img-thumbnail" style="max-width: 200px;">
                                <p class="text-muted small">Current image</p>
                            </div>
                        <?php endif; ?>
                        
                        <input type="file" class="form-control" id="image" name="image" accept="image/jpeg,image/jpg,image/png,image/webp">
                        <small class="text-muted">Upload a new image (JPG, PNG, WEBP - Max 5MB). Recommended: 600x800px</small>
                    </div>
                    
                    
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="is_active" name="is_active" <?= ($game['is_active'] ?? true) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="is_active">
                            Active (visible to customers)
                        </label>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary" id="submitBtn">
                            <i class="bi bi-save"></i> <?= $isEdit ? 'Update' : 'Create' ?> Game
                        </button>
                        <a href="/admin/games" class="btn btn-secondary">
                            <i class="bi bi-x-circle"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5>Tips</h5>
            </div>
            <div class="card-body">
                <ul class="small">
                    <li>Use high-quality cover images (recommended: 600x800px)</li>
                    <li>Write detailed descriptions to help customers</li>
                    <li>Set competitive prices</li>
                    <li>Provide valid download URLs for purchased games</li>
                    <li>Mark as inactive to hide from customers without deleting</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
// Preview image before upload
document.getElementById('image').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        // Validate file size
        if (file.size > 5 * 1024 * 1024) {
            alert('File size must be less than 5MB');
            this.value = '';
            return;
        }
        
        // Validate file type
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
        if (!allowedTypes.includes(file.type)) {
            alert('Only JPG, PNG, and WEBP images are allowed');
            this.value = '';
            return;
        }
    }
});

// Show loading on submit
document.getElementById('gameForm').addEventListener('submit', function() {
    const submitBtn = document.getElementById('submitBtn');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Saving...';
});
</script>

<?php require __DIR__ . '/../layout/footer.php'; ?>
