<?php
// auth_simple.php - helper koneksi & current_user
session_start();

function pdo_connect(){
    $host = '127.0.0.1';
    $port = 3307;
    $db   = 'lab_guestbook';
    $user = 'root';
    $pass = ''; // sesuaikan
    $charset = 'utf8mb4';
    $dsn = "mysql:host=$host;port=$port;dbname=$db;charset=$charset";
    $opt = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION];
    return new PDO($dsn, $user, $pass, $opt);
}

function current_user(){
    if(!empty($_SESSION['user_id'])){
        $pdo = pdo_connect();
        $stmt = $pdo->prepare("SELECT id, username, full_name FROM users WHERE id=:id");
        $stmt->execute([':id'=>$_SESSION['user_id']]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    return null;
}
