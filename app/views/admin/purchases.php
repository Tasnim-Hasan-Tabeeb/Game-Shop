<?php $pageTitle = 'Manage Purchases - Admin - Video Game Shop'; ?>
<?php require __DIR__ . '/../layout/header.php'; ?>

<div class="mb-4">
    <h1 class="display-5">Manage Purchases</h1>
    <p class="lead">View all customer purchases and transactions</p>
</div>

<div class="card">
    <div class="card-body">
        <!-- Filter -->
        <div class="mb-3">
            <label for="statusFilter" class="form-label">Filter by Status:</label>
            <select class="form-select w-auto" id="statusFilter" onchange="filterPurchases()">
                <option value="">All</option>
                <option value="completed">Completed</option>
                <option value="pending">Pending</option>
                <option value="failed">Failed</option>
                <option value="refunded">Refunded</option>
            </select>
        </div>
        
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Transaction ID</th>
                        <th>Customer</th>
                        <th>Email</th>
                        <th>Game</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody id="purchasesTable">
                    <?php foreach ($purchases as $purchase): ?>
                        <tr data-status="<?= $purchase['payment_status'] ?>">
                            <td>#<?= $purchase['id'] ?></td>
                            <td><small><?= htmlspecialchars(substr($purchase['transaction_id'] ?? 'N/A', 0, 20)) ?>...</small></td>
                            <td><?= htmlspecialchars($purchase['username']) ?></td>
                            <td><?= htmlspecialchars($purchase['email']) ?></td>
                            <td><?= htmlspecialchars($purchase['game_title']) ?></td>
                            <td>$<?= number_format($purchase['amount'], 2) ?></td>
                            <td>
                                <?php
                                $statusClass = match($purchase['payment_status']) {
                                    'completed' => 'success',
                                    'pending' => 'warning',
                                    'failed' => 'danger',
                                    'refunded' => 'info',
                                    default => 'secondary'
                                };
                                ?>
                                <span class="badge bg-<?= $statusClass ?>">
                                    <?= ucfirst($purchase['payment_status']) ?>
                                </span>
                            </td>
                            <td><?= date('M d, Y H:i', strtotime($purchase['purchase_date'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <?php if (empty($purchases)): ?>
            <p class="text-muted text-center my-4">No purchases found.</p>
        <?php endif; ?>
    </div>
</div>

<script>
function filterPurchases() {
    const status = document.getElementById('statusFilter').value;
    const rows = document.querySelectorAll('#purchasesTable tr');
    
    rows.forEach(row => {
        const rowStatus = row.getAttribute('data-status');
        if (status === '' || rowStatus === status) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}
</script>

<?php require __DIR__ . '/../layout/footer.php'; ?>
