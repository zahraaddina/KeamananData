<?php
// safe/create.php
require_once __DIR__ . '/../config.php';
if (empty($_SESSION['user'])) header('Location: ../login.php');

$user = $_SESSION['user']; // Ambil data user untuk header
$err = ''; // Inisialisasi variabel error

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Diasumsikan check_csrf, uuid4, token_generate, dan token_hash ada di config.php
    if (!function_exists('check_csrf') || !check_csrf($_POST['csrf'] ?? '')) { 
        http_response_code(400); 
        exit('CSRF fail'); 
    }
    
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    
    if ($title === '') { 
        $err = "Title required"; 
    }
    
    if (empty($err)) {
        $uuid = uuid4();
        $token = token_generate();
        $hash = token_hash($token);
        
        $stmt = $pdo->prepare("INSERT INTO items_safe (uuid, token_hash, token_expires_at, user_id, title, content)
                               VALUES (:uuid, :th, NULL, :uid, :t, :c)");
        $stmt->execute([
            ':uuid'=>$uuid, ':th'=>$hash, ':uid'=>$_SESSION['user']['id'],
            ':t'=>$title, ':c'=>$content
        ]);
        
        // Output hasil setelah sukses membuat item
        ?>
        <!doctype html>
        <html lang="id">
        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width,initial-scale=1">
            <title>Item Created</title>
            <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
            <style>
                body { font-family: 'Poppins', sans-serif; background: #f8f9fa; color: #333; padding: 50px; }
                .success-box { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 0 15px rgba(0,0,0,0.1); max-width: 600px; margin: auto; }
                h3 { color: #34c759; font-weight: 700; margin-bottom: 20px; }
                pre { background: #eee; padding: 15px; border-radius: 4px; overflow-x: auto; white-space: pre-wrap; word-wrap: break-word; }
                a { color: #0d6efd; text-decoration: none; font-weight: 500;}
            </style>
        </head>
        <body>
            <div class="success-box">
                <h3>Item created successfully!</h3>
                <p><b>UUID:</b> <?= htmlspecialchars($uuid) ?></p>
                <p><b>ACCESS TOKEN (save this now):</b><br><pre><?= htmlspecialchars($token) ?></pre></p>
                <p><a href="list.php">Back to List</a></p>
            </div>
        </body>
        </html>
        <?php
        exit;
    }
}
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Create SAFE Item</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root {
            --color-safe: #34c759;      /* Hijau untuk SAFE */
            --color-primary-default: #b02a37; /* Merah untuk branding/logout */
            --color-bg: #f8f9fa;        /* Background cerah */
            --color-link: #0d6efd;      /* Biru standar link */
            --color-error: #dc3545;     /* Merah standar untuk Error */
        }
        body { 
            background: var(--color-bg); 
            min-height: 100vh; 
            font-family: 'Poppins', sans-serif;
            color: #333;
        }

        /* --- Header Styling (Warna Merah Default untuk Branding) --- */
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
            color: var(--color-primary-default);
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
            background-color: var(--color-primary-default);
            border-color: var(--color-primary-default);
            color: white;
            font-weight: 500;
            margin-left: 15px;
            padding: 8px 18px;
            border-radius: 6px;
            text-decoration: none;
        }

        /* --- Content Area --- */
        .container {
            max-width: 800px;
            background: white;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
        h2.main-title {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--color-safe); /* Judul HIJAU */
            margin-bottom: 25px;
        }

        /* --- Form Styling --- */
        .form-control-custom {
            width: 100%;
            margin-bottom: 15px;
            padding: 12px;
            border: 1px solid #ced4da;
            border-radius: 6px;
            font-family: 'Poppins', sans-serif;
            box-sizing: border-box;
        }
        textarea.form-control-custom {
            height: 150px;
            resize: vertical;
        }
        
        /* Tombol Create (HIJAU) */
        .btn-create {
            background-color: var(--color-safe);
            border-color: var(--color-safe);
            color: white;
            padding: 10px 25px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            border: none;
            width: 150px; 
            cursor: pointer;
            transition: background-color 0.2s;
        }
        .btn-create:hover {
            background-color: #2a944a; /* Hijau lebih gelap saat hover */
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

        /* Error Message */
        .alert-error {
            color: var(--color-error);
            font-weight: 500;
            margin-bottom: 15px;
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
        <h2 class="main-title">Create SAFE Item</h2>
        
        <?php if (!empty($err)): ?>
            <p class='alert-error'><?=htmlspecialchars($err)?></p>
        <?php endif; ?>

        <form method="post">
            <div class="mb-3">
                <input name="title" placeholder="Title" class="form-control-custom" value="<?=htmlspecialchars($_POST['title'] ?? '')?>" required>
            </div>
            
            <div class="mb-4">
                <textarea name="content" placeholder="Content" rows="6" class="form-control-custom"><?=htmlspecialchars($_POST['content'] ?? '')?></textarea>
            </div>
            
            <input type="hidden" name="csrf" value="<?=csrf_token()?>">
            
            <button type="submit" class="btn-create">Create</button>
        </form>

        <p><a href="list.php" class="link-back">Back to List</a></p>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>