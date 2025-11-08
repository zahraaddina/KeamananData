<?php
// login.php
require 'auth_simple.php'; // tetap panggil jika Anda butuh helper current_user(), dll.
$err = '';

// simple CSRF token (lab/demo)
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(24));
}

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    // CSRF check
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $err = 'Invalid request (CSRF).';
    } else {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($username === '' || $password === '') {
            $err = 'Username dan password wajib diisi.';
        } else {
            $pdo = pdo_connect();
            $stmt = $pdo->prepare("SELECT id, username, password FROM users WHERE username = :u LIMIT 1");
            $stmt->execute([':u' => $username]);
            $u = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($u) {
                // Prefer secure password verification (bcrypt/argon2)
                // But keep plaintext fallback for lab compatibility:
                $ok = false;
                if (password_verify($password, $u['password'])) {
                    $ok = true;
                } elseif ($password === $u['password']) { // legacy plaintext (lab only)
                    $ok = true;
                }

                if ($ok) {
                    // login success
                    // regenerate session id to prevent fixation
                    session_regenerate_id(true);
                    $_SESSION['user_id'] = $u['id'];
                    // optional: unset CSRF token so it's single-use
                    unset($_SESSION['csrf_token']);
                    // header('Location: post_vul.php?id=1');
                    header('Location: dashboard.php');
                    exit;
                } else {
                    $err = 'Login gagal: username atau password salah.';
                }
            } else {
                $err = 'Login gagal: username atau password salah.';
            }
        }
    }
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Login — Lab</title>

  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    :root {
        --color-primary: #b02a37; /* Merah sesuai dashboard */
        --color-accent: #198754; /* Hijau untuk register */
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
    }
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
      <p class="auth-subtitle">Login to your account to continue</p>

      <?php if($err): ?>
        <div class="alert alert-danger" role="alert">
          <?= htmlspecialchars($err); ?>
        </div>
      <?php endif; ?>

      <form method="post" novalidate>
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']); ?>">

        <div class="mb-3">
          <input id="username" name="username" class="form-control" placeholder="Username" required
                  value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '' ?>">
        </div>

        <div class="mb-4">
          <input id="password" name="password" type="password" class="form-control" placeholder="Password" required>
        </div>

        <div class="d-grid mb-3">
          <button class="btn btn-submit" type="submit">Sign In</button>
        </div>
      </form>

      <div class="text-center">
        <span class="small">Don't have an account? 
            <a href="register.php" class="link-small">Sign Up</a>
        </span>
      </div>
    </div>

    <div class="auth-card-right">
        <h2 class="mb-3">Selamat Datang!</h2>
        <p>Ini adalah praktikum uji kerentanan website.</p>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // focus ke username pada load
    document.getElementById('username')?.focus();
  </script>
</body>
</html>