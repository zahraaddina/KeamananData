<?php
// post_safe.php (SAFE - same visual style as the vulnerable version)
// - Comments are escaped (no stored XSS).
// - Uses prepared statements for DB actions.
// - CSRF token + owner-only delete.
// - Visual styling duplicated from the vulnerable page for easy comparison.

require 'auth_simple.php';

$pdo = pdo_connect();
$post_id = (int)($_GET['id'] ?? 1);
$user = current_user(); // may be null

// ensure session CSRF token exists
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(24));
}

$msg = '';
$err = '';

// Handle new comment (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'post_comment') {
    if (!$user) {
        $err = 'Anda harus login untuk mengirim komentar.';
    } else {
        if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
            $err = 'Request tidak valid (CSRF).';
        } else {
            $comment = trim((string)($_POST['comment'] ?? ''));
            if ($comment === '') {
                $err = 'Komentar tidak boleh kosong.';
            } elseif (mb_strlen($comment, 'UTF-8') > 2000) {
                $err = 'Komentar terlalu panjang (maks 2000 karakter).';
            } else {
                $stmt = $pdo->prepare("INSERT INTO comments (user_id, post_id, comment, created_at) VALUES (:uid, :pid, :c, NOW())");
                $stmt->execute([
                    ':uid' => $user['id'],
                    ':pid' => $post_id,
                    ':c'    => $comment
                ]);
                header("Location: post_safe.php?id=$post_id");
                exit;
            }
        }
    }
}

// Handle delete comment (POST) - only owner allowed
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_comment') {
    if (!$user) {
        $err = 'Anda harus login untuk menghapus komentar.';
    } else {
        if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
            $err = 'Request tidak valid (CSRF).';
        } else {
            $del_id = (int)($_POST['delete_comment_id'] ?? 0);
            if ($del_id <= 0) {
                $err = 'ID komentar tidak valid.';
            } else {
                $stmt = $pdo->prepare("SELECT user_id FROM comments WHERE id = :cid");
                $stmt->execute([':cid' => $del_id]);
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                if (!$row) {
                    $err = 'Komentar tidak ditemukan.';
                } elseif ((int)$row['user_id'] !== (int)$user['id']) {
                    $err = 'Anda tidak berhak menghapus komentar ini.';
                } else {
                    $stmt = $pdo->prepare("DELETE FROM comments WHERE id = :cid");
                    $stmt->execute([':cid' => $del_id]);
                    header("Location: post_safe.php?id=$post_id");
                    exit;
                }
            }
        }
    }
}

// Fetch post
$stmt = $pdo->prepare("SELECT * FROM posts WHERE id=:id LIMIT 1");
$stmt->execute([':id' => $post_id]);
$post = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch comments
$stmt = $pdo->prepare("
    SELECT c.*, u.username 
    FROM comments c 
    LEFT JOIN users u ON c.user_id = u.id 
    WHERE c.post_id = :pid 
    ORDER BY c.created_at DESC
");
$stmt->execute([':pid' => $post_id]);
$comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// helper to safely escape and preserve newlines
function esc_nl(string $s): string {
    return nl2br(htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5, 'UTF-8'));
}

// New helper: Mendapatkan inisial dan kelas warna avatar
function get_avatar_data(string $username): array {
    $initial = strtoupper(mb_substr($username, 0, 1, 'UTF-8'));
    // Tentukan kelas background berdasarkan inisial (contoh sederhana)
    $first_char = strtolower(mb_substr($username, 0, 1, 'UTF-8'));
    if (in_array($first_char, ['a', 'e', 'i', 'o', 'u'])) {
        $color_class = 'avatar-bg-A'; // Biru Muda (mirip "bob" di gambar)
    } elseif (in_array($first_char, ['b', 'c', 'd', 'f'])) {
        $color_class = 'avatar-bg-B'; // Hijau Muda (mirip "alice" di gambar)
    } elseif (in_array($first_char, ['g', 'h', 'j', 'k', 'l', 'm', 'n'])) {
        $color_class = 'avatar-bg-C'; // Kuning
    } else {
        $color_class = 'avatar-bg-D'; // Biru (default)
    }

    return ['initial' => $initial, 'class' => $color_class];
}
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title><?php echo htmlspecialchars($post['title'] ?? 'Post', ENT_QUOTES, 'UTF-8'); ?> — SAFE</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root {
            --color-primary: #AC1616; /* Merah Tua */
            --color-safe: #38c172; /* Hijau Aman */
            --color-text-dark: #333;
            --color-text-muted: #6c757d;
        }

        /* Global & Layout */
        body { 
            background: #f8f9fa; /* Background lebih cerah */
            min-height: 100vh; 
            font-family: 'Poppins', sans-serif; 
        }
        .container-main { 
            max-width: 900px; 
            margin: 36px auto; 
        }
        .post-card { 
            border-radius: 12px; 
            box-shadow: none; 
            border: 1px solid #ddd; 
            overflow: hidden; 
            background: #fff; 
        }
        .post-body { 
            padding: 28px; 
        }
        
        /* Typography & Meta */
        .post-meta, .note { 
            color: var(--color-text-muted); 
            font-size: .9rem; 
        }
        .post-body h2 {
            font-weight: 700;
            color: var(--color-text-dark);
            font-size: 1.8rem;
        }

        /* SAFE Badge */
        .safe-badge { 
            font-size: .75rem; 
            background: var(--color-safe); 
            color: white; 
            padding: 6px 12px; 
            border-radius: 6px; 
            font-weight: 600;
        }
        
        /* --- START KOMENTAR STYLING DARI GAMBAR --- */
        .comment-card { 
    margin-top: 15px;
    padding: 15px; 
    background: #f7f7f7;
    border-radius: 8px;
    box-shadow: none;
    border: none;
    display: flex;
    gap: 15px;
    align-items: flex-start;
    width: 100%;
    text-align: left; /* PENTING: Menetapkan perataan teks ke kiri */ 
    justify-content: flex-start;
    margin-left: 0;
    margin-right: 0;
}

        /* Avatar Inisial */
        .comment-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            font-weight: 500; /* Sedikit lebih tipis dari 600 */
            font-size: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0; /* Pastikan tidak menyusut */
            color: white; /* Default white, diubah oleh kelas di bawah */
        }

        /* Warna Avatar disesuaikan dengan contoh di gambar */
        /* Bob (B) terlihat Biru Muda/Cyan */
        .avatar-bg-A { background-color: #5bc0de; color: #fff; } 
        /* Alice (A) terlihat Hijau Muda */
        .avatar-bg-B { background-color: #5cb85c; color: #fff; } 
        .avatar-bg-C { background-color: #f0ad4e; color: #fff; } 
        .avatar-bg-D { background-color: #0d6efd; color: #fff; } 

        .comment-content {
    flex-grow: 1;
    /* PENTING: Pastikan konten komentar menggunakan lebar penuh area yang tersedia */
    width: 100%; 
}

        .comment-content .username {
            font-weight: 600;
            color: var(--color-text-dark);
        }
        .comment-content .timestamp {
            font-size: .8rem;
            color: var(--color-text-muted);
            /* Memindahkan timestamp ke kanan, sejajar dengan tombol hapus/nama */
             margin-left: 0;    /* hapus margin-left:auto yang memaksa posisi */
             margin-inline-start: 8px; /* sedikit jarak dari nama */
        }
        
       
        .comment-header {
            display: flex;
            justify-content: space-between;
            align-items: center; 
            margin-bottom: 5px;
        }

        .escaped-comment { 
    /* Mengatasi wrapping dan membiarkan teks rata kiri secara default */
    white-space: pre-wrap; 
    font-size: .95rem; 
    color: #444;
    line-height: 1.5;
    text-align: left; /* PENTING: Jaminan rata kiri untuk teks komentar */
}
        /* --- END KOMENTAR STYLING --- */


        /* Form Controls */
        textarea.form-control {
            border-radius: 6px;
            border: 1px solid #ddd;
            padding: 10px;
        }
        textarea.form-control:focus {
            border-color: var(--color-primary);
            box-shadow: none;
            outline: none;
        }

        /* Buttons */
        .btn-primary {
            background-color: #AC1616; 
            border-color: #AC1616;
            font-weight: 500;
            border-radius: 6px;
        }
        .btn-primary:hover {
            background-color: #0b5ed7;
            border-color: #0a58ca;
        }
        .btn-danger {
            /* Ubah btn-danger untuk tombol Hapus menjadi lebih kecil dan ringan */
            background-color: transparent;
            border: none;
            color: var(--color-text-muted);
            padding: 0;
            font-size: .8rem;
            font-weight: 400;
            transition: color 0.1s ease;
        }
        .btn-danger:hover {
            background-color: transparent;
            color: #dc3545; /* Merah untuk hover */
        }
        .btn-outline-warning {
            color: var(--color-primary);
            border-color: var(--color-primary);
        }
        .btn-outline-warning:hover {
             background-color: var(--color-primary);
             color: white;
        }
        
        /* Header Logo/Badge */
        .header-badge {
            width: 48px;
            height: 48px;
            background: var(--color-safe); 
            color: white;
            font-weight: 700;
            font-size: 1.1rem;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: none;
        }

        .card-footer {
            background-color: #f7f7f7;
            border-top: 1px solid #eee;
            border-radius: 0 0 12px 12px;
            padding: 12px 28px;
        }

    </style>
</head>
<body>
    <div class="container container-main">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="d-flex align-items-center gap-3">
                <div class="header-badge">SAFE</div>
                <div>
                    <h4 class="mb-0" style="font-weight: 600; color: #333;">Contoh Artikel</h4>
                    <div class="note">Halaman ini SAFE — komentar akan di-escape sehingga tidak mengeksekusi HTML/JS.</div>
                </div>
            </div>

            <div class="text-end">
                <span class="safe-badge">SAFE</span>
            </div>
        </div>
<div class="card post-card">
            <div class="post-body">
                <div class="d-flex justify-content-between mb-2">
                    <div>
                        <?php if ($user): ?>
                          <small class="text-muted">Signed in as: <strong><?php echo htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8'); ?></strong></small>
                        <?php else: ?>
                          <a class="btn btn-primary btn-sm" href="login.php">Login</a>
                        <?php endif; ?>
                    </div>
                    <?php if ($user): ?>
                      <div>
                        <a class="btn btn-danger btn-sm" href="logout.php">Logout</a> 
                        <a class="btn btn-outline-warning btn-sm" href="dashboard.php">Kembali</a> 
                      </div>
                    <?php endif; ?>
                </div>

                <h2 class="mb-1"><?php echo htmlspecialchars($post['title'] ?? '—', ENT_QUOTES, 'UTF-8'); ?></h2>
                <div class="post-meta mb-3">
                    <?php echo htmlspecialchars($post['created_at'] ?? '', ENT_QUOTES, 'UTF-8'); ?> 
                    <?php if (!empty($post['author'])): ?> &nbsp;oleh <?php echo htmlspecialchars($post['author'], ENT_QUOTES, 'UTF-8'); ?> <?php endif; ?>
                </div>

                <div class="mb-4"><?php echo nl2br(htmlspecialchars($post['body'] ?? '', ENT_QUOTES, 'UTF-8')); ?></div>

                <hr class="mt-4 mb-4">

                <h4>Tulis Komentar</h4>

                <?php if ($err): ?>
                  <div class="alert alert-danger"><?php echo htmlspecialchars($err, ENT_QUOTES, 'UTF-8'); ?></div>
                <?php endif; ?>
                <?php if ($msg): ?>
                  <div class="alert alert-success"><?php echo htmlspecialchars($msg, ENT_QUOTES, 'UTF-8'); ?></div>
                <?php endif; ?>

                <?php if ($user): ?>
                  <form method="post" class="mb-3" autocomplete="off">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
                    <input type="hidden" name="action" value="post_comment">
                    <div class="mb-3">
                      <textarea name="comment" rows="5" class="form-control" placeholder="Tulis komentar Anda (maks 2000 karakter)"><?php echo isset($_POST['comment']) ? htmlspecialchars($_POST['comment'], ENT_QUOTES, 'UTF-8') : ''; ?></textarea>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                      <div class="note"><i class="bi bi-shield-lock"></i> HTML dalam komentar akan di-*escape* (aman).</div>
                      <div>
                        <button type="submit" class="btn btn-primary"><i class="bi bi-send-fill"></i> Kirim Komentar</button>
                      </div>
                    </div>
                  </form>
                <?php else: ?>
                  <p>Anda harus <a href="login.php" style="color: var(--color-primary); text-decoration: none;">login</a> untuk mengirim komentar.</p>
                <?php endif; ?>

                <hr class="mt-4 mb-4">

                <h4 class="mb-3">Komentar</h4>

                <?php if (empty($comments)): ?>
                  <p class="text-muted">Belum ada komentar.</p>
                <?php else: ?>
                  <div class="mt-3">
                    <?php foreach ($comments as $c): ?>
                      <?php 
                        $username = htmlspecialchars($c['username'] ?? 'Guest', ENT_QUOTES, 'UTF-8');
                        $avatar = get_avatar_data($username);
                        // Formatting timestamp agar terlihat seperti gambar
                        $display_timestamp = date('Y-m-d H:i:s', strtotime($c['created_at'] ?? ''));
                      ?>
                      
                      <div class="comment-card mb-3">
                        <div class="comment-avatar <?php echo $avatar['class']; ?>">
                          <?php echo $avatar['initial']; ?>
                        </div>
                        
                        <div class="comment-content">
                          
                          <div class="d-flex justify-content-between align-items-start">
                            
                            <div class="d-flex align-items-center">
                              <strong class="username me-3"><?php echo $username; ?></strong>
                              <span class="timestamp"><?php echo $display_timestamp; ?></span>
                            </div>

                            <?php if ($user && (int)$c['user_id'] === (int)$user['id']): ?>
                              <div>
                                <form method="post" style="display:inline-block;" onsubmit="return confirm('Hapus komentar ini?');">
                                  <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
                                  <input type="hidden" name="action" value="delete_comment">
                                  <input type="hidden" name="delete_comment_id" value="<?php echo (int)$c['id']; ?>">
                                  <button type="submit" class="btn btn-danger"><i class="bi bi-trash-fill"></i> Hapus</button>
                                </form>
                              </div>
                            <?php endif; ?>
                            
                          </div>

                          <div class="escaped-comment">
                            <?php echo esc_nl((string)$c['comment']); ?>
                          </div>
                        </div>
                      </div>
                    <?php endforeach; ?>
                  </div>
                <?php endif; ?>

            </div>

            <div class="card-footer text-muted small">
                Halaman ini aman: komentar di-escape untuk mencegah XSS. Gunakan versi vulnerable (`post_vul.php`) untuk demo kontrastar.
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>