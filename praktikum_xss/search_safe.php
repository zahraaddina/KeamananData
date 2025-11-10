<?php
// search_safe.php (SAFE version)
// - Uses prepared statements to prevent SQL injection.
// - Escapes all user content to prevent XSS.
// - Highlights matched query terms in the escaped output (safe).
// - Styling uses Bootstrap and matches the vulnerable UI for teaching comparison.
//
// Usage: place in same project that provides pdo_connect() and auth_simple.php
// DO NOT mix this file with vulnerable one on a public server (keep lab isolated).

require 'auth_simple.php';
$pdo = pdo_connect();

$q = trim((string)($_GET['q'] ?? ''));
$results = [];
$error = null;

if ($q !== '') {
    try {
        // safe prepared statement with case-insensitive search
        $sql = "SELECT c.id, u.username, c.comment, c.created_at
                FROM comments c
                LEFT JOIN users u ON c.user_id = u.id
                WHERE LOWER(c.comment) LIKE :q OR LOWER(u.username) LIKE :q
                ORDER BY c.created_at DESC
                LIMIT 200"; // safety limit for results
        $stmt = $pdo->prepare($sql);
        $like = '%' . mb_strtolower($q, 'UTF-8') . '%';
        $stmt->execute([':q' => $like]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        // don't reveal DB error in production; log if needed
        $error = 'Terjadi kesalahan saat mencari. Coba lagi.';
    }
}

// helper: safely escape and optionally highlight the query in the escaped text
function safe_highlight(string $text, string $query): string {
    // escape first to avoid XSS
    $escaped = htmlspecialchars($text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    if ($query === '') return nl2br($escaped);

    // prepare safe regex of the escaped query (preg_quote on the escaped form prevents issues)
    $safe_q = htmlspecialchars($query, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    $pattern = '/' . preg_quote($safe_q, '/') . '/iu'; // case-insensitive, unicode
    // wrap matches with <mark> (safe because $escaped is escaped; replacement uses captured group)
    $highlighted = preg_replace($pattern, '<mark>$0</mark>', $escaped);
    // convert newlines to <br>
    return nl2br($highlighted);
}
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Search Comments — SAFE</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root {
            --color-primary-safe: #3BE138; /* Hijau Aman Sesuai Permintaan */
            --color-text-dark: #333;
            --color-text-muted: #6c757d;
        }

        /* Global & Layout */
        body { 
            background: #f8f9fa; 
            min-height: 100vh; 
            font-family: 'Poppins', sans-serif; 
        }
        .search-card { 
            max-width: 900px; /* Lebar lebih dekat ke post card */
            margin: 42px auto; 
            border-radius: 12px; 
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08); /* Shadow halus */
            border: 1px solid #d6d6d6ff; 
            background: #fff;
        }
        .card-body {
            padding: 28px !important;
        }
        
        /* Header & Badge */
        .header-section {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .header-badge-icon {
            /* Gaya Mirip dengan Icon Shield di Gambar */
            width: 48px;
            height: 48px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #3BE138; /* Background sangat terang */
            padding: 8px;
        }
        .header-badge-icon img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }
        .header-title h4 {
            font-weight: 600;
            color: var(--color-text-dark);
            margin-bottom: 0;
        }
        .note { 
            font-size: .9rem; 
            color: var(--color-text-muted); 
            margin-top: 2px;
        }

        /* Tombol Kembali */
        .btn-kembali {
            color: #495057;
            border-color: #dee2e6;
            font-weight: 500;
            border-radius: 6px;
            padding: .375rem .75rem;
        }
        .btn-kembali:hover {
            background-color: #f8f9fa;
            border-color: #ced4da;
        }

        /* Search Form Styling */
        .input-group-search {
            position: relative;
            display: flex;
            align-items: stretch;
            width: 100%;
        }
        .form-control-search {
            border-radius: 6px 0 0 6px;
            border: 1px solid #b0b0b0ff;
            padding: 10px 15px 10px 40px; /* Padding kiri untuk ikon */
            height: 45px;
            font-size: 1rem;
        }
        .form-control-search:focus {
            border-color: var(--color-primary-safe);
            box-shadow: none;
        }
        .search-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--color-text-muted);
            pointer-events: none; /* agar tidak mengganggu klik input */
        }
        .btn-search {
            background-color: #3BE138; /* Warna hijau yang lebih gelap untuk kontras */
            color: white;
            font-weight: 600;
            border-radius: 0 6px 6px 0;
            padding: 0 20px;
            height: 45px;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: background-color 0.2s;
        }
        .btn-search:hover {
            background-color: #006a45;
            border-color: #006a45;
        }

        /* Hasil Pencarian */
        .result-info {
            font-weight: 500;
            color: var(--color-text-dark);
        }
        .count-badge { 
            font-weight: 600; 
            color: var(--color-primary-safe);
        }
        
        /* Comment Card */
        .comment { 
            padding: 15px; 
            border-radius: 8px; 
            background: #f7f7f7; /* Background abu-abu muda */
            box-shadow: none;
            border: 1px solid #eee;
            margin-bottom: 12px; 
        }
        .comment strong {
            font-weight: 600;
            color: var(--color-text-dark);
        }
        .meta { 
            color: var(--color-text-muted); 
            font-size: .85rem; 
        }
        mark { 
            background:#f0ad4e; /* Warna highlight oranye/kuning */
            padding: 0 .15rem; 
            border-radius: .15rem; 
            color: #333; /* Pastikan teks di dalam mark terbaca */
        }

        /* Footer */
        .card-footer {
            background-color: #f7f7f7;
            border-top: 1px solid #eee;
            border-radius: 0 0 12px 12px;
            padding: 15px 28px;
        }

        /* Compare Button Styling */
        .compare-section {
            display: flex;
            align-items: center;
            background: #fff8f8;
            border: 1px solid #f9cccc;
            border-radius: 8px;
            padding: 15px;
            margin-top: 20px;
        }
        .compare-text {
            font-size: .95rem;
            color: #a72834;
            font-weight: 500;
        }
        .btn-compare {
            background-color: #f79d9d;
            color: #a72834;
            border: none;
            font-weight: 600;
            border-radius: 6px;
            transition: background-color 0.2s;
        }
        .btn-compare:hover {
            background-color: #f57e7e;
            color: #791e27;
        }
        
    </style>
</head>
<body>
    <div class="card search-card">
        <div class="card-body">
            
            <div class="header-section mb-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="header-badge-icon">
                        <img src="images/safe.png" alt="Safe Icon">
                    </div>
                    <div class="header-title">
                        <h4 class="mb-0">Search Komentar (SAFE)</h4>
                        <div class="note">Versi aman: prepared statements + escaping. Cocok untuk perbandingan.</div>
                    </div>
                </div>
                <div class="ms-auto d-flex align-items-center gap-2">
                    <a class="btn btn-sm btn-kembali" href="dashboard.php"><i class="bi bi-arrow-left"></i> Kembali</a>
                </div>
            </div>

            <form class="mb-4" method="get" action="">
                <div class="input-group-search">
                    <span class="search-icon"><i class="bi bi-search"></i></span>
                    <input name="q" class="form-control form-control-search" placeholder="Cari Komentar atau username..." value="<?php echo htmlspecialchars($q, ENT_QUOTES, 'UTF-8'); ?>" autofocus>
                    <button class="btn btn-search" type="submit">
                        <i class="bi bi-search"></i> Search
                    </button>
                </div>
            </form>

            <div class="compare-section">
                <i class="bi bi-x-circle-fill me-3" style="color: #a72834; font-size: 1.2rem;"></i>
                <div class="compare-text">Bandingkan dengan versi rentan.</div>
                <div class="ms-auto">
                    <a class="btn btn-sm btn-compare" href="search_vul.php">Compare with Vulnerable Version <i class="bi bi-arrow-right"></i></a>
                </div>
            </div>

            <?php if ($q !== ''): ?>
                <hr class="my-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h5 class="mb-0 result-info">Hasil untuk: <small class="text-muted"><?php echo htmlspecialchars($q, ENT_QUOTES, 'UTF-8'); ?></small></h5>
                    </div>
                    <div class="text-end">
                        <span class="count-badge"><?php echo count($results); ?> hasil</span>
                    </div>
                </div>

                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
                <?php endif; ?>

                <?php if (empty($results)): ?>
                    <div class="alert alert-info">Tidak ada hasil untuk pencarian ini.</div>
                <?php else: ?>
                    <div>
                        <?php foreach ($results as $r): ?>
                            <div class="comment">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong><?php echo htmlspecialchars($r['username'] ?? 'Guest', ENT_QUOTES, 'UTF-8'); ?></strong>
                                        <span class="meta ms-3"><?php echo htmlspecialchars($r['created_at'] ?? '', ENT_QUOTES, 'UTF-8'); ?></span>
                                    </div>
                                    <!-- <div>
                                        <a href="post_safe.php?id=<?php echo (int)$r['post_id']; ?>" class="btn btn-sm btn-outline-secondary" style="font-size: .85rem;">View Post</a>
                                    </div> -->
                                </div>

                                <div class="mt-2 text-break">
                                    <?php echo safe_highlight((string)$r['comment'], $q); ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

        </div>

        <div class="card-footer text-muted small">
            Catatan: file ini aman — menggunakan prepared statements dan escaping output. 
            Untuk demonstrasi perbandingan, bandingkan dengan `search_vul.php` (intentionally vulnerable).
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>