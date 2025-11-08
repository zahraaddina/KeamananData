<?php
// lab/demo: intentionally vulnerable — DO NOT USE IN PRODUCTION
require 'auth_simple.php';
$pdo = pdo_connect();
$q = $_GET['q'] ?? '';
$results = [];

if ($q !== '') {
    // VULNERABLE: concatenation => SQLi demonstration
    $sql = "SELECT c.id, u.username, c.comment, c.created_at
            FROM comments c LEFT JOIN users u ON c.user_id=u.id
            WHERE c.comment LIKE '%$q%' OR u.username LIKE '%$q%'";
    // echo "<!-- $sql -->";
    $stmt = $pdo->query($sql);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Search Comments — LAB (VULNERABLE)</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background: linear-gradient(120deg,#f8fafc 0%, #eef6ff 100%); min-height:100vh; }
    .search-card { max-width:980px; margin:42px auto; border-radius:12px; box-shadow:0 10px 30px rgba(15,23,42,0.06); }
    .brand { width:56px; height:56px; border-radius:10px; display:inline-flex; align-items:center; justify-content:center; background:#fff; box-shadow:0 4px 12px rgba(16,24,40,0.06); font-weight:700; color:#0d6efd; }
    .comment { padding:12px; border-radius:8px; background:#fff; box-shadow:0 6px 18px rgba(15,23,42,0.03); margin-bottom:12px; }
    .meta { color:#6c757d; font-size:.9rem; }
    .note { font-size:.85rem; color:#6c757d; }
    .vuln-badge { font-size:.75rem; background:#ffe9e9; color:#b02a37; padding:4px 8px; border-radius:999px; }
  </style>
</head>
<body>
  <div class="card search-card">
    <div class="card-body p-4">
      <div class="d-flex align-items-center mb-3">
        <div class="brand me-3">LAB</div>
        <div>
          <h4 class="mb-0">Search Komentar (VULNERABLE)</h4>
          <div class="note">Demo: intentionally vulnerable untuk praktik SQL Injection & Stored XSS.</div>
        </div>
        <div class="ms-auto">
          <span class="vuln-badge">VULNERABLE</span>
            <a class="btn btn-outline-warning btn-sm" href="dashboard.php">Kembali</a>
        </div>
      </div>

      <form class="row g-2 align-items-center" method="get" action="">
        <div class="col-md-9">
          <input name="q" class="form-control" placeholder="Cari komentar atau username..." value="<?php echo htmlspecialchars($q, ENT_QUOTES, 'UTF-8'); ?>">
        </div>
        <div class="col-md-3 d-grid">
          <button class="btn btn-primary" type="submit">Search</button>
        </div>
      </form>

      <?php if ($q !== ''): ?>
        <hr class="my-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <div>
            <h5 class="mb-0">Hasil untuk: <small class="text-muted"><?php echo htmlspecialchars($q, ENT_QUOTES, 'UTF-8'); ?></small></h5>
            <div class="note">Menampilkan komentar yang mengandung kata pencarian atau username.</div>
          </div>
          <div class="text-end">
            <small class="text-muted"><?php echo count($results); ?> hasil</small>
          </div>
        </div>

        <?php if (empty($results)): ?>
          <div class="alert alert-info">Tidak ada hasil.</div>
        <?php else: ?>
          <div>
            <?php foreach ($results as $r): ?>
              <div class="comment">
                <div class="d-flex justify-content-between">
                  <div>
                    <strong><?php echo htmlspecialchars($r['username'] ?? 'Guest', ENT_QUOTES, 'UTF-8'); ?></strong>
                    <div class="meta"><?php echo htmlspecialchars($r['created_at'] ?? '', ENT_QUOTES, 'UTF-8'); ?></div>
                  </div>
                  <!-- contoh tombol (non-functional) untuk lab -->
                  <div>
                    <a href="#" class="btn btn-sm btn-outline-secondary">View</a>
                  </div>
                </div>

                <div class="mt-2">
                  <!-- RAW OUTPUT (INTENTIONAL): stored XSS possible -->
                  <?php echo $r['comment']; ?>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      <?php endif; ?>

    </div>

    <div class="card-footer text-muted small">
      Catatan lab: file ini rentan terhadap <strong>SQL Injection</strong> dan <strong>Stored XSS</strong>. 
      Jangan jalankan di lingkungan publik. Gunakan VM/isolated sandbox saat demonstrasi.
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
