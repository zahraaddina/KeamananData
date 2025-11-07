<?php
require 'config.php';
require_login();

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $user_id = $_SESSION['user_id'];

    $file_path = null;
    if (!empty($_FILES['file']['name'])) {
        $upload_dir = 'uploads/';
        $file_name = $_FILES['file']['name'];
        $tmp_file = $_FILES['file']['tmp_name'];
        $target = $upload_dir . basename($file_name);

        // ❌ TIDAK ADA VALIDASI — RENTAN!
        if (move_uploaded_file($tmp_file, $target)) {
            $file_path = $target;
        }
    }

    $stmt = $pdo->prepare("INSERT INTO articles (user_id, title, content, file_path) VALUES (?, ?, ?, ?)");
    $stmt->execute([$user_id, $title, $content, $file_path]);

    $message = "Artikel berhasil disimpan!";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Artikel - Versi RENTAN</title>
    <link rel="stylesheet" href="css/artikel_vul.css">
</head>
<body>
    <div class="wrap">
        <div class="top">
            <h2>Tulis Artikel (Versi Rentan)</h2>
            <a class="btn-back" href="dashboard.php">Kembali ke Dashboard</a>
        </div>

        <?php if ($message): ?>
            <div class="msg-success"><?= htmlspecialchars($message) ?></div>
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
                    <label class="file-input">
                        <input type="file" name="file">
                    </label>
                    <button class="submit-btn" type="submit">Simpan Artikel</button>
                </div>
            </form>
        </div>

        <div style="margin-top:14px;color:#c0392b;font-weight:600">⚠️ PERINGATAN: Versi ini memungkinkan upload file PHP berbahaya!</div>
    </div>
</body>
</html>