<?php
// login.php (Broken Access Control Demo)
require 'config.php';
$err = ''; // Inisialisasi $err agar tidak ada warning jika tidak ada error

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $u = trim($_POST['username']);
    $p = $_POST['password'];
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :u LIMIT 1");
    $stmt->execute([':u'=>$u]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Perhatian: Karena ini lab/demo dan menggunakan password plaintext
    // 'if ($user && $user['password'])' di bawah ini diasumsikan sebagai verifikasi password
    // Jika password di DB di-hash, gunakan password_verify($p, $user['password'])
    
    // Asumsi: $user['password'] berisi plaintext/string yang harus cocok dengan $p
    if ($user && $p === $user['password']) { // Menggunakan kecocokan string plaintext
        session_regenerate_id(true);
        $_SESSION['user'] = ['id'=>$user['id'],'username'=>$user['username'],'role'=>$user['role']];
        header('Location: index.php'); exit;
    } else $err = "Login gagal: Username atau Password salah.";
}
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Login — Broken Access Control Lab</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root {
            --color-primary: #b02a37; /* Merah sesuai desain */
        }
        body { 
            background: #f8f9fa; /* Background cerah */
            min-height: 100vh; 
            font-family: 'Poppins', sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        /* --- Main Card Styling (Two Columns) - Tanpa Bayangan --- */
        .auth-card-wrapper {
            max-width: 800px; /* Ukuran kartu yang lebih kecil */
            width: 90%;
            margin: 50px auto;
            border-radius: 10px;
            box-shadow: none; /* Menghilangkan bayangan */
            border: 1px solid #ddd; /* Border tipis */
            overflow: hidden; 
            background: white;
            display: flex;
            flex-wrap: wrap; /* Untuk responsif */
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
        .auth-card-right h2 {
            font-size: 2rem;
            font-weight: 700;
        }

        /* Form specific styles */
        .form-control {
            border-radius: 6px;
            padding: 12px;
            border: 1px solid #ddd;
        }
        .form-control:focus {
            border-color: var(--color-primary);
            box-shadow: 0 0 0 0.25rem rgba(255, 255, 255, 0.25);
        }
        .btn-submit {
            background-color: var(--color-primary);
            border-color: var(--color-primary);
            font-weight: 600;
            color: white;
            padding: 10px 0;
            border-radius: 6px;
        }

        

        .btn-submit:hover {
            background-color: #9a2430;
            border-color: #9a2430;
        }
        .alert {
            border-radius: 6px;
            text-align: left;
        }
    </style>
</head>
<body>
    <div class="auth-card-wrapper">
        
        <div class="auth-card-left">
            <h2 class="auth-title">Welcome back!</h2>
            <p class="auth-subtitle">Sign in to continue to your dashboard</p>

            <?php if($err): ?>
                <div class="alert alert-danger" role="alert">
                    <?= htmlspecialchars($err); ?>
                </div>
            <?php endif; ?>

            <form method="post">
                <div class="mb-3">
                    <input name="username" class="form-control" placeholder="Name" required>
                </div>

                <div class="mb-4">
                    <input name="password" type="password" class="form-control" placeholder="Password" required>
                </div>

                <div class="d-grid mb-3">
                    <button class="btn btn-submit" type="submit">Sign In</button>
                </div>
                
                </form>
        </div>

        <div class="auth-card-right">
            <h2 class="mb-3">Glad to see You!</h2>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>