<?php
// login_safe.php (VERSI AMAN)
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

        // --- prepared statement (AMAN) ---
        $stmt = $pdo->prepare("SELECT id, username, password_hash, full_name FROM users_safe WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password_hash'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['demo_mode'] = 'safe';
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
  <title>Login (Aman)</title>
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
    <h3>Login — VERSI AMAN</h3>
    <?php if ($message) echo "<p style='color:red;'>".htmlspecialchars($message)."</p>"; ?>
    <form method="post" action="">
        <label>Username</label><br>
        <input name="username" required><br>
        <label>Password</label><br>
        <input name="password" type="password" required><br><br>
        <button>Login</button>
    </form>
  </div>
</body>
</html>
