<?php
// dashboard.php
// Protected dashboard page. Requires login.
// Shows links to vulnerable/safe demo pages and Logout.

require 'auth_simple.php'; // harus tersedia di project
$pdo = pdo_connect();

$user = current_user();
if (!$user) {
    // jika belum login, redirect ke login
    header('Location: login.php');
    exit;
}

// fetch some simple stats (best-effort; tidak fatal jika query gagal)
$stats = [
    'posts' => null,
    'comments' => null,
    'users' => null,
];
try {
    $stmt = $pdo->query("SELECT COUNT(*) AS cnt FROM posts");
    $stats['posts'] = (int)$stmt->fetch(PDO::FETCH_ASSOC)['cnt'];
} catch (Exception $e) { /* ignore */ }

try {
    $stmt = $pdo->query("SELECT COUNT(*) AS cnt FROM comments");
    $stats['comments'] = (int)$stmt->fetch(PDO::FETCH_ASSOC)['cnt'];
} catch (Exception $e) { /* ignore */ }

try {
    $stmt = $pdo->query("SELECT COUNT(*) AS cnt FROM users");
    $stats['users'] = (int)$stmt->fetch(PDO::FETCH_ASSOC)['cnt'];
} catch (Exception $e) { /* ignore */ }

function esc($s) {
    return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

// Ambil username, pastikan ada
$username_display = esc($user['username'] ?? 'Guest');
$posts_count = is_null($stats['posts']) ? '—' : esc((string)$stats['posts']);
$comments_count = is_null($stats['comments']) ? '—' : esc((string)$stats['comments']);
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Dashboard — Lab Demo</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    body { 
        background: #fff; 
        min-height:100vh; 
        font-family: 'Poppins', sans-serif; 
    }
    .wrap { max-width:1100px; margin:36px auto; padding:18px; }

    /* --- Desain Topbar --- */
    .topbar-new {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 15px 0; 
      margin-bottom: 24px;
      border-bottom: 1px solid #eee; 
    }
    .topbar-left {
      display: flex;
      align-items: center;
      gap: 10px;
    }
    .dashboard-logo {
      width: 30px; 
      height: 30px;
    }
    .dashboard-title {
      font-size: 1.8rem;
      font-weight: 700;
      color: #b02a37; 
      margin-left: 5px;
    }
    .topbar-right {
      display: flex;
      align-items: center;
      gap: 20px;
    }
    .user-greeting {
      font-size: 1.1rem;
      font-weight: 500;
      color: #333;
    }
    .logout-btn-new {
      background-color: #b02a37; 
      color: #fff;
      font-weight: 600;
      border: none;
      padding: 8px 15px;
      border-radius: 6px;
      text-decoration: none;
    }
    .logout-btn-new:hover {
      background-color: #9a2430;
      color: #fff;
    }
    /* --- Desain Statistik Kartu (Diperbarui) --- */
    .card-grid {    
        display:grid;    
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));    
        gap:16px;    
        margin-top:18px;    
    }
    .dash-card {    
        border-radius:10px; /* Radius sedikit lebih kecil */
        padding:20px; 
        background:#fff;    
        box-shadow: none; /* Hapus bayangan */
        min-height:100px; /* Diperkecil agar lebih ringkas */
        display:flex;    
        flex-direction:column;    
        justify-content:space-between;
        border: 1px solid #9c9c9cff; /* Border tipis abu-abu */
    }
    .stat-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .stat-icon-bg {
        width: 45px; 
        height: 45px;
        background-color: #b02a37; 
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: none; /* Hapus bayangan ikon */
    }
    .stat-icon {
        width: 28px; 
        height: 28px;
        filter: brightness(0) invert(1); 
    }
    .stat-number {
        font-size: 2rem; 
        font-weight: 700;
        color: #343a40;
    }
    .stat-label {
        font-size: 1.1rem;
        font-weight: 500; /* Font weight disesuaikan */
        color: #333; /* Warna label hitam */
        margin: 0; /* Hapus margin atas */
    }
    
    /* --- Desain Kartu Versi (SAFE/RENTAN) Diperbarui --- */
    .version-section {
        margin-top: 30px;
    }
    .version-title {
        font-size: 1.5rem;
        font-weight: 600;
        color: #b02a37;
        margin-bottom: 15px;
    }
    
    /* Base Card Style */
    .styled-card {
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: none; /* Hapus bayangan */
        border: 1px solid #9c9c9cff; /* Border tipis abu-abu */
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        min-height: 200px; 
    }
    
    /* Safe Card Style */
    .safe-card-style {
        background: #fff;
    }
    .safe-card-icon-container {
        display: flex;
        align-items: center;
        margin-bottom: 15px;
    }
    .safe-card-icon-bg {
        width: 40px;
        height: 40px;
        background-color: #5cb85c; 
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 15px;
    }
    .safe-card-icon {
        width: 24px;
        height: 24px;
    }
    .safe-card-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: #333;
        margin: 0;
    }
    .safe-card-desc {
        font-size: 0.95rem;
        color: #6c757d;
        margin-bottom: 15px;
    }
    .btn-safe-new {
        background: #5cb85c; 
        color: white;
        font-weight: 600;
        width: 100%;
        padding: 10px;
        border-radius: 6px;
        border: none;
        text-decoration: none;
        text-align: center;
        transition: background 0.2s;
    }
    .btn-safe-new:hover {
        background: #4cae4c;
        color: white;
    }

    /* Vulnerable Card Style */
    .vulnerable-card-style {
        background: #fff;
    }
    .vulnerable-card-icon-container {
        display: flex;
        align-items: center;
        margin-bottom: 15px;
    }
    .vulnerable-card-icon-bg {
        width: 40px;
        height: 40px;
        background-color: #b02a37; 
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 15px;
    }
    .vulnerable-card-icon {
        width: 24px;
        height: 24px;
    }
    .vulnerable-card-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: #333;
        margin: 0;
    }
    .vulnerable-card-desc {
        font-size: 0.95rem;
        color: #6c757d;
        margin-bottom: 15px;
    }
    .btn-vulnerable-new {
        background: #b02a37; 
        color: white;
        font-weight: 600;
        width: 100%;
        padding: 10px;
        border-radius: 6px;
        border: none;
        text-decoration: none;
        text-align: center;
        transition: background 0.2s;
    }
    .btn-vulnerable-new:hover {
        background: #9a2430;
        color: white;
    }

    /* Styling lama yang tidak digunakan */
    .card, .safe-card, .vulnerable-card, .card-icon, .safe-icon, .vulnerable-icon, .btn-safe, .btn-vulnerable {
        display: none !important; 
    }

    h3.version-title, h4 {
        font-family: 'Poppins', sans-serif;
        font-weight: 600;
    }
    .text-secondary {
        color: #6c757d !important;
    }
  </style>
</head>
<body>
  <div class="wrap">
    <div class="topbar-new">
      <div class="topbar-left">
        <img src="images/dashboardlogo.png" alt="Logo" class="dashboard-logo">
        <div class="dashboard-title">Dashboard</div>
      </div>
      <div class="topbar-right">
        <div class="user-greeting">Hallo! <?php echo $username_display; ?></div>
        <a href="logout.php" class="logout-btn-new">Logout</a>
      </div>
    </div>

    <div class="mt-4">
        <h2 style="font-size:1.5rem; font-weight:700;">Praktik Cross-Site Scripting (XSS)</h2>
        <p class="text-secondary">Selamat datang di lab simulasi! Halaman ini menyediakan skenario untuk memahami dan menguji kerentanan Cross-Site Scripting (XSS). Pilih salah satu modul di bawah untuk memulai.</p>
    </div>

    <div class="card-grid">
        <div class="dash-card">
            <div class="stat-content">
                <div>
                    <div class="stat-label">Total Posts</div>
                    <div class="stat-number"><?php echo $posts_count; ?></div>
                </div>
                <div class="stat-icon-bg">
                    <img src="images/post.png" alt="Post Icon" class="stat-icon">
                </div>
            </div>
        </div>

        <div class="dash-card">
            <div class="stat-content">
                <div>
                    <div class="stat-label">Comments</div>
                    <div class="stat-number"><?php echo $comments_count; ?></div>
                </div>
                <div class="stat-icon-bg">
                    <img src="images/comment.png" alt="Comment Icon" class="stat-icon">
                </div>
            </div>
        </div>
    </div>
    
    <div class="version-section">
      <h3 class="version-title">Versi Aman</h3>
      <div class="row">
        <div class="col-md-6">
          <div class="styled-card safe-card-style">
            <div class="safe-card-icon-container">
              <div class="safe-card-icon-bg">
                <img src="images/safe.png" alt="Safe Icon" class="safe-card-icon">
              </div>
              <h5 class="safe-card-title">Post (SAFE)</h5>
            </div>
            <p class="safe-card-desc">Halaman ini merupakan versi aman dari fitur komentar. Setiap input pengguna akan di-escape menggunakan htmlspecialchars() agar tidak bisa menjalankan script berbahaya. Di sini kamu bisa melihat perbedaan hasil tampilan komentar antara versi rentan dan versi aman.</p>
            <a href="post_safe.php" class="btn btn-block btn-safe-new">Buka</a>
          </div>
        </div>
        <div class="col-md-6">
          <div class="styled-card safe-card-style">
            <div class="safe-card-icon-container">
              <div class="safe-card-icon-bg">
                <img src="images/safe.png" alt="Safe Icon" class="safe-card-icon">
              </div>
              <h5 class="safe-card-title">Search (SAFE)</h5>
            </div>
            <p class="safe-card-desc">Halaman ini merupakan versi aman dari fitur pencarian komentar. Menggunakan prepared statement untuk mencegah SQL Injection dan output escaping untuk mencegah XSS. Hasil pencarian ditampilkan secara aman dengan highlight pada teks yang cocok dengan kata kunci.</p>
            <a href="search_safe.php" class="btn btn-block btn-safe-new">Buka</a>
          </div>
        </div>
      </div>
    </div>

    <div class="version-section">
      <h3 class="version-title">Versi Rentan</h3>
      <div class="row">
        <div class="col-md-6">
          <div class="styled-card vulnerable-card-style">
            <div class="vulnerable-card-icon-container">
              <div class="vulnerable-card-icon-bg">
                <img src="images/rentan.png" alt="Vulnerable Icon" class="vulnerable-card-icon">
              </div>
              <h5 class="vulnerable-card-title">Post (RENTAN)</h5>
            </div>
            <p class="vulnerable-card-desc">Halaman ini sengaja dibuat tidak aman untuk demonstrasi serangan Stored XSS. Komentar yang dikirim akan disimpan dan ditampilkan tanpa filter, sehingga script HTML/JavaScript di dalam komentar bisa dieksekusi langsung oleh browser. ⚠️ Gunakan hanya untuk latihan di lingkungan lokal!</p>
            <a href="post_vul.php" class="btn btn-block btn-vulnerable-new">Buka</a>
          </div>
        </div>
        <div class="col-md-6">
          <div class="styled-card vulnerable-card-style">
            <div class="vulnerable-card-icon-container">
              <div class="vulnerable-card-icon-bg">
                <img src="images/rentan.png" alt="Vulnerable Icon" class="vulnerable-card-icon">
              </div>
              <h5 class="vulnerable-card-title">Search (RENTAN)</h5>
            </div>
            <p class="vulnerable-card-desc">Pencarian ini rentan terhadap SQL Injection dan Stored XSS. Input dari pengguna langsung digabungkan ke dalam query SQL tanpa disanitasi, dan bisa dieksploitasi dengan perintah SQL berbahaya. Hasil pencarian juga ditampilkan tanpa filter, XSS saat data mengandung script.</p>
            <a href="search_vul.php" class="btn btn-block btn-vulnerable-new">Buka</a>
          </div>
        </div>
      </div>
    </div>

    <div class="mt-4">
      <h4>Tools & Info</h4>
      <p>Gunakan tombol di atas untuk membuka halaman demo. Ingat: jalankan hanya di lingkungan lab/terisolasi.</p>
      <div class="d-flex gap-2">
        <a href="#" class="btn btn-outline-danger">Open vulnerable post (sample)</a>
        <a href="#" class="btn btn-outline-success">Open safe post (sample)</a>
      </div>
      <p class="mt-3">Tip: untuk demo, siapkan beberapa akun dummy (alice, bob) dan beberapa komentar XSS/SQLi di DB.</p>
      <p>Users: <span class="fw-bold"><?php echo is_null($stats['users']) ? '—' : esc((string)$stats['users']); ?></span></p>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>