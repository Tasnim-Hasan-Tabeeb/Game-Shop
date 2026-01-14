<?php $pageTitle = 'Admin Dashboard - Video Game Shop'; ?>
<?php require __DIR__ . '/../layout/header.php'; ?>

<div class="mb-4">
    <h1 class="display-4">Admin Dashboard</h1>
    <p class="lead">Manage your video game shop</p>
</div>

<div class="row mb-4">
    <div class="col-md-4 mb-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-white-50">Total Revenue</h6>
                    <h2>$<?= number_format($stats['total_revenue'] ?? 0, 2) ?></h2>
                </div>
                <i class="bi bi-currency-dollar" style="font-size: 3rem; opacity: 0.3;"></i>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-white-50">Total Purchases</h6>
                    <h2><?= $stats['total_purchases'] ?? 0 ?></h2>
                </div>
                <i class="bi bi-cart-check" style="font-size: 3rem; opacity: 0.3;"></i>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-white-50">Unique Customers</h6>
                    <h2><?= $stats['unique_customers'] ?? 0 ?></h2>
                </div>
                <i class="bi bi-people" style="font-size: 3rem; opacity: 0.3;"></i>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <a href="/admin/games/new" class="card text-center text-decoration-none hover-card">
            <div class="card-body">
                <i class="bi bi-plus-circle text-primary" style="font-size: 3rem;"></i>
                <h5 class="mt-3">Add New Game</h5>
            </div>
        </a>
    </div>
    
    <div class="col-md-3 mb-3">
        <a href="/admin/games" class="card text-center text-decoration-none hover-card">
            <div class="card-body">
                <i class="bi bi-joystick text-success" style="font-size: 3rem;"></i>
                <h5 class="mt-3">Manage Games</h5>
            </div>
        </a>
    </div>
    
    <div class="col-md-3 mb-3">
        <a href="/admin/purchases" class="card text-center text-decoration-none hover-card">
            <div class="card-body">
                <i class="bi bi-cart-check text-info" style="font-size: 3rem;"></i>
                <h5 class="mt-3">View Purchases</h5>
            </div>
        </a>
    </div>
    
    <div class="col-md-3 mb-3">
        <a href="/admin/users" class="card text-center text-decoration-none hover-card">
            <div class="card-body">
                <i class="bi bi-people text-warning" style="font-size: 3rem;"></i>
                <h5 class="mt-3">Manage Users</h5>
            </div>
        </a>
    </div>
</div>

<!-- Recent Purchases -->
<div class="card">
    <div class="card-header">
        <h4><i class="bi bi-clock-history"></i> Recent Purchases</h4>
    </div>
    <div class="card-body">
        <?php if (!empty($recentPurchases)): ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Customer</th>
                            <th>Game</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentPurchases as $purchase): ?>
                            <tr>
                                <td>#<?= $purchase['id'] ?></td>
                                <td><?= htmlspecialchars($purchase['username']) ?></td>
                                <td><?= htmlspecialchars($purchase['game_title']) ?></td>
                                <td>$<?= number_format($purchase['amount'], 2) ?></td>
                                <td>
                                    <?php
                                    $statusClass = match($purchase['payment_status']) {
                                        'completed' => 'success',
                                        'pending' => 'warning',
                                        'failed' => 'danger',
                                        default => 'secondary'
                                    };
                                    ?>
                                    <span class="badge bg-<?= $statusClass ?>">
                                        <?= ucfirst($purchase['payment_status']) ?>
                                    </span>
                                </td>
                                <td><?= date('M d, Y', strtotime($purchase['purchase_date'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-muted text-center">No purchases yet.</p>
        <?php endif; ?>
    </div>
</div>

<style>
.hover-card {
    transition: all 0.3s;
}

.hover-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 16px rgba(0,0,0,0.2);
}
</style>

<?php require __DIR__ . '/../layout/footer.php'; ?>
