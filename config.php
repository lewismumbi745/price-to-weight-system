<?php
$host = 'bb3bdxfipesqjcygy73z-mysql.services.clever-cloud.com';          
$db   = 'bb3bdxfipesqjcygy73z';
$user = 'ulbesy6iyflgmw03';
$pass = '5S3uQyVoTrCTY8C2Cfj';
$port = '21416';              

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

session_start();
?>
