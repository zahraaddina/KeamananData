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
        /* Menggunakan warna merah sesuai permintaan */
        :root {
            --color-primary: #AC1616; /* Merah AC1616 */
        }
        body { 
            background: #f8f9fa;
            min-height: 100vh; 
            font-family: 'Poppins', sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        /* --- Main Card Styling (Dihilangkan bayangannya, hanya border/clean) --- */
        .auth-card-wrapper {
            max-width: 900px;
            width: 90%;
            margin: 50px auto;
            border-radius: 10px;
            /* HILANGKAN BAYANGAN */
            box-shadow: none; 
            border: 1px solid #ddd; /* Tambahkan border tipis sebagai ganti bayangan */
            overflow: hidden;
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
        /* Border input fokus normal merah (tanpa shadow berlebihan) */
        .form-control:focus {
            border-color: var(--color-primary);
            box-shadow: none; /* Hilangkan shadow fokus bootstrap */
            outline: none;
        }
        .form-label {
            font-weight: 500;
            margin-bottom: 5px;
            color: #333;
            display: block;
            text-align: left;
        }
        /* Button Style: Menggunakan warna Merah (Primary) dan teks putih */
        .btn-submit {
            background-color: var(--color-primary); 
            border-color: var(--color-primary);
            color: white !important; /* Pastikan teks tombol putih */
            font-weight: 600;
            padding: 10px 0;
            border-radius: 6px;
        }
        .btn-submit:hover {
            background-color: #901414; /* Sedikit lebih gelap saat hover */
            border-color: #901414;
        }
        .link-small {
            font-size: 0.9rem;
            color: var(--color-primary);
            text-decoration: none;
        }
        .link-small:hover {
            color: #901414;
        }
        .alert {
            border-radius: 6px;
        }

        @media (max-width: 768px) {
            .auth-card-right {
                display: none; /* Sembunyikan panel kanan di layar kecil */
            }
        }
    </style>
</head>
<body>
    <div class="auth-card-wrapper d-flex flex-wrap">
        
        <div class="auth-card-left">
            <h2 class="auth-title">Hello, friend!</h2>
            <p class="auth-subtitle">Bikin akun dulu kalau belum punya akun ya!</p>

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
                    <label for="full_name" class="form-label">Nama Lengkap (opsional)</label>
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
            <p>Ini adalah praktikum.</p>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>