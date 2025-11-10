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
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create User (VULNERABLE)</title>
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
            background-color: #f8f9fa;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        /* Container Card */
        .box {
            background-color: white;
            max-width: 400px;
            width: 90%;
            padding: 40px;
            padding-top: 40px;
            border-radius: 12px;
            position: relative;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }
        
        /* --- Icon Styling (Di dalam container, tanpa bayangan, lebih kecil) --- */
        .top-icon-wrapper {
            width: 60px;
            height: 60px;
            margin: 0 auto 10px auto; /* Membuatnya di tengah dan memberi jarak ke bawah */
            background-color: transparent;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            box-shadow: none; 
        }

        .top-icon-wrapper img {
            width: 60px; /* Ukuran gambar dikecilkan */
            height: 60px;
        }


        .box h2 {
            font-weight: 600;
            color: #333;
            margin-top: 0;
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

        /* Input styling */
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

        /* Fokus menggunakan warna RENTAN (merah tua) */
        input[type="text"]:focus,
        input[type="password"]:focus {
            border-color: #A31F1F; /* Warna merah tua/coklat */
            outline: none;
        }

        /* Button Styling - Menggunakan warna RENTAN (merah tua) */
        button[type="submit"] {
            width: 100%;
            padding: 12px;
            background-color: #A31F1F; /* Warna tombol merah tua */
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.1s;
        }
        
        button[type="submit"]:active {
            background-color: #8c1a1a; /* Sedikit gelap saat diklik */
        }
        
        /* Message Styling */
        .message-success {
            color: green;
            margin-bottom: 15px;
            text-align: center;
            font-weight: 500;
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
            <img src="vuln.png" alt="Icon Rentan/Cross"> 
        </div>

        <h2>CREATE USER — VERSI RENTAN (DEMO)</h2>

        <?php if ($message): ?><p class="message-success"><?= htmlspecialchars($message) ?></p><?php endif; ?>

        <form method="post" action="">
            
            <label>Username</label>
            <input type="text" name="username" required value="<?= isset($username) ? htmlspecialchars($username) : '' ?>">

            <label>Password</label>
            <input type="text" name="password">

            <label>Full name (opsional)</label>
            <input type="text" name="full_name" value="<?= isset($fullname) ? htmlspecialchars($fullname) : '' ?>">

            <button type="submit">Buat User (vul)</button>
        </form>
        <p class="note">Catatan: contoh ini <b>rentan</b> — tidak gunakan di lingkungan publik.</p>
    </div>
</body>
</html>