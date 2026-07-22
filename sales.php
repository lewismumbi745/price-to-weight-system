<?php
require 'config.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$products = $pdo->query("SELECT * FROM products ORDER BY name ASC")->fetchAll();
$receipt = null;

if (isset($_POST['calculate'])) {
    $product_id = intval($_POST['product_id']);
    $quantity = floatval($_POST['quantity']);
    $payment_method = $_POST['payment_method'];

    if ($product_id && $quantity > 0) {
        $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$product_id]);
        $product = $stmt->fetch();

        if ($product && $product['stock'] >= $quantity) {
            $total = $quantity * $product['base_price'];
            $unit = $product['unit'] ?? 'kg';

            // Save transaction with payment method
            $stmt = $pdo->prepare("INSERT INTO transactions (product_id, weight, total_price, user_id, payment_method) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$product_id, $quantity, $total, $_SESSION['user_id'], $payment_method]);

            // Reduce stock
            $stmt = $pdo->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
            $stmt->execute([$quantity, $product_id]);

            $receipt = [
                'date' => date('Y-m-d H:i:s'),
                'product' => $product['name'],
                'quantity' => $quantity,
                'unit' => $unit,
                'price_per_unit' => $product['base_price'],
                'total' => $total,
                'payment_method' => $payment_method,
                'clerk' => $_SESSION['full_name']
            ];
        } else {
            $error = "Not enough stock available!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Sale</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css">
    <style>
        @media print {
            .no-print { display: none !important; }
        }
    </style>
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="mb-3 no-print">
        <a href="dashboard.php" class="btn btn-secondary">← Back to Dashboard</a>
    </div>

    <div class="card shadow no-print">
        <div class="card-header bg-success text-white">
            <h4>🛒 New Sale - Price Calculator</h4>
        </div>
        <div class="card-body">
            <?php if(isset($error)): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <label class="form-label fw-bold">Select Product</label>
                    <select name="product_id" class="form-select form-select-lg" required>
                        <option value="">-- Choose Product --</option>
                        <?php foreach($products as $p): ?>
                            <option value="<?= $p['id'] ?>">
                                <?= htmlspecialchars($p['name']) ?> — Ksh <?= number_format($p['base_price'], 2) ?>/<?= $p['unit'] ?? 'kg' ?> (Stock: <?= number_format($p['stock'], 3) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Quantity (kg or Litres)</label>
                    <input type="number" step="0.001" name="quantity" class="form-control form-control-lg" placeholder="e.g. 5 or 10" required>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">Payment Method</label>
                    <select name="payment_method" class="form-select form-select-lg" required>
                        <option value="Cash">Cash</option>
                        <option value="M-Pesa">M-Pesa</option>
                        <option value="Card">Card</option>
                        <option value="Credit">Credit</option>
                    </select>
                </div>

                <button type="submit" name="calculate" class="btn btn-success btn-lg w-100">Calculate & Save Sale</button>
            </form>
        </div>
    </div>

    <?php if($receipt): ?>
    <div class="card mt-4 shadow" id="receipt">
        <div class="card-body" style="padding: 30px;">
            <div class="text-center mb-4">
                <h3>🛒 Nyamakima Market</h3>
                <p class="mb-1">Nyamakima Wholesale Market, Nairobi</p>
                <p class="mb-1">Phone: 0712 345 678</p>
                <hr>
            </div>

            <p><strong>Date:</strong> <?= $receipt['date'] ?></p>
            <p><strong>Product:</strong> <?= htmlspecialchars($receipt['product']) ?></p>
            <p><strong>Quantity:</strong> <?= $receipt['quantity'] ?> <?= $receipt['unit'] ?></p>
            <p><strong>Price per <?= $receipt['unit'] ?>:</strong> Ksh <?= number_format($receipt['price_per_unit'], 2) ?></p>
            <p><strong>Payment Method:</strong> <?= $receipt['payment_method'] ?></p>
            <hr>
            <h4 class="text-success text-center">Total: Ksh <?= number_format($receipt['total'], 2) ?></h4>
            <p class="text-center mt-3"><strong>Clerk:</strong> <?= htmlspecialchars($receipt['clerk']) ?></p>
            
            <div class="text-center mt-5">
                <small>Thank you for your business! Come again.</small>
            </div>
        </div>
        <div class="card-footer text-center no-print">
            <button onclick="window.print()" class="btn btn-primary">🖨️ Print Receipt</button>
            <a href="sales.php" class="btn btn-secondary">New Sale</a>
        </div>
    </div>
    <?php endif; ?>

    <div class="text-center mt-4 no-print">
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