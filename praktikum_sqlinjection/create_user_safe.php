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
            // Gunakan variabel koneksi yang sesuai dengan environment Anda
            $pdo = new PDO($dsn, $dbUser, $dbPass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

            // Periksa username sudah ada (Prepared statement untuk mencegah SQLI)
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
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create User (SAFE)</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* Base Styling */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }
        body {
            background-color: #f8f9fa; /* Latar belakang abu-abu muda/off-white */
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        /* Container Card */
        .box {
            background-color: white; /* Latar belakang putih bersih */
            max-width: 400px; /* Ukuran yang sesuai dengan desain form */
            width: 90%;
            padding: 40px;
            padding-top: 40px; /* Kembali ke padding normal */
            border-radius: 12px;
            position: relative;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05); /* Tambah shadow ringan */
        }
        
        /* --- Icon Styling (Diubah: Di dalam container, tanpa bayangan, lebih kecil) --- */
        .top-icon-wrapper {
            /* Dibuat in-flow dan di tengah */
            width: 60px; /* Ukuran wrapper dikecilkan */
            height: 60px; /* Ukuran wrapper dikecilkan */
            margin: 0 auto 10px auto; /* Membuatnya di tengah dan memberi jarak ke bawah */
            background-color: transparent; /* Menghilangkan latar belakang putih */
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            box-shadow: none; /* Menghilangkan bayangan */
        }

        .top-icon-wrapper img {
            width: 60px; /* Ukuran gambar dikecilkan */
            height: 60px;
        }


        .box h2 {
            font-weight: 600;
            color: #333;
            margin-top: 0; /* Tidak perlu margin-top tambahan */
            margin-bottom: 25px;
            text-align: center;
            font-size: 24px;
        }

        /* Form Elements */
        form {
            width: 100%;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
            color: #555;
            font-size: 14px;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 10px 15px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s;
        }

        input[type="text"]:focus,
        input[type="password"]:focus {
            border-color: #3BE138; /* Warna fokus */
            outline: none;
        }

        /* Button Styling */
        button[type="submit"] {
            width: 100%;
            padding: 12px;
            background-color: #3BE138; /* Warna tombol sesuai permintaan */
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.1s;
        }
        
        button[type="submit"]:active {
            background-color: #2bb928ff; /* Sedikit gelap saat diklik */
        }
        
        /* Message and Error Styling */
        .message-success {
            color: green;
            margin-bottom: 15px;
            text-align: center;
            font-weight: 500;
        }

        .err {
            background-color: #fdd;
            border: 1px solid #3BE138;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 8px;
        }

        .err ul {
            list-style: none;
            padding: 0;
            color: #AC1616;
            font-size: 14px;
        }
        
        /* Footer Note */
        .note {
            color: #999;
            font-size: 12px;
            margin-top: 25px;
            text-align: center;
        }

    </style>
</head>
<body>
    <div class="box">
        
        <div class="top-icon-wrapper">
            <img src="aman.png" alt="Icon Aman/Checklist"> 
        </div>

        <h2>CREATE USER — VERSI AMAN</h2>

        <?php if ($message): ?><p class="message-success"><?= $message ?></p><?php endif; ?>

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
            
            <label>Username (3-30; huruf/angka/_)</label>
            <input type="text" name="username" required value="<?= isset($username) ? htmlspecialchars($username) : '' ?>">

            <label>Password (minimal 8 karakter)</label>
            <input type="password" name="password" required>

            <label>Full name (opsional)</label>
            <input type="text" name="full_name" value="<?= isset($fullname) ? htmlspecialchars($fullname) : '' ?>">

            <button type="submit">Buat User (aman)</button>
        </form>

        <p class="note">Catatan: Form ini melakukan validasi dasar, CSRF token sederhana, dan menyimpan password sebagai hash.</p>
    </div>
</body>
</html>