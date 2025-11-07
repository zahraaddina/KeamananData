<?php
// login_vul.php  (VERSI RENTAN — DEMO)
session_start();

$dsn = 'mysql:host=127.0.0.1;port=3307;dbname=praktek_sqli;charset=utf8mb4';
$dbUser = 'root';
$dbPass = '';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    try {
        $pdo = new PDO($dsn, $dbUser, $dbPass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

        // --- pola rentan: concatenation langsung dengan input user ---
        $sql = "SELECT id, username, password, full_name FROM users_vul
                WHERE username = '" . $username . "' AND password = '" . $password . "'";
        $stmt = $pdo->query($sql);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['demo_mode'] = 'vul';
            header('Location: dashboard.php');
            exit;
        } else {
            $message = 'Username atau password salah.';
        }
    } catch (PDOException $e) {
        $message = 'Terjadi kesalahan server.';
    }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Login — VERSI RENTAN (Demo)</title>
  <style>
    body { font-family: Arial, sans-serif; padding:20px; }
    .box { max-width:420px; margin:auto; border:1px solid #ddd; padding:16px; border-radius:6px; }
    input { width:100%; padding:8px; margin:6px 0; box-sizing:border-box; }
    button { width:100%; padding:10px; }
    .note { font-size:12px; color:gray; }
  </style>
</head>
<body>
  <div class="box">
    <h3>Login — VERSI RENTAN (Demo)</h3>
    <?php if ($message) echo "<p style='color:red;'>".htmlspecialchars($message)."</p>"; ?>
    <form method="post" action="">
      <label>Username</label>
      <input name="username" required>
      <label>Password</label>
      <input name="password" type="password" required>
      <br><br>
      <button>Login</button>
    </form>
    <p class="note">Catatan: contoh ini sengaja rentan (concatenation, password plaintext). Jalankan hanya di lingkungan lokal yang terisolasi.</p>
  </div>
</body>
</html>
