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
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        .navbar {
            background: white;
            padding: 1rem 2rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar img {
            height: 35px;
        }

        .nav-links a {
            color: #333;
            text-decoration: none;
            margin-left: 2rem;
            font-weight: 500;
        }

        .container {
            max-width: 1000px;
            margin: 2rem auto;
            padding: 0 1rem;
        }

        .header {
            text-align: center;
            margin-bottom: 3rem;
        }

        .header h1 {
            color: #C41E3A;
            margin-bottom: 1rem;
            font-size: 2.5rem;
            font-weight: 600;
        }

        .header p {
            color: #666;
            line-height: 1.6;
            max-width: 800px;
            margin: 0 auto;
        }

        .version-group {
            margin-bottom: 2.5rem;
        }

        .version-group h2 {
            color: #333;
            margin-bottom: 1.5rem;
            font-size: 1.8rem;
            font-weight: 600;
        }

        .cards {
            display: flex;
            gap: 2rem;
            justify-content: center;
        }

        .card {
            background: white;
            border: 1px solid #ddd;
            border-radius: 12px;
            padding: 1.5rem;
            width: 340px;
        }

        .icon-wrapper {
            background: #f5f5f5;
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
        }

        .version-group:first-child .icon-wrapper {
            background: #E8F5E9;
        }

        .version-group:last-child .icon-wrapper {
            background: #FFEBEE;
        }

        .icon-wrapper img {
            width: 32px;
            height: 32px;
        }

        .card h3 {
            margin-bottom: 0.8rem;
            font-weight: 500;
        }

        .card p {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 1.5rem;
            min-height: 40px;
        }

        .btn {
            display: block;
            width: 100%;
            padding: 0.8rem;
            border: none;
            border-radius: 8px;
            text-align: center;
            text-decoration: none;
            cursor: pointer;
            font-weight: 500;
            transition: opacity 0.2s;
        }

        .btn:hover {
            opacity: 0.9;
        }

        .btn-safe {
            background: #4CAF50;
            color: white;
        }

        .btn-vulnerable {
            background: #C41E3A;
            color: white;
        }

        .back-button {
            display: block;
            width: 200px;
            margin: 2rem auto;
            padding: 0.8rem;
            background: #C41E3A;
            color: white;
            text-align: center;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <img src="../logo.png" alt="Logo Keamanan Data">
        <div class="nav-links">
            <a href="index.php">Beranda</a>
            <a href="index.php">Topik</a>
        </div>
    </nav>

    <div class="container">
        <div class="header">
            <h1>SQL INJECTION</h1>
            <p>Halaman ini mendemonstrasikan bagaimana serangan SQL Injection dapat terjadi dan cara mencegahnya. Bandingkan implementasi yang rentan dengan implementasi yang aman untuk memahami perbedaannya.</p>
        </div>

        <div class="versions">
            <div class="version-group">
                <h2>Versi Aman</h2>
                <div class="cards">
                    <div class="card">
                        <div class="icon-wrapper">
                            <img src="safe.png" alt="Safe Icon">
                        </div>
                        <h3>Create User (SAFE)</h3>
                        <p>Prepared statements memisahkan data dari perintah, mencegah injeksi.</p>
                        <a href="create_user_safe.php" class="btn btn-safe">Create</a>
                    </div>
                    <div class="card">
                        <div class="icon-wrapper">
                            <img src="safe.png" alt="Safe Icon">
                        </div>
                        <h3>Login User (SAFE)</h3>
                        <p>Query terparametersisasi memastikan input adalah data, bukan kode.</p>
                        <a href="login_safe.php" class="btn btn-safe">Login</a>
                    </div>
                </div>
            </div>

            <div class="version-group">
                <h2>Versi Rentan</h2>
                <div class="cards">
                    <div class="card">
                        <div class="icon-wrapper">
                            <img src="rentan.png" alt="Vulnerable Icon">
                        </div>
                        <h3>Create User (RENTAN)</h3>
                        <p>Input pengguna langsung digabungkan ke query SQL, menciptakan celah keamanan.</p>
                        <a href="create_user_vul.php" class="btn btn-vulnerable">Create</a>
                    </div>
                    <div class="card">
                        <div class="icon-wrapper">
                            <img src="rentan.png" alt="Vulnerable Icon">
                        </div>
                        <h3>Login User (RENTAN)</h3>
                        <p>Login dapat dilewati dengan injeksi seperti: ' OR '1'='1</p>
                        <a href="login_vul.php" class="btn btn-vulnerable">Login</a>
                    </div>
                </div>
            </div>
        </div>

        <a href="#" class="back-button">Kembali ke Topik</a>
    </div>
</body>
</html>