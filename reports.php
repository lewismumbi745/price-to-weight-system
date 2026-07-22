<?php
require 'config.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Delete Transaction
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $pdo->prepare("DELETE FROM transactions WHERE id = ?");
    $stmt->execute([$id]);
    $success = "✅ Transaction deleted successfully!";
}

$search = $_GET['search'] ?? '';
$filter_date = $_GET['filter_date'] ?? '';

$query = "SELECT t.id, t.transaction_date, p.name, t.weight, t.total_price, p.unit 
          FROM transactions t 
          JOIN products p ON t.product_id = p.id WHERE 1=1";
$params = [];

if ($search) {
    $query .= " AND p.name LIKE ?";
    $params[] = "%$search%";
}
if ($filter_date) {
    $query .= " AND DATE(t.transaction_date) = ?";
    $params[] = $filter_date;
}

$query .= " ORDER BY t.transaction_date DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$sales = $stmt->fetchAll();

$total_sales = array_sum(array_column($sales, 'total_price'));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Reports</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css">
</head>
<body class="bg-light">
<div class="container mt-5">
    <!-- Top Back Button -->
    <div class="mb-3">
        <a href="dashboard.php" class="btn btn-secondary">← Back to Dashboard</a>
    </div>

    <h2>📊 Sales Reports</h2>

    <?php if(isset($success)): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>

    <div class="mb-3">
        <a href="?export=1" class="btn btn-success">📥 Export to Excel (CSV)</a>
    </div>

    <form method="GET" class="row g-3 mb-4">
        <div class="col-md-5">
            <input type="text" name="search" class="form-control" placeholder="Search product..." value="<?= htmlspecialchars($search) ?>">
        </div>
        <div class="col-md-4">
            <input type="date" name="filter_date" class="form-control" value="<?= $filter_date ?>">
        </div>
        <div class="col-md-3">
            <button type="submit" class="btn btn-primary w-100">Filter</button>
        </div>
    </form>

    <div class="alert alert-info">
        <strong>Grand Total:</strong> Ksh <?= number_format($total_sales, 2) ?> 
        (<?= count($sales) ?> transactions)
    </div>

    <table class="table table-bordered table-hover">
        <thead class="table-dark">
            <tr>
                <th>Date</th>
                <th>Product</th>
                <th>Quantity</th>
                <th>Unit</th>
                <th>Total (Ksh)</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($sales as $s): ?>
            <tr>
                <td><?= $s['transaction_date'] ?></td>
                <td><?= htmlspecialchars($s['name']) ?></td>
                <td><?= $s['weight'] ?></td>
                <td><?= $s['unit'] ?? 'kg' ?></td>
                <td>Ksh <?= number_format($s['total_price'], 2) ?></td>
                <td>
                    <a href="?delete=<?= $s['id'] ?>" class="btn btn-danger btn-sm" 
                       onclick="return confirm('Delete this transaction?')">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Bottom Back Button -->
    <div class="text-center mt-4">
        <a href="dashboard.php" class="btn btn-secondary btn-lg">← Back to Dashboard</a>
    </div>
</div>
<script>
// Auto logout after 2 minutes of inactivity
let inactivityTime = 2 * 60 * 1000; // 2 minutes
let timeout;

function resetTimer() {
    clearTimeout(timeout);
    timeout = setTimeout(() => {
        alert("Session expired due to inactivity. Logging out...");
        window.location.href = "logout.php";
    }, inactivityTime);
}

// Reset timer on any activity
document.addEventListener('mousemove', resetTimer);
document.addEventListener('keypress', resetTimer);
document.addEventListener('click', resetTimer);

// Start timer
resetTimer();
</script>
</body>
</html>