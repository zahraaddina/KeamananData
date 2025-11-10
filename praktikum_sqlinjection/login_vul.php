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
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — VERSI RENTAN (Demo)</title>
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
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05); /* Shadow ringan tetap dipertahankan untuk efek card */
        }
        
        /* --- Icon Styling --- */
        .top-icon-wrapper {
            width: 60px;
            height: 60px;
            margin: 0 auto 10px auto; 
            background-color: transparent;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            box-shadow: none; 
        }

        .top-icon-wrapper img {
            width: 60px;
            height: 60px;
        }


        .box h3 {
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

        input {
            width: 100%;
            padding: 10px 15px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s;
        }

        /* Fokus menggunakan warna RENTAN (merah tua) */
        input:focus {
            border-color: #A31F1F; 
            outline: none;
        }

        /* Button Styling - Menggunakan warna RENTAN (merah tua) */
        button {
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
        
        button:active {
            background-color: #8c1a1a; /* Sedikit gelap saat diklik */
        }
        
        /* Message Styling */
        .message-error {
            color: #AC1616; /* Warna merah untuk pesan error */
            background-color: #fdd;
            border: 1px solid #f00;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 8px;
            text-align: center;
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
            <img src="vuln.png" alt="Icon Rentan/Cross"> 
        </div>

        <h3>LOGIN — VERSI RENTAN (Demo)</h3>
        
        <?php if ($message) echo "<p class='message-error'>".htmlspecialchars($message)."</p>"; ?>
        
        <form method="post" action="">
  <label>Username</label>
  <input name="username" type="text">   <!-- removed required -->

  <label>Password</label>
  <input name="password" type="password"> <!-- removed required -->

  <button type="submit">Login</button>
</form>

        
        <p class="note">Catatan: contoh ini sengaja rentan (concatenation, password plaintext). Jalankan hanya di lingkungan lokal yang terisolasi.</p>
    </div>
</body>
</html>