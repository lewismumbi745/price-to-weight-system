<?php
require 'config.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Delete Product
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $success = "Product deleted successfully!";
}

// Add Product
if (isset($_POST['add_product'])) {
    $name = trim($_POST['name']);
    $price = floatval($_POST['price']);
    $unit = $_POST['unit'];
    $stock = floatval($_POST['stock']);

    $stmt = $pdo->prepare("INSERT INTO products (name, base_price, unit, stock) VALUES (?, ?, ?, ?)");
    $stmt->execute([$name, $price, $unit, $stock]);
    $success = "✅ Product added successfully!";
}

// Update Stock
if (isset($_POST['update_stock'])) {
    $id = intval($_POST['id']);
    $new_stock = floatval($_POST['new_stock']);
    $stmt = $pdo->prepare("UPDATE products SET stock = ? WHERE id = ?");
    $stmt->execute([$new_stock, $id]);
    $success = "✅ Stock updated successfully!";
}

$products = $pdo->query("SELECT * FROM products ORDER BY name")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css">
</head>
<body class="bg-light">
<div class="container mt-4">
    <div class="mb-3">
        <a href="dashboard.php" class="btn btn-secondary">← Back to Dashboard</a>
    </div>

    <h2>📦 Manage Products & Stock</h2>
    
    <?php if(isset($success)): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>

    <!-- Add New Product -->
    <div class="card mb-4">
        <div class="card-header">Add New Product</div>
        <div class="card-body">
            <form method="POST" class="row g-3">
                <div class="col-md-4">
                    <input type="text" name="name" class="form-control" placeholder="Product Name" required>
                </div>
                <div class="col-md-2">
                    <input type="number" step="0.01" name="price" class="form-control" placeholder="Price per Unit" required>
                </div>
                <div class="col-md-2">
                    <select name="unit" class="form-select" required>
                        <option value="kg">Kilograms (kg)</option>
                        <option value="L">Litres (L)</option>
                        <option value="Tray">Tray</option>
                        <option value="Ml">Millitres (Ml)</option>
                        <option value="Gm">Grams (Gm)</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="number" step="0.001" name="stock" class="form-control" placeholder="Initial Stock" value="100" required>
                </div>
                <div class="col-md-2">
                    <button type="submit" name="add_product" class="btn btn-success w-100">Add Product</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Products List with Stock -->
    <table class="table table-bordered table-hover">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Product Name</th>
                <th>Price per Unit</th>
                <th>Unit</th>
                <th>Remaining Stock</th>
                <th>Update Stock</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($products as $p): ?>
            <tr>
                <td><?= $p['id'] ?></td>
                <td><?= htmlspecialchars($p['name']) ?></td>
                <td>Ksh <?= number_format($p['base_price'], 2) ?></td>
                <td><strong><?= $p['unit'] ?? 'kg' ?></strong></td>
                <td><?= number_format($p['stock'], 3) ?> <?= $p['unit'] ?? 'kg' ?></td>
                <td>
                    <form method="POST" class="d-flex">
                        <input type="hidden" name="id" value="<?= $p['id'] ?>">
                        <input type="number" step="0.001" name="new_stock" class="form-control form-control-sm" style="width:120px" value="<?= $p['stock'] ?>">
                        <button type="submit" name="update_stock" class="btn btn-sm btn-primary ms-2">Update</button>
                    </form>
                </td>
                <td>
                    <a href="?delete=<?= $p['id'] ?>" class="btn btn-danger btn-sm" 
                       onclick="return confirm('Delete this product?')">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

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