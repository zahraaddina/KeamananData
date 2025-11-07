<?php
require 'config.php';
require_login();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Dashboard - Demo App</title>
    <link rel="stylesheet" href="css/dashboard.css?v=<?php echo time(); ?>">
</head>
<body>
    <div class="site-wrap">
        <div class="topbar">
            <div>
                <h1>Dashboard Utama</h1>
                <div class="breadcrumbs">Halo, <?= htmlspecialchars($_SESSION['username']) ?></div>
            </div>
            <div class="logout-container">
                <a href="logout.php" class="logout-btn">Logout</a>
            </div>
        </div>

        <div class="grid">
            <div class="card">
                <div class="label">
                    <div class="dot rent"><img src="images/warning.png" alt="Warning"></div>
                    <div>
                        <h3>Artikel Rentan</h3>
                        <p>Unggah artikel yang berisi informasi sensitif atau memerlukan penanganan khusus.</p>
                    </div>
                </div>
                <a class="btn rent" href="artikel_vul.php">Unggah Sekarang</a>
            </div>

            <div class="card">
                <div class="label">
                    <div class="dot safe"><img src="images/safe.png" alt="Safe"></div>
                    <div>
                        <h3>Artikel Aman</h3>
                        <p>Unggah artikel umum yang dapat diakses publik tanpa batasan.</p>
                    </div>
                </div>
                <a class="btn safe" href="artikel_safe.php">Unggah Sekarang</a>
            </div>
        </div>
    </div>
</body>
</html>
