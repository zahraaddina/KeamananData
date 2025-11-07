<?php
// create_user_safe_form.php
// SAFE user creation form — gunakan untuk praktikum mahasiswa / disebarkan

session_start();

$dsn = 'mysql:host=127.0.0.1;port=3307;dbname=praktek_sqli;charset=utf8mb4';
$dbUser = 'root';
$dbPass = ''; // sesuaikan jika perlu

// generate CSRF token if not exists
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(24));
}

$message = '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF check
    $token = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        $errors[] = 'Token CSRF tidak valid.';
    }

    // read and trim inputs
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $fullname = trim($_POST['full_name'] ?? '');

    // basic validation
    if ($username === '' || $password === '') {
        $errors[] = 'Username dan password wajib diisi.';
    } else {
        if (!preg_match('/^[A-Za-z0-9_]{3,30}$/', $username)) {
            $errors[] = 'Username hanya boleh huruf, angka, underscore; 3-30 karakter.';
        }
        if (strlen($password) < 8) {
            $errors[] = 'Password minimal 8 karakter.';
        }
    }

    if (empty($errors)) {
        try {
            $pdo = new PDO($dsn, $dbUser, $dbPass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

            // Periksa username sudah ada
            $stmt = $pdo->prepare("SELECT id FROM users_safe WHERE username = ?");
            $stmt->execute([$username]);
            if ($stmt->fetch()) {
                $errors[] = 'Username sudah terdaftar. Pilih username lain.';
            } else {
                // hash password
                $passwordHash = password_hash($password, PASSWORD_DEFAULT);

                // prepared statement (aman)
                $stmt = $pdo->prepare("INSERT INTO users_safe (username, password_hash, full_name) VALUES (?, ?, ?)");
                $stmt->execute([$username, $passwordHash, $fullname]);

                $message = "User aman berhasil dibuat: " . htmlspecialchars($username);

                // regenerate CSRF token after success to avoid form resubmission risk
                $_SESSION['csrf_token'] = bin2hex(random_bytes(24));
            }
        } catch (PDOException $e) {
            // log server-side dalam implementasi nyata
            $errors[] = 'Terjadi kesalahan server. Coba lagi nanti.';
        }
    }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Create User (SAFE)</title>
  <style>body{font-family:Arial;padding:20px} .box{max-width:560px;margin:auto;border:1px solid #ddd;padding:16px;border-radius:8px} .err{color:red}</style>
</head>
<body>
  <div class="box">
    <h2>CREATE USER — VERSI AMAN</h2>

    <?php if ($message): ?><p style="color:green;"><?= $message ?></p><?php endif; ?>

    <?php if (!empty($errors)): ?>
      <div class="err">
        <ul>
          <?php foreach ($errors as $e): ?>
            <li><?= htmlspecialchars($e) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <form method="post" action="">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
      <label>Username (3-30; huruf/angka/_)</label><br>
      <input type="text" name="username" required value="<?= isset($username) ? htmlspecialchars($username) : '' ?>"><br><br>

      <label>Password (minimal 8 karakter)</label><br>
      <input type="password" name="password" required><br><br>

      <label>Full name (opsional)</label><br>
      <input type="text" name="full_name" value="<?= isset($fullname) ? htmlspecialchars($fullname) : '' ?>"><br><br>

      <button type="submit">Buat User (aman)</button>
    </form>

    <p style="color:gray;font-size:12px">Catatan: Form ini melakukan validasi dasar, CSRF token sederhana, dan menyimpan password sebagai hash.</p>
  </div>
</body>
</html>
