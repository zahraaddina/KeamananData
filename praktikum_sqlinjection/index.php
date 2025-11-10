<?php
// Start session if needed
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SQL Injection Demo</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        /* Base Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }
        
        /* Tema Warna: Merah Utama (#AC1616), Hijau Aman (#4CAF50), Latar Belakang (#f8f9fa) */

        body {
            background-color: #f8f9fa; 
        }

        /* --- Navigation Styling --- */
        .navbar {
            background: #fff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 15px 0;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .nav-logo {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .nav-logo img {
            width: 40px;
            height: 40px;
        }

        .nav-logo span {
            font-size: 22px;
            font-weight: 700; 
            color: #AC1616; /* Merah Utama */
        }

        .nav-menu {
            display: flex;
            list-style: none;
            gap: 40px;
        }

        .nav-menu a {
            text-decoration: none;
            color: #333;
            font-weight: 500;
            transition: color 0.3s;
        }

        .nav-menu a:hover,
        .nav-menu a.active {
            color: #AC1616;
        }
        
        /* --- Konten Utama (Membuat Rapi dan Sejajar Kiri) --- */
        .main-content-container {
            max-width: 1000px; 
            margin: 2rem auto;
            padding: 20px;
            text-align: left; /* Tata letak ke KIRI */
        }

        /* --- Header Section --- */
        .header {
            text-align: left;
            margin-bottom: 2rem; 
            padding: 0;
            background: none;
            color: #333;
            box-shadow: none;
        }

        .header h1 {
            color: #AC1616;
            font-size: 38px;
            font-weight: 800;
            margin-bottom: 10px;
            text-transform: uppercase;
        }
        
        .header p {
            color: #666;
            line-height: 1.8;
            max-width: 900px; 
            margin: 0 auto 1.5rem 0;
            font-size: 16px;
        }
        
        /* --- Style Konten Baru: Bagaimana SQLi Bekerja? --- */
        .sqli-work-section {
            background-color: #fff;
            padding: 25px;
            border-radius: 8px;
            margin-bottom: 3rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            border-left: 5px solid #AC1616; 
        }

        .sqli-work-section h3 {
            color: #AC1616;
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 15px;
        }

        .sqli-work-section p {
            color: #444;
            line-height: 1.6;
            margin-bottom: 15px;
        }

        .sqli-work-section ul {
            list-style: none;
            padding-left: 0;
        }

        .sqli-work-section ul li {
            position: relative;
            padding-left: 25px;
            margin-bottom: 10px;
            color: #444;
            font-size: 15px;
        }

        .sqli-work-section ul li::before {
            content: "\f058"; /* Ikon checkmark Font Awesome */
            font-family: "Font Awesome 5 Free";
            font-weight: 900;
            color: #AC1616; 
            position: absolute;
            left: 0;
            top: 0;
        }
        /* --- Versi Group Styling --- */

        .version-group {
            margin-bottom: 3rem;
            text-align: left; 
        }

        .version-group h2 {
            color: #333; 
            font-size: 28px; 
            font-weight: 700;
            margin-top: 3rem;
            margin-bottom: 25px;
            text-align: left;
            position: relative;
        }
        
        .cards-wrapper { 
            display: flex;
            gap: 30px; 
            justify-content: flex-start; 
            max-width: 700px;
            margin: 0; 
        }

        .card {
            background: #fff; 
            border-radius: 8px; /* Lebih kotak */
            padding: 25px;
            width: 330px; 
            text-align: left;
            /* Hapus box-shadow tebal, ganti dengan border tipis */
            border: 1px solid #ddd; 
            box-shadow: none; /* Hapus bayangan */
            transition: border-color 0.3s;
        }
        
        .card:hover {
            /* Hapus efek mengambang dan bayangan */
            transform: none; 
            box-shadow: none;
            border-color: #6e6e6eff; /* Ubah border saat hover agar kelihatan interaktif */
        }
        
        /* --- Icon Wrapper --- */
        .icon-wrapper {
            width: 50px;
            height: 50px;
            border-radius: 50%; 
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
            color: white;
            font-size: 24px;
        }

        .version-group:first-child .icon-wrapper {
            background: #4CAF50; 
        }
        .version-group:last-child .icon-wrapper {
            background: #AC1616; 
        }
        
        .card h3 {
            font-weight: 700;
            font-size: 1.15rem;
            margin-bottom: 5px;
        }

        .card p {
            min-height: 40px; 
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 20px;
        }
        /* --- Tombol Warna Kontras --- */

        .btn {
            display: block;
            width: 100%;
            padding: 0.8rem;
            border: none;
            border-radius: 8px;
            text-align: center;
            text-decoration: none;
            cursor: pointer;
            font-weight: 600;
            transition: background-color 0.3s, opacity 0.3s;
        }

        .btn:hover {
            opacity: 0.9;
        }

        .btn-safe {
            background: #4CAF50;
            color: white;
        }

        .btn-vulnerable {
            background: #AC1616;
            color: white;
        }

        .back-button {
            display: block;
            width: 250px;
            /* PENTING: Mengubah margin untuk rata kiri */
            margin: 3rem 0 2rem 0; 
            padding: 0.8rem;
            background: #000000ff; 
            color: white;
            text-align: center;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 500;
            transition: background 0.3s;
        }

        .back-button:hover {
            background: #1e1e1eff;
        }
        
        /* Responsif Sederhana */
        @media (max-width: 768px) {
            .cards-wrapper {
                flex-direction: column;
                align-items: center;
                max-width: 100%;
                justify-content: center;
            }
            .card {
                width: 100%;
                max-width: 340px;
            }
            .header h1 {
                font-size: 36px;
            }
            .version-group h2 {
                text-align: center;
            }
            .nav-menu {
                gap: 20px;
            }
            .back-button {
                /* Tengah saat di layar kecil */
                margin: 3rem auto 2rem auto; 
            }
        }
    </style>
</head>
<body>

    <nav class="navbar">
        <div class="container">
            <div class="nav-logo">
                <img src="../logo.png" alt="Keamanan Data Logo"> 
                <span>Keamanan Data</span>
            </div>
            <ul class="nav-menu">
                <li><a href= "http://localhost/KeamananData/Beranda/beranda.html">Beranda</a></li>
                <li><a href="http://localhost/KeamananData/Topik/topik.html" >Topik</a></li> 
            </ul>
        </div>
    </nav>

    <div class="main-content-container"> 
        <div class="header">
            <h1>SQL INJECTION</h1>
            <p><strong>SQL Injection</strong> adalah jenis serangan injeksi yang memungkinkan untuk mengeksekusi pernyataan SQL berbahaya. Pelajari bagaimana serangan ini bekerja, dampak potensialnya, dan langkah-langkah pertahanan yang tepat menggunakan parameterisasi.</p>
        </div>
        
        <div class="sqli-work-section">
            <h3> Bagaimana SQLi Bekerja?</h3>
            <p>Tujuan utama serangan ini adalah mengubah logika query yang telah ditentukan aplikasi, memungkinkan penyerang untuk:</p>
            <ul>
                <li>Melihat data yang seharusnya tidak diizinkan untuk diakses (misalnya, informasi pengguna lain, hash kata sandi, atau data rahasia).</li>
                <li>Memodifikasi data dalam database.</li>
                <li>Menghapus data, atau bahkan seluruh tabel (table) atau database.</li>
                <li>Dalam kasus yang ekstrem, bahkan mengambil kendali atas sistem operasi server (server-side).</li>
            </ul>
        </div>

        <div class="versions">
            <div class="version-group">
                <h2>Implementasi Aman (SAFE)</h2>
                <div class="cards-wrapper"> 
                    <div class="card">
                        <div class="icon-wrapper">
                            <i class="fas fa-lock"></i> 
                        </div>
                        <h3>Create User (AMAN)</h3>
                        <p>Mencegah SQL Injection. Menggunakan Prepared Statements agar input selalu dianggap DATA, bukan kode.</p>
                        <a href="create_user_safe.php" class="btn btn-safe">Coba Create</a>
                    </div>
                    <div class="card">
                        <div class="icon-wrapper">
                            <i class="fas fa-shield-alt"></i> 
                        </div>
                        <h3>Login User (AMAN)</h3>
                        <p>Autentikasi aman dari bypass. Menggunakan Prepared Statements dan Password Hashing untuk verifikasi login.</p>
                        <a href="login_safe.php" class="btn btn-safe">Coba Login</a>
                    </div>
                </div>
            </div>

            <div class="version-group">
                <h2>Implementasi Rentan (VULNERABLE)</h2>
                <div class="cards-wrapper"> 
                    <div class="card">
                        <div class="icon-wrapper">
                            <i class="fas fa-exclamation-triangle"></i> 
                        </div>
                        <h3>Create User (RENTAN)</h3>
                        <p>Rentan SQL Injection. Input pengguna digabung langsung ke query SQL, berpotensi mengubah perintah.</p>
                        <a href="create_user_vul.php" class="btn btn-vulnerable">Coba Create</a>
                    </div>
                    <div class="card">
                        <div class="icon-wrapper">
                            <i class="fas fa-skull-crossbones"></i> 
                        </div>
                        <h3>Login User (RENTAN)</h3>
                        <p>Sangat rentan bypass login. Untuk masuk tanpa password yang sah.</p>
                        <a href="login_vul.php" class="btn btn-vulnerable">Coba Login</a>
                    </div>
                </div>
            </div>
        </div>

        <a href="http://localhost/KeamananData/Topik/topik.html" class="back-button">Kembali ke Topik</a>
    </div>
</body>
</html>