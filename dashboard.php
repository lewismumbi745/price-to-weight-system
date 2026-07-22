<?php
require 'config.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-success">
        <div class="container">
            <a class="navbar-brand fw-bold" href="dashboard.php">💰 Nyamakima Price System</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="products.php">Products</a>
                <a class="nav-link" href="sales.php">New Sale</a>
                <a class="nav-link" href="reports.php">Reports</a>
                <?php if($_SESSION['role'] === 'manager'): ?>
                <a class="nav-link" href="users.php">Users</a>
                <?php endif; ?>
                <a class="nav-link text-warning fw-bold" href="logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="card shadow text-center">
            <div class="card-body py-5">
                <h2>Welcome, <?= htmlspecialchars($_SESSION['full_name']) ?> 👋</h2>
                <p class="lead">Logged in as <strong><?= ucfirst($_SESSION['role']) ?></strong></p>
                
                <div class="mt-3 mb-4">
                    <strong>Current Date & Time:</strong> 
                    <span id="datetime" class="text-success fw-bold"></span>
                </div>

                <div class="row g-4 mt-4">
                    <div class="col-md-3"><a href="sales.php" class="btn btn-success btn-lg w-100">🛒 New Sale</a></div>
                    <div class="col-md-3"><a href="products.php" class="btn btn-primary btn-lg w-100">📦 Products</a></div>
                    <div class="col-md-3"><a href="reports.php" class="btn btn-info btn-lg w-100">📊 Reports</a></div>
                    <?php if($_SESSION['role'] === 'manager'): ?>
                    <div class="col-md-3"><a href="users.php" class="btn btn-warning btn-lg w-100">👥 Manage Users</a></div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script>
        function updateDateTime() {
            const now = new Date();
            const options = { 
                weekday: 'long', 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            };
            document.getElementById('datetime').textContent = now.toLocaleString('en-US', options);
        }
        setInterval(updateDateTime, 1000);
        updateDateTime();
    </script>
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