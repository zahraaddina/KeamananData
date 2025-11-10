<?php
// index.php (Dashboard Broken Access Control Lab)
require 'config.php';
if (empty($_SESSION['user'])) header('Location: login.php');
$user = $_SESSION['user'];
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Dashboard — <?=htmlspecialchars($user['username'])?></title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root {
            --color-primary: #AC1616; /* Merah untuk branding/rentan */
            --color-safe: #34c759;    /* Hijau untuk aman */
            --color-bg: #f8f9fa;      /* Background cerah */
        }
        body { 
            background: var(--color-bg); 
            min-height: 100vh; 
            font-family: 'Poppins', sans-serif;
            color: #333;
        }

        /* --- Header Styling (Sama seperti Dashboard XSS) --- */
        .app-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 30px;
            background: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            margin-bottom: 30px;
        }
        .app-brand {
            display: flex;
            align-items: center;
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--color-primary);
        }
        .app-brand img {
            width: 32px;
            height: 32px;
            margin-right: 10px;
        }
        .user-info {
            display: flex;
            align-items: center;
        }
        .btn-logout {
            background-color: var(--color-primary);
            border-color: var(--color-primary);
            color: white;
            font-weight: 500;
            margin-left: 15px;
            padding: 8px 18px;
            border-radius: 6px;
            text-decoration: none;
        }
        .btn-logout:hover {
            background-color: #8c212a;
            border-color: #8c212a;
            color: white;
        }

        /* --- Content Area --- */
        .container {
            max-width: 1200px;
        }
        h1.main-title {
            font-size: 1.8rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 10px;
        }
        .description-text {
            font-size: 1rem;
            color: #555;
            margin-bottom: 30px;
            line-height: 1.6;
        }

        /* --- Card Styling (Mirip SAFE/RENTAN) --- */
        .access-card {
            border-radius: 8px;
            border: 1px solid #ddd;
            padding: 20px;
            display: flex;
            flex-direction: column;
            gap: 15px;
            background: white;
        }
        .access-card.vulnerable {
            border-left: 5px solid var(--color-primary);
            border: 1px solid #bbbbbb
        }
        .access-card.safe {
            border-left: 5px solid var(--color-safe);
            border: 1px solid #bbbbbb
        }
        .card-header-content {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .card-header-content img {
            width: 36px;
            height: 36px;
        }
        .card-header-content h3 {
            font-size: 1.25rem;
            font-weight: 600;
            margin: 0;
        }
        .card-description {
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 10px;
            line-height: 1.5;
        }

        .icon-wrapper {
    width: 50px; 
    height: 50px;
    border-radius: 8px; /* Memberi sedikit lengkungan */
    display: flex;
    align-items: center;
    justify-content: center; /* Memastikan gambar berada di tengah */
}
.access-card.vulnerable .icon-wrapper {
    background-color: var(--color-primary); /* Merah */
}
.access-card.safe .icon-wrapper {
    background-color: var(--color-safe); /* Hijau */
}

        /* Button Styling untuk Card */
        .btn-card {
            font-weight: 600;
            padding: 10px 0;
            border-radius: 6px;
            text-decoration: none;
            text-align: center;
        }
        .btn-vulnerable {
            background-color: var(--color-primary);
            color: white;
        }
        .btn-vulnerable:hover {
            background-color: #8c212a;
            color: white;
        }
        .btn-safe {
            background-color: var(--color-safe);
            color: white;
        }
        .btn-safe:hover {
            background-color: #2a944a;
            color: white;
        }
    </style>
</head>
<body>
    <header class="app-header">
        <div class="app-brand">
            <img src="images/dashboard.png" alt="Dashboard Icon">
            Dashboard
        </div>
        <div class="user-info">
            Hallo! <span style="font-weight: 600; margin-right: 15px;"><?=htmlspecialchars($user['username'])?></span>
            <a href="logout.php" class="btn-logout">Logout</a>
        </div>
    </header>

    <div class="container p-4">
        <h1 class="main-title">Praktik Broken Access Control (BAC)</h1>
        
        <p class="description-text">
            Selamat datang di lab simulasi! Halaman ini menyediakan skenario untuk memahami dan menguji kerentanan Broken Access Control (BAC), di mana pengguna dapat mengakses sumber daya atau fungsi di luar otorisasi mereka, seperti mengakses data pengguna lain (IDOR).
        </p>

        <div class="row g-4">
            
           <div class="col-md-6">
            <div class="access-card vulnerable">
              <div class="card-header-content">
                        <div class="icon-wrapper">
                          <img src="images/rentan.png" alt="Icon Rentan">
                        </div>
                        <h3>VULNERABLE AREA</h3>
                      </div>
                      <p class="card-description">
                        Contoh Broken Access Control (IDOR) — tanpa validasi ownership. Pengguna dapat mencoba mengakses data pengguna lain hanya dengan mengubah parameter ID.
                      </p>
                      <a href="vuln/list.php" class="btn-card btn-vulnerable">Masuk ke area VULN</a>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="access-card safe">
                      <div class="card-header-content">
                        <div class="icon-wrapper">
                          <img src="images/safe.png" alt="Icon Aman">
                        </div>
                        <h3>SAFE AREA</h3>
                      </div>
                      <p class="card-description">
                        Versi aman dengan UUID atau Token yang kuat, dilengkapi dengan Ownership Check di sisi server untuk memastikan pengguna hanya dapat mengakses data mereka sendiri.
                      </p>
                      <a href="safe/list.php" class="btn-card btn-safe">Masuk ke area SAFE</a>
                    </div>
                  </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>