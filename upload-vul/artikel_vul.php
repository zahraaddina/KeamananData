<?php
require 'config.php';
require_login();

$message = '';
$error = ''; // Tambahkan variabel error untuk pesan kegagalan upload

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $user_id = $_SESSION['user_id'];

    $file_path = null;
    if (!empty($_FILES['file']['name'])) {
        $upload_dir = 'uploads/';
        $file_name = $_FILES['file']['name'];
        $tmp_file = $_FILES['file']['tmp_name'];
        
        // ❌ VULNERABLE: Tidak ada validasi atau sanitasi nama file/ekstensi.
        // File apapun, termasuk .php, dapat diunggah.
        $target = $upload_dir . basename($file_name); 

        if (move_uploaded_file($tmp_file, $target)) {
            $file_path = $target;
        } else {
            // Ini akan muncul jika move_uploaded_file gagal (misalnya izin folder)
            $error = "Gagal mengunggah file. Pastikan folder 'uploads/' memiliki izin tulis.";
        }
    }

    if (empty($error)) {
        // Meskipun kueri menggunakan prepared statements (aman dari SQLi),
        // fokus kerentanan di sini adalah pada fitur Upload.
        $stmt = $pdo->prepare("INSERT INTO articles (user_id, title, content, file_path) VALUES (?, ?, ?, ?)");
        if ($stmt->execute([$user_id, $title, $content, $file_path])) {
            $message = "Artikel berhasil disimpan!";
        } else {
             $error = "Gagal menyimpan artikel ke database.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Artikel - Versi RENTAN</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root {
            --color-vuln: #AC1616; /* Merah Rentan */
            --color-text-dark: #333;
            --color-text-muted: #6c757d;
            --color-background: #f8f9fa;
        }

        body {
            background: var(--color-background);
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
            padding: 40px 20px;
        }

        .wrap {
            max-width: 800px;
            margin: 0 auto;
        }

        /* --- Header Section --- */
        .top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }
        .top h2 {
            font-weight: 600;
            color: var(--color-text-dark);
            margin: 0;
            font-size: 1.8rem;
        }
        .btn-back {
            display: inline-flex;
            align-items: center;
            padding: 8px 15px;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            text-decoration: none;
            color: var(--color-text-dark);
            background-color: #f7f7f7;
            font-weight: 500;
            transition: background-color 0.2s;
        }
        .btn-back:hover {
            background-color: #e9ecef;
        }

        /* --- Card Styling --- */
        .card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            border: 1px solid #ddd;
            padding: 25px;
        }

        /* --- Form Fields --- */
        .field {
            margin-bottom: 20px;
        }
        label {
            display: block;
            font-weight: 600;
            margin-bottom: 6px;
            color: var(--color-text-dark);
        }
        input[type="text"],
        textarea {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #ced4da;
            border-radius: 6px;
            font-size: 1rem;
            box-sizing: border-box;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        input[type="text"]:focus,
        textarea:focus {
            border-color: var(--color-vuln);
            outline: 0;
            box-shadow: 0 0 0 0.15rem rgba(172, 22, 22, 0.2); /* Shadow merah */
        }
        textarea {
            min-height: 150px;
            resize: vertical;
        }

        /* --- File Input (Custom Look) --- */
        .form-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 25px;
        }
        .file-input input[type="file"] {
            display: none; /* Sembunyikan input asli */
        }
        .file-input {
            /* Styling label agar terlihat seperti input file kustom */
            display: inline-block;
            padding: 8px 12px;
            background-color: #e9ecef;
            color: var(--color-text-dark);
            border: 1px solid #ced4da;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.95rem;
            font-weight: 500;
            transition: background-color 0.2s;
        }
        .file-input:hover {
            background-color: #dee2e6;
        }
        .file-input:after {
            content: "Choose File"; /* Teks default */
            margin-right: 10px;
        }

        /* --- Submit Button (Merah Rentan) --- */
        .submit-btn {
            background-color: var(--color-vuln); 
            border: 1px solid var(--color-vuln);
            color: white;
            font-weight: 600;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 1rem;
            transition: background-color 0.2s;
        }
        .submit-btn:hover {
            background-color: #8c0c0c;
            border-color: #8c0c0c;
        }

        /* --- Message & Warning --- */
        .msg-success {
            background-color: #f0fff0;
            color: #00875A;
            border: 1px solid #d0f0d0;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-weight: 500;
        }
        .msg-error {
            background-color: #fdf5f5;
            color: var(--color-vuln);
            border: 1px solid #f9cccc;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-weight: 500;
        }
        .warning-vuln {
            margin-top: 14px;
            color: var(--color-vuln);
            font-weight: 600;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }
    </style>
</head>
<body>
    <div class="wrap">
        <div class="top">
            <h2>Tulis Artikel (Versi Rentan)</h2>
            <a class="btn-back" href="dashboard.php"><i class="bi bi-arrow-left me-1"></i> Kembali ke Dashboard</a>
        </div>

        <?php if ($message): ?>
            <div class="msg-success"><i class="bi bi-check-circle-fill me-1"></i> <?= htmlspecialchars($message) ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="msg-error"><i class="bi bi-x-octagon-fill me-1"></i> <?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <div class="card">
            <form method="post" enctype="multipart/form-data">
                <div class="field">
                    <label for="title">Judul Artikel</label>
                    <input id="title" type="text" name="title" placeholder="Masukkan judul artikel" required>
                </div>

                <div class="field">
                    <label for="content">Isi Artikel</label>
                    <textarea id="content" name="content" placeholder="Masukkan isi artikel" required></textarea>
                </div>

                <div class="form-actions">
                    <label class="file-input" id="fileLabel">
                        <input type="file" name="file" onchange="updateFileName(this)">
                        <span id="fileName">No file chosen</span>
                    </label>
                    <button class="submit-btn" type="submit">Simpan Artikel</button>
                </div>
            </form>
        </div>

        <div class="warning-vuln">
            <i class="bi bi-exclamation-triangle-fill"></i>
            PERINGATAN: Versi ini memungkinkan upload file PHP berbahaya (Unrestricted File Upload)!
        </div>
    </div>
    
    <script>
        // Script sederhana untuk menampilkan nama file yang dipilih
        function updateFileName(input) {
            const fileNameSpan = document.getElementById('fileName');
            if (input.files.length > 0) {
                fileNameSpan.textContent = input.files[0].name;
            } else {
                fileNameSpan.textContent = 'No file chosen';
            }
        }
    </script>
</body>
</html>