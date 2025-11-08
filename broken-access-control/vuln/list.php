<?php
// vuln/list.php
require_once __DIR__ . '/../config.php';
if (empty($_SESSION['user'])) header('Location: ../login.php');

$user = $_SESSION['user']; // Ambil data user untuk header
$res = $pdo->query("SELECT items_vuln.*, users.username FROM items_vuln JOIN users ON items_vuln.user_id = users.id ORDER BY items_vuln.id DESC");
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>VULN — Items</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root {
            --color-primary: #b02a37; /* Merah untuk branding/rentan */
            --color-bg: #f8f9fa;      /* Background cerah */
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
            max-width: 1200px;
        }
        h2.main-title {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--color-primary);
            margin-bottom: 10px;
        }
        .action-links {
            margin-bottom: 25px;
            display: flex;
            gap: 15px;
            align-items: center;
        }
        
        /* 1. Hapus warna default untuk .action-links a (agar tidak bentrok) */
        /* .action-links a {
            text-decoration: none;
            font-weight: 500;
            color: #0d6efd; 
        } */
        
        /* 2. Tambahkan style khusus untuk link Dashboard */
        .link-dashboard {
            text-decoration: none;
            font-weight: 500;
            color: #0d6efd; /* Biru standar Bootstrap */
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
            background-color: var(--color-primary);
            color: white;
            font-weight: 600;
            border-color: var(--color-primary);
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
        .btn-edit {
            color: #0d6efd; /* Biru */
        }
        .btn-delete {
            color: var(--color-primary); /* Merah */
        }
        .btn-create {
            background-color: var(--color-primary);
            border-color: var(--color-primary);
            color: white;
            padding: 8px 18px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
        }
        .btn-create:hover {
            background-color: #8c212a;
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
        <h2 class="main-title">VULN — Items</h2>
        
        <div class="action-links">
            <a href="create.php" class="btn-create">Create New Item</a>
            |
            <a href="../index.php" class="link-dashboard">Back to Dashboard</a>
        </div>

        <table class="table table-striped table-hover table-custom">
        <thead>
            <tr>
                <th style="width: 5%;">ID</th>
                <th style="width: 25%;">Title</th>
                <th style="width: 40%;">Content</th>
                <th style="width: 15%;">Author</th>
                <th style="width: 15%;">Action</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach($res as $r): ?>
        <tr>
            <td><?= $r['id'] ?></td>
            <td><?= htmlspecialchars($r['title']) ?></td>
            <td><?= $r['content'] ?></td>
            <td><?= htmlspecialchars($r['username']) ?></td>
            <td>
                <a href="edit.php?id=<?= $r['id'] ?>" class="btn-action btn-edit">Edit</a> |
                <a href="delete.php?id=<?= $r['id'] ?>" class="btn-action btn-delete" onclick="return confirm('Delete item ID <?= $r['id'] ?>?')">Delete</a>
            </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>