<?php
require 'config.php';

$error = '';

if ($_POST) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['full_name'] = $user['full_name'];

        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Invalid username or password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Nyamakima System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css">
    <style>
        body { 
            background: linear-gradient(135deg, #28a745, #20c997); 
            height: 100vh; 
            display: flex; 
            align-items: center; 
        }
        .login-card {
            max-width: 450px;
            border-radius: 20px;
            overflow: hidden;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-card card mx-auto shadow">
            <div class="card-header text-center bg-success text-white py-4">
                <h4>🛒 Nyamakima Market</h4>
                <h5>Price-to-Weight System</h5>
            </div>
            <div class="card-body p-5">
                <?php if($error): ?>
                    <div class="alert alert-danger text-center"><?= $error ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="mb-3">
                        <input type="text" name="username" class="form-control form-control-lg" placeholder="Username" required>
                    </div>
                    <div class="mb-4">
                        <input type="password" name="password" class="form-control form-control-lg" placeholder="Password" required>
                    </div>
                    <button type="submit" class="btn btn-success btn-lg w-100">LOGIN</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>