<?php
// safe/list.php
require_once __DIR__ . '/../config.php';
if (empty($_SESSION['user'])) header('Location: ../login.php');

$user = $_SESSION['user']; // Ambil data user untuk header
$stmt = $pdo->prepare("SELECT id, uuid, title, created_at FROM items_safe WHERE user_id = :u ORDER BY created_at DESC");
$stmt->execute([':u' => $_SESSION['user']['id']]);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Definisikan fungsi CSRF token jika belum ada
if (!function_exists('csrf_token')) {
    function csrf_token() {
        return bin2hex(random_bytes(16)); // Hanya placeholder jika belum ada di config.php
    }
}
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>SAFE — Items</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root {
            --color-safe: #3BE138;      /* Hijau untuk SAFE */
            --color-bg: #f8f9fa;        /* Background cerah */
            --color-link: #0d6efd;      /* Biru standar link */
            --color-danger: #dc3545;    /* Merah standar untuk Delete */
        }
        body { 
            background: var(--color-bg); 
            min-height: 100vh; 
            font-family: 'Poppins', sans-serif;
            color: #333;
        }

        /* --- Header Styling (Menggunakan warna Merah Default) --- */
        /* Kita pertahankan warna Merah untuk branding/Logout agar konsisten dengan Dashboard utama */
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
            /* Default Red, biar konsisten dengan Dashboard */
            color: #b02a37; 
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
            background-color: #b02a37;
            border-color: #b02a37;
            color: white;
            font-weight: 500;
            margin-left: 15px;
            padding: 8px 18px;
            border-radius: 6px;
            text-decoration: none;
        }

        /* --- Content Area --- */
        .container {
            max-width: 1200px;
        }
        h2.main-title {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--color-safe); /* Judul HIJAU */
            margin-bottom: 10px;
        }
        .action-links {
            margin-bottom: 25px;
            display: flex;
            gap: 15px;
            align-items: center;
        }
        
        /* Style Link Dashboard (tetap Biru) */
        .link-dashboard {
            text-decoration: none;
            font-weight: 500;
            color: var(--color-link); 
        }
        .link-dashboard:hover {
            color: #0a58ca;
            text-decoration: underline;
        }
        
        /* --- Table Styling --- */
        .table-custom {
            background-color: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 0 10px rgba(0,0,0,0.05);
            border: 1px solid #ddd;
        }
        .table-custom thead th {
            background-color: var(--color-safe); /* Header Tabel HIJAU */
            color: white;
            font-weight: 600;
            border-color: var(--color-safe);
        }
        .table-custom tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .table-custom tbody td {
            vertical-align: middle;
            max-width: 300px;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        /* --- Button Aksi --- */
        .btn-action {
            font-size: 0.9rem;
            text-decoration: none;
            font-weight: 500;
            margin-right: 5px;
        }
        /* Style untuk Button Delete (karena ini form, kita style buttonnya) */
        .btn-action-delete {
            background: none;
            border: none;
            padding: 0;
            font: inherit;
            cursor: pointer;
            color: var(--color-danger); /* Merah untuk Delete */
            font-size: 0.9rem;
            font-weight: 500;
            margin-left: -5px; /* Sesuaikan jarak dengan link sebelumnya */
        }
        .btn-action-delete:hover {
            text-decoration: underline;
        }

        /* Link View/Edit di SAFE area */
        .btn-view, .btn-edit {
            color: var(--color-safe); /* Hijau untuk link aksi yang "aman" */
        }
        
        /* Button Create (HIJAU) */
        .btn-create {
            background-color: var(--color-safe);
            border-color: var(--color-safe);
            color: white;
            padding: 8px 18px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
        }
        .btn-create:hover {
            background-color: #2a944a; /* Hijau lebih gelap saat hover */
            color: white;
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

    <div class="container p-4">
        <h2 class="main-title">SAFE — Items (your items)</h2>
        
        <div class="action-links">
            <a href="create.php" class="btn-create">Create New Item</a>
            |
            <a href="../index.php" class="link-dashboard">Back to Dashboard</a>
        </div>

        <table class="table table-striped table-hover table-custom">
        <thead>
            <tr>
                <th style="width: 30%;">UUID</th>
                <th style="width: 35%;">Title</th>
                <th style="width: 20%;">Created</th>
                <th style="width: 15%;">Action</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach($rows as $r): ?>
        <tr>
            <td><?=htmlspecialchars($r['uuid'])?></td>
            <td><?=htmlspecialchars($r['title'])?></td>
            <td><?=htmlspecialchars($r['created_at'])?></td>
            <td>
                <a href="view.php?u=<?=urlencode($r['uuid'])?>" class="btn-action btn-view">View</a> |
                <a href="edit.php?u=<?=urlencode($r['uuid'])?>" class="btn-action btn-edit">Edit</a> |
                <form action="delete.php" method="post" style="display:inline" onsubmit="return confirm('Delete item UUID <?=htmlspecialchars($r['uuid'])?>?')">
                    <input type="hidden" name="uuid" value="<?=htmlspecialchars($r['uuid'])?>">
                    <input type="hidden" name="csrf" value="<?=csrf_token()?>">
                    <button type="submit" class="btn-action-delete">Delete</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>