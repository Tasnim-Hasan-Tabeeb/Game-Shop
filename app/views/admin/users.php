<?php $pageTitle = 'Manage Users - Admin - Video Game Shop'; ?>
<?php require __DIR__ . '/../layout/header.php'; ?>

<div class="mb-4">
    <h1 class="display-5">Manage Users</h1>
    <p class="lead">View and manage registered users</p>
</div>

<div class="card">
    <div class="card-body">
        <!-- Filter -->
        <div class="mb-3">
            <label for="roleFilter" class="form-label">Filter by Role:</label>
            <select class="form-select w-auto" id="roleFilter" onchange="filterUsers()">
                <option value="">All</option>
                <option value="client">Clients</option>
                <option value="admin">Admins</option>
            </select>
        </div>
        
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Registered</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="usersTable">
                    <?php foreach ($users as $userItem): ?>
                        <tr data-role="<?= $userItem['role'] ?>" id="user-row-<?= $userItem['id'] ?>">
                            <td><?= $userItem['id'] ?></td>
                            <td>
                                <strong><?= htmlspecialchars($userItem['username']) ?></strong>
                            </td>
                            <td><?= htmlspecialchars($userItem['email']) ?></td>
                            <td>
                                <?php if ($userItem['role'] === 'admin'): ?>
                                    <span class="badge bg-danger">Admin</span>
                                <?php else: ?>
                                    <span class="badge bg-primary">Client</span>
                                <?php endif; ?>
                            </td>
                            <td><?= date('M d, Y', strtotime($userItem['created_at'])) ?></td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="/admin/users/edit/<?= $userItem['id'] ?>" class="btn btn-primary" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button class="btn btn-danger" onclick="deleteUser(<?= $userItem['id'] ?>, this)" title="Delete" data-confirm="Are you sure you want to delete this user?">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <script>
            const CSRF_TOKEN = '<?= htmlspecialchars($csrfToken) ?>';

            async function deleteUser(id, btn) {
                if (!confirm('Are you sure you want to delete this user? This action is permanent.')) {
                    return;
                }

                try {
                    const res = await fetch('/admin/users/delete/' + id, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-Token': CSRF_TOKEN
                        },
                        body: JSON.stringify({})
                    });

                    const data = await res.json();

                    if (data.success) {
                        const row = document.getElementById('user-row-' + id);
                        if (row) row.remove();
                    } else {
                        alert(data.message || 'Failed to delete user');
                    }
                } catch (err) {
                    alert('Failed to delete user');
                }
            }
        </script>
        
        <?php if (empty($users)): ?>
            <p class="text-muted text-center my-4">No users found.</p>
        <?php endif; ?>
    </div>
</div>

<script>
function filterUsers() {
    const role = document.getElementById('roleFilter').value;
    const rows = document.querySelectorAll('#usersTable tr');
    
    rows.forEach(row => {
        const rowRole = row.getAttribute('data-role');
        if (role === '' || rowRole === role) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}
</script>

<?php require __DIR__ . '/../layout/footer.php'; ?>
