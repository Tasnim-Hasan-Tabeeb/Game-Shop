<?php $pageTitle = ($isEdit ? 'Edit User - Admin - Video Game Shop' : 'Create User - Admin - Video Game Shop'); ?>
<?php require __DIR__ . '/../layout/header.php'; ?>

<div class="mb-4">
    <h1 class="display-5"><?= $isEdit ? 'Edit User' : 'Create User' ?></h1>
    <p class="lead"><?= $isEdit ? 'Modify user details' : 'Add a new user' ?></p>
</div>

<div class="card">
    <div class="card-body">
        <form method="post" action="/admin/users/save/<?= $isEdit ? (int)$userItem['id'] : '' ?>">
            <?= \App\Middleware\CSRF::field() ?>

            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" id="username" name="username" class="form-control" required value="<?= $isEdit ? htmlspecialchars($userItem['username']) : '' ?>">
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" id="email" name="email" class="form-control" required value="<?= $isEdit ? htmlspecialchars($userItem['email']) : '' ?>">
            </div>

            <div class="mb-3">
                <label for="role" class="form-label">Role</label>
                <select id="role" name="role" class="form-select w-auto">
                    <option value="client" <?= $isEdit && $userItem['role'] === 'client' ? 'selected' : '' ?>>Client</option>
                    <option value="admin" <?= $isEdit && $userItem['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password (leave blank to keep current)</label>
                <input type="password" id="password" name="password" class="form-control">
            </div>

            <div class="d-flex gap-2">
                <button class="btn btn-primary" type="submit"><?= $isEdit ? 'Save Changes' : 'Create User' ?></button>
                <a href="/admin/users" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>