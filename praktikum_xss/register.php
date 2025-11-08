<?php
// register.php
require 'auth_simple.php';
$pdo = pdo_connect();
$msg = '';
$err = '';

if($_SERVER['REQUEST_METHOD']==='POST'){
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $full_name = trim($_POST['full_name'] ?? '');

    if($username && $password){
        try {
            // lab: simpan plaintext, di produksi wajib password_hash()
            $stmt = $pdo->prepare("INSERT INTO users (username, password, full_name) VALUES (:u,:p,:n)");
            $stmt->execute([':u'=>$username, ':p'=>$password, ':n'=>$full_name]);
            $msg = "User berhasil didaftarkan. Silakan login.";
        } catch (Exception $e) {
            $err = "Registrasi gagal: kemungkinan username sudah dipakai.";
        }
    } else {
        $err = "Username & password wajib diisi.";
    }
}
?>

<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Register — Lab</title>
  
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    :root {
        --color-primary: #b02a37; /* Merah sesuai dashboard */
    }
    body { 
        background: #f8f9fa; /* Background lebih cerah */
        min-height: 100vh; 
        font-family: 'Poppins', sans-serif;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    /* --- Main Card Styling (Two Columns) --- */
    .auth-card-wrapper {
        max-width: 900px; /* Lebar maksimum kartu */
        width: 90%;
        margin: 50px auto;
        border-radius: 10px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        overflow: hidden; /* Penting untuk radius */
        background: white;
    }
    .auth-card-left {
        padding: 40px;
        flex: 1;
        min-width: 300px;
        text-align: center;
    }
    .auth-card-right {
        padding: 40px;
        flex: 1;
        min-width: 300px;
        background-color: var(--color-primary); /* Merah */
        color: white;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        text-align: center;
    }
    /* Title and Subtitle for Form Section */
    .auth-title {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 5px;
        color: #333;
    }
    .auth-subtitle {
        font-size: 1rem;
        color: #6c757d;
        margin-bottom: 25px;
    }
    /* Title and Content for Right Panel */
    .auth-card-right h2 {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 10px;
    }
    .auth-card-right p {
        font-size: 1.1rem;
        font-weight: 400;
    }

    /* Form specific styles */
    .form-control {
        border-radius: 6px;
        padding: 12px;
        border: 1px solid #ddd;
    }
    .form-control:focus {
        border-color: var(--color-primary);
        box-shadow: 0 0 0 0.25rem rgba(176, 42, 55, 0.25);
    }
    .form-label {
        font-weight: 500;
        margin-bottom: 5px;
        color: #333;
        display: block; /* Agar label tetap di atas input */
        text-align: left; /* Teks label rata kiri */
    }
    /* Button Style: Menggunakan warna Merah (Primary) */
    .btn-submit {
        background-color: var(--color-primary); 
        border-color: var(--color-primary);
        font-weight: 600;
        padding: 10px 0;
        border-radius: 6px;
    }
    .btn-submit:hover {
        background-color: #9a2430;
        border-color: #9a2430;
    }
    .link-small {
        font-size: 0.9rem;
        color: var(--color-primary);
        text-decoration: none;
    }
    .link-small:hover {
        color: #9a2430;
    }
    .alert {
        border-radius: 6px;
    }
  </style>
</head>
<body>
  <div class="auth-card-wrapper d-flex flex-wrap">
    
    <div class="auth-card-left">
      <h2 class="auth-title">Hello, friend!</h2>
      <p class="auth-subtitle">Create your account to start uploading files</p>

      <?php if($msg): ?>
        <div class="alert alert-success"><?= htmlspecialchars($msg) ?></div>
      <?php endif; ?>
      <?php if($err): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($err) ?></div>
      <?php endif; ?>

      <form method="post" novalidate style="text-align: left;">
        <div class="mb-3">
          <label for="username" class="form-label">Username</label>
          <input id="username" name="username" class="form-control" placeholder="Pilih username unik" required>
        </div>
        <div class="mb-3">
          <label for="password" class="form-label">Password</label>
          <input id="password" name="password" type="password" class="form-control" placeholder="••••••••" required>
        </div>
        <div class="mb-3">
          <label for="full_name" class="form-label">Nama Lengkap</label>
          <input id="full_name" name="full_name" class="form-control" placeholder="Nama Anda">
        </div>
        
        <div class="d-grid mb-3 mt-4">
          <button class="btn btn-submit" type="submit">Create Account</button>
        </div>
      </form>

      <div class="text-center">
        <span class="small">Already have an account? 
            <a href="login.php" class="link-small">Sign In</a>
        </span>
      </div>
    </div>

    <div class="auth-card-right">
        <h2 class="mb-3">Selamat Datang!</h2>
        <p>Ini adalah praktikum uji kerentanan website.</p>
    </div>
  </div>
  
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>