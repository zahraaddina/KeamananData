<?php
// dashboard.php
session_start();
if (!isset($_SESSION['user_id'])) {
    // arahkan ke halaman login sesuai kebutuhan manual saat demo
    header('Location: login_safe.php');
    exit;
}

// Menentukan warna berdasarkan mode demo
$mode = $_SESSION['demo_mode'] ?? 'unknown';
$modeColor = '#555'; // Default gray
if ($mode === 'safe') {
    $modeColor = '#3BE138'; // Hijau untuk Aman
} elseif ($mode === 'vul') {
    $modeColor = '#A31F1F'; // Merah Tua untuk Rentan
}
?>

<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
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
        .dashboard-container {
            background-color: white;
            max-width: 500px;
            width: 90%;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            text-align: center;
        }
        .dashboard-container h2 {
            font-weight: 700;
            color: #333;
            margin-bottom: 25px;
            font-size: 28px;
            border-bottom: 2px solid #eee;
            padding-bottom: 15px;
        }
        .welcome-message {
            font-size: 18px;
            margin-bottom: 15px;
            color: #555;
        }
        .mode-info {
            font-size: 16px;
            font-weight: 500;
            margin-bottom: 30px;
            padding: 10px;
            border-radius: 6px;
            background-color: #f1f1f1;
        }
        .mode-info span {
            font-weight: 700;
            /* Warna diatur secara inline di HTML menggunakan PHP */
        }
        .logout-link {
            display: inline-block;
            padding: 10px 20px;
            background-color: #AC1616; /* Merah konsisten dengan Logout Button */
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            transition: background-color 0.1s;
        }
        .logout-link:hover {
            background-color: #901414;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <h2>Dashboard</h2>
        
        <p class="welcome-message">
            Selamat datang, <?=htmlspecialchars($_SESSION['full_name'] ?? $_SESSION['username'])?>!
        </p>
        
        <p class="mode-info">
            Anda sedang berada dalam mode demo: 
            <span style="color: <?= $modeColor ?>;"><?= strtoupper(htmlspecialchars($mode)) ?></span>
        </p>

        <p>
            <a href="logout.php" class="logout-link">Logout</a>
        </p>
    </div>
</body>
</html>