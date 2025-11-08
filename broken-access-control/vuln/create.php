<?php
// vuln/create.php
require_once __DIR__ . '/../config.php';
if (empty($_SESSION['user'])) header('Location: ../login.php');

$user = $_SESSION['user']; // Ambil data user untuk header

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $content = $_POST['content'] ?? '';
    $uid = (int)$_SESSION['user']['id'];
    // VULNERABLE: string concatenation into SQL (demonstrate SQLi)
    $sql = "INSERT INTO items_vuln (user_id, title, content) VALUES ($uid, '{$title}', '{$content}')";
    $pdo->exec($sql);
    header('Location: list.php'); exit;
}
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Create VULN Item</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root {
            --color-primary: #b02a37; /* Merah untuk branding/rentan */
            --color-bg: #f8f9fa;      /* Background cerah */
            --color-link: #0d6efd;    /* Biru standar link */
        }
        body { 
            background: var(--color-bg); 
            min-height: 100vh; 
            font-family: 'Poppins', sans-serif;
            color: #333;
        }

        /* --- Header Styling (Sama seperti Dashboard) --- */
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

        /* --- Content Area --- */
        .container {
            max-width: 800px; /* Lebar lebih kecil untuk form */
            background: white;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
        h2.main-title {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--color-primary); /* Judul Merah */
            margin-bottom: 25px;
        }

        /* --- Form Styling (Bootstrap) --- */
        .form-control-custom {
            width: 100%;
            margin-bottom: 15px;
            padding: 12px;
            border: 1px solid #ced4da;
            border-radius: 6px;
            font-family: 'Poppins', sans-serif;
            box-sizing: border-box; /* Pastikan padding tidak menambah lebar */
        }
        textarea.form-control-custom {
            height: 150px;
            resize: vertical;
        }
        
        /* Tombol Create (MERAH) */
        .btn-create {
            background-color: var(--color-primary);
            border-color: var(--color-primary);
            color: white;
            padding: 10px 25px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            border: none;
            width: 150px; /* Lebar tombol yang lebih jelas */
            cursor: pointer;
            transition: background-color 0.2s;
        }
        .btn-create:hover {
            background-color: #8c212a;
        }

        /* Link Back */
        .link-back {
            display: inline-block;
            margin-top: 20px;
            text-decoration: none;
            font-weight: 500;
            color: var(--color-link); 
        }
        .link-back:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <header class="app-header">
        <div class="app-brand">
            <img src="../images/dashboard.png" alt="Dashboard Icon">
            Lab BAC
        </div>
        <div class="user-info">
            Hallo! <span style="font-weight: 600; margin-right: 15px;"><?=htmlspecialchars($user['username'])?></span>
            <a href="../logout.php" class="btn-logout">Logout</a>
        </div>
    </header>

    <div class="container">
        <h2 class="main-title">Create VULN Item</h2>
        
        <form method="post">
            <div class="mb-3">
                <input name="title" placeholder="Title" class="form-control-custom" required>
            </div>
            
            <div class="mb-4">
                <textarea name="content" placeholder="Content" rows="6" class="form-control-custom" required></textarea>
            </div>
            
            <button type="submit" class="btn-create">Create</button>
        </form>

        <p><a href="list.php" class="link-back">Back to List</a></p>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>