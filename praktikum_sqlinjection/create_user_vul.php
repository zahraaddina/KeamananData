<?php
// create_user_vul_form.php
// DEMO ONLY: VULNERABLE user creation form — gunakan hanya di lab lokal/VM

$dsn = 'mysql:host=127.0.0.1;port=3307;dbname=praktek_sqli;charset=utf8mb4';
$dbUser = 'root';
$dbPass = ''; // sesuaikan jika perlu

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $fullname = $_POST['full_name'] ?? '';

    try {
        $pdo = new PDO($dsn, $dbUser, $dbPass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

        // VULNERABLE: menyimpan password plaintext and concatenation query
        $sql = "INSERT INTO users_vul (username, password, full_name) VALUES ('" 
                . $username . "', '" . $password . "', '" . $fullname . "')";
        $pdo->exec($sql);

        $message = "User rentan berhasil dibuat: " . htmlspecialchars($username);

    } catch (PDOException $e) {
        $message = "Terjadi kesalahan server (demo).";
    }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Create User (VULNERABLE)</title>
  <style>body{font-family:Arial;padding:20px} .box{max-width:480px;margin:auto;border:1px solid #ddd;padding:16px;border-radius:8px}</style>
</head>
<body>
  <div class="box">
    <h2>CREATE USER — VERSI RENTAN (DEMO)</h2>
    <?php if ($message): ?><p style="color:green;"><?= htmlspecialchars($message) ?></p><?php endif; ?>
    <form method="post" action="">
      <label>Username</label><br>
      <input type="text" name="username" required><br><br>
      <label>Password</label><br>
      <input type="text" name="password" required><br><br>
      <label>Full name</label><br>
      <input type="text" name="full_name"><br><br>
      <button type="submit">Buat User (vul)</button>
    </form>
    <p style="color:gray;font-size:12px">Catatan: contoh ini <b>rentan</b> — tidak gunakan di lingkungan publik.</p>
  </div>
</body>
</html>
