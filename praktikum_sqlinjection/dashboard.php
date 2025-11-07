<?php
// dashboard.php
session_start();
if (!isset($_SESSION['user_id'])) {
    // arahkan ke halaman login sesuai kebutuhan manual saat demo
    header('Location: login_safe.php');
    exit;
}
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Dashboard</title></head>
<body>
  <h2>Dashboard</h2>
  <p>Selamat datang, <?=htmlspecialchars($_SESSION['full_name'] ?? $_SESSION['username'])?></p>
  <p>Mode demo: <?=htmlspecialchars($_SESSION['demo_mode'] ?? 'unknown')?></p>
  <p><a href="logout.php">Logout</a></p>
</body>
</html>
