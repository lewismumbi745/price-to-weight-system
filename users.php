<?php
require 'config.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'manager') {
    header("Location: dashboard.php");
    exit;
}

// Add New User with Hashed Password
if (isset($_POST['add_user'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $role = $_POST['role'];
    $full_name = trim($_POST['full_name']);

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("INSERT INTO users (username, password, role, full_name) VALUES (?, ?, ?, ?)");
    $stmt->execute([$username, $hashed_password, $role, $full_name]);
    $success = "✅ New user added successfully!";
}

// Reset Password with Hash
if (isset($_POST['reset_password'])) {
    $id = intval($_POST['id']);
    $new_password = $_POST['new_password'];
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
    $stmt->execute([$hashed_password, $id]);
    $success = "✅ Password reset successfully!";
}

// Edit User
if (isset($_POST['update_user'])) {
    $id = intval($_POST['id']);
    $username = trim($_POST['username']);
    $full_name = trim($_POST['full_name']);
    $role = $_POST['role'];

    $stmt = $pdo->prepare("UPDATE users SET username=?, full_name=?, role=? WHERE id=?");
    $stmt->execute([$username, $full_name, $role, $id]);
    $success = "✅ User updated successfully!";
}

$users = $pdo->query("SELECT id, username, role, full_name FROM users ORDER BY role DESC, full_name")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h2>👥 User Management (Manager Only)</h2>

    <?php if(isset($success)): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>

    <!-- Add New User -->
    <div class="card mb-4">
        <div class="card-header">Add New User</div>
        <div class="card-body">
            <form method="POST">
                <div class="row g-3">
                    <div class="col-md-3">
                        <input type="text" name="username" class="form-control" placeholder="Username" required>
                    </div>
                    <div class="col-md-3">
                        <input type="text" name="full_name" class="form-control" placeholder="Full Name" required>
                    </div>
                    <div class="col-md-3">
                        <input type="password" name="password" class="form-control" placeholder="Set Password" required>
                    </div>
                    <div class="col-md-2">
                        <select name="role" class="form-select" required>
                            <option value="clerk">Sales Clerk</option>
                            <option value="manager">Manager</option>
                        </select>
                    </div>
                    <div class="col-md-1">
                        <button type="submit" name="add_user" class="btn btn-success w-100">Add</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Users List -->
    <table class="table table-bordered table-hover">
        <thead class="table-dark">
            <tr>
                <th>Full Name</th>
                <th>Username</th>
                <th>Role</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($users as $u): ?>
            <tr>
                <td><?= htmlspecialchars($u['full_name']) ?></td>
                <td><?= htmlspecialchars($u['username']) ?></td>
                <td><?= ucfirst($u['role']) ?></td>
                <td>
                    <a href="#" onclick="resetPassword(<?= $u['id'] ?>)" class="btn btn-info btn-sm">Reset Password</a>
                    <?php if($u['id'] != $_SESSION['user_id']): ?>
                    <a href="?delete=<?= $u['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete user?')">Delete</a>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
            <div class="text-left mt-4 no-print">
    <a href="dashboard.php" class="btn btn-secondary btn-lg">← Back to Dashboard</a>
</div>
        </tbody>
    </table>

    <div class="mt-4">
        <a href="dashboard.php" class="btn btn-secondary btn-lg">← Back to Dashboard</a>
    </div>
</div>

<script>
function resetPassword(id) {
    const newPass = prompt("Enter new password:");
    if (newPass && newPass.length >= 4) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `
            <input type="hidden" name="id" value="${id}">
            <input type="hidden" name="new_password" value="${newPass}">
            <input type="hidden" name="reset_password" value="1">
        `;
        document.body.append(form);
        form.submit();
    }
}
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
