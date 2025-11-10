<?php
// lab/demo: intentionally vulnerable — DO NOT USE IN PRODUCTION
require 'auth_simple.php';
$pdo = pdo_connect();
$q = $_GET['q'] ?? '';
$results = [];

if ($q !== '') {
    // VULNERABLE: concatenation => SQLi demonstration
    // NOTE: For demonstration purposes, this part of the code remains vulnerable.
    $sql = "SELECT c.id, u.username, c.comment, c.created_at
            FROM comments c LEFT JOIN users u ON c.user_id=u.id
            WHERE c.comment LIKE '%$q%' OR u.username LIKE '%$q%'";
    // echo ""; // Uncomment to see the vulnerable query
    
    try {
        $stmt = $pdo->query($sql);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        // Suppress detailed DB error for the demo, show generic error
        $error = 'Terjadi kesalahan saat mencari (Potensi SQL Injection).';
    }
}
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Search Comments — LAB (VULNERABLE)</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root {
            /* Warna Merah Rentan Sesuai Permintaan */
            --color-primary-vuln: #AC1616; 
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
            max-width: 900px; 
            margin: 42px auto; 
            border-radius: 12px; 
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08); 
            border: 1px solid #959595ff; 
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
            /* Gaya Merah untuk Kerentanan */
            width: 48px;
            height: 48px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #AC1616; /* Background sangat terang */
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
            border: 1px solid #868686ff;
            padding: 10px 15px 10px 40px; 
            height: 45px;
            font-size: 1rem;
        }
        
        .search-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--color-text-muted);
            pointer-events: none; 
        }
        .btn-search {
            /* Warna Merah untuk tombol Search */
            background-color: var(--color-primary-vuln); 
            border-color: var(--color-primary-vuln);
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
            background-color: #8c0c0c; 
            border-color: #8c0c0c;
        }

        /* Hasil Pencarian */
        .result-info {
            font-weight: 500;
            color: var(--color-text-dark);
        }
        .count-badge { 
            font-weight: 600; 
            color: var(--color-primary-vuln);
        }
        
        /* Comment Card */
        .comment { 
            padding: 15px; 
            border-radius: 8px; 
            background: #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
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
        /* Tidak ada highlight di sini karena output tidak di-escape (rentan) */
        
        /* Footer */
        .card-footer {
            background-color: #f7f7f7;
            border-top: 1px solid #eee;
            border-radius: 0 0 12px 12px;
            padding: 15px 28px;
        }

        /* Compare Button Styling (Warna Hijau untuk Kembali ke Safe) */
        .compare-section {
            display: flex;
            align-items: center;
            background: #f0fff0;
            border: 1px solid #d0f0d0;
            border-radius: 8px;
            padding: 15px;
            margin-top: 20px;
        }
        .compare-text {
            font-size: .95rem;
            color: #00875A;
            font-weight: 500;
        }
        .btn-compare-safe {
            background-color: #92e192;
            color: #00875A;
            border: none;
            font-weight: 600;
            border-radius: 6px;
            transition: background-color 0.2s;
        }
        .btn-compare-safe:hover {
            background-color: #72d172;
            color: #006a45;
        }
        
    </style>
</head>
<body>
    <div class="card search-card">
        <div class="card-body">
            
            <div class="header-section mb-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="header-badge-icon">
                        <img src="images/rentan.png" alt="Vulnerable Icon">
                    </div>
                    <div class="header-title">
                        <h4 class="mb-0">Search Komentar (VULNERABLE)</h4>
                        <div class="note">Demo: intentionally vulnerable untuk praktik SQL Injection & Stored XSS.</div>
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
                <i class="bi bi-info-circle-fill me-3" style="color: #00875A; font-size: 1.2rem;"></i>
                <div class="compare-text">Bandingkan dengan versi aman.</div>
                <div class="ms-auto">
                    <a class="btn btn-sm btn-compare-safe" href="search_safe.php">Compare with SAFE Version <i class="bi bi-arrow-right"></i></a>
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

                <?php if (isset($error)): ?>
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
                                    <?php echo $r['comment']; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

        </div>

        <div class="card-footer text-muted small">
            Catatan lab: file ini rentan terhadap SQL Injection dan Stored XSS. 
            Jangan jalankan di lingkungan publik.
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>