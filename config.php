<?php
$host = getenv('mysql.railway.internal');
$port = getenv('3306');
$db   = getenv('railway');
$user = getenv('root');
$pass = getenv('UmIoFcSAHDnAOJcnDXLPoLhJnCEFqbOR');

try {
    $pdo = new PDO(
        "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4",
        $user,
        $pass
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

session_start();
?>
