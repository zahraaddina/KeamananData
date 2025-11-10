<?php
// ... Kode PHP di atas (tidak diubah) ...
require 'config.php';
require_login();

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $user_id = $_SESSION['user_id'];

    $file_path = null;
    if (!empty($_FILES['file']['name'])) {
        $upload_dir = 'uploads/';
        $file_name = $_FILES['file']['name'];
        $tmp_file = $_FILES['file']['tmp_name'];
        $file_size = $_FILES['file']['size'];

        // Validasi ekstensi
        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif', 'pdf'];
        $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed_ext)) {
            $error = "Ekstensi file tidak diizinkan! Hanya " . implode(', ', $allowed_ext) . " yang diperbolehkan.";
            goto end_upload; // Langsung lompat ke akhir proses upload
        }

        // Validasi ukuran (max 2MB)
        if ($file_size > 2 * 1024 * 1024) {
            $error = "File terlalu besar! Maksimal 2MB.";
            goto end_upload;
        }

        // ✅ Validasi MIME type
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $tmp_file);
        finfo_close($finfo);

        $allowed_mimes = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf'];
        if (!in_array($mime, $allowed_mimes)) {
            $error = "Tipe file tidak valid!";
            goto end_upload;
        }

        // ✅ Nama file acak untuk mencegah eksekusi file berbahaya
        $new_name = uniqid('upload_') . '.' . $ext;
        $target = $upload_dir . $new_name;

        if (move_uploaded_file($tmp_file, $target)) {
            $file_path = $target;
        } else {
            $error = "Gagal menyimpan file (Periksa izin folder 'uploads/').";
        }
    }

    end_upload: // Label untuk goto

    if (empty($error)) {
        // ✅ Prepared statement untuk mencegah SQL Injection
        $stmt = $pdo->prepare("INSERT INTO articles (user_id, title, content, file_path) VALUES (?, ?, ?, ?)");
        if ($stmt->execute([$user_id, $title, $content, $file_path])) {
            $message = "Artikel berhasil disimpan dengan aman!";
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
    <title>Artikel - Versi AMAN</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root {
            --color-safe: #3BE138; /* Hijau Aman */
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
            border-color: var(--color-safe);
            outline: 0;
            box-shadow: 0 0 0 0.15rem rgba(59, 225, 56, 0.2); 
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
        
        /* SOLUSI: Pastikan input file asli tersembunyi */
        #file_upload {
            /* Input file asli (yang memunculkan tombol dobel) */
            display: none; 
        }

        .file-input-wrapper {
            /* Kontainer untuk file input dan teks */
            background-color: #e9ecef;
            color: var(--color-text-dark);
            border: 1px solid #ced4da;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.95rem;
            font-weight: 500;
            transition: background-color 0.2s;
            display: flex;
            align-items: center;
            overflow: hidden;
        }
        .file-input-wrapper label {
            /* Tombol Choose File kustom */
            margin: 0;
            background-color: #cdd4da;
            padding: 8px 12px;
            border-right: 1px solid #ced4da;
            font-weight: 600;
            /* Pastikan label juga memiliki kursor pointer */
            cursor: pointer; 
        }
        .file-input-wrapper span {
            /* Nama file */
            padding: 8px 12px;
            font-weight: 400;
            color: var(--color-text-muted);
        }

        /* --- Submit Button (Hijau Aman) --- */
        .submit-btn {
            background-color: var(--color-safe); 
            border: 1px solid var(--color-safe);
            color: white;
            font-weight: 600;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 1rem;
            transition: background-color 0.2s;
        }
        .submit-btn:hover {
            background-color: #2da12c;
            border-color: #2da12c;
        }

        /* --- Message & Warning (Tidak diubah) --- */
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
            color: #AC1616;
            border: 1px solid #f9cccc;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-weight: 500;
        }
        .info-safe {
            margin-top: 14px;
            color: #00875A;
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
            <h2>Tulis Artikel (Versi Aman)</h2>
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
                    <div class="file-input-wrapper">
                        <label for="file_upload">Choose File</label>
                        <input id="file_upload" type="file" name="file" onchange="updateFileName(this)">
                        <span id="fileName">No file chosen</span>
                    </div>
                    <button class="submit-btn" type="submit">Simpan Artikel</button>
                </div>
            </form>
        </div>

        <div class="info-safe">
            <i class="bi bi-check-circle-fill"></i>
            Versi ini memblokir file berbahaya dan hanya mengizinkan gambar/PDF (dengan validasi ekstensi, MIME, dan ukuran).
        </div>
    </div>
    
    <script>
        // Script sederhana untuk menampilkan nama file yang dipilih
        function updateFileName(input) {
            const fileNameSpan = document.getElementById('fileName');
            if (input.files.length > 0) {
                fileNameSpan.textContent = input.files[0].name;
                fileNameSpan.style.color = 'var(--color-text-dark)';
            } else {
                fileNameSpan.textContent = 'No file chosen';
                fileNameSpan.style.color = 'var(--color-text-muted)';
            }
        }
    </script>
</body>
</html>