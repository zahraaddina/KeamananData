<?php
// safe/view.php
require_once __DIR__ . '/../config.php';
if (empty($_SESSION['user'])) header('Location: ../login.php');

$uuid = $_GET['u'] ?? '';
// If token not provided in GET, show form to ask token
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $uuid = $_POST['u'] ?? '';
    $token = $_POST['token'] ?? '';
} else {
    $token = $_GET['t'] ?? '';
}

if (!$uuid) { http_response_code(400); exit('Missing uuid'); }

$stmt = $pdo->prepare("SELECT * FROM items_safe WHERE uuid = :u LIMIT 1");
$stmt->execute([':u'=>$uuid]);
$item = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$item) { http_response_code(404); exit('Not found'); }

// Ownership check first (defense-in-depth)
if ($item['user_id'] != $_SESSION['user']['id']) {
    http_response_code(403); exit('Forbidden: not owner');
}

// If token not yet provided, ask user to input token (or provide via ?t=)
if (!$token) {
    // show simple form
    ?>
    <!doctype html><html><body>
    <h2>Enter access token for UUID <?=htmlspecialchars($uuid)?></h2>
    <form method="post">
      <input type="hidden" name="u" value="<?=htmlspecialchars($uuid)?>">
      <input name="token" placeholder="paste token here" style="width:400px"><br><br>
      <button>View</button>
    </form>
    <p><a href="list.php">Back</a></p>
    </body></html>
    <?php
    exit;
}

// Verify token (compare hash)
$provided_hash = token_hash($token);
if (!hash_equals($item['token_hash'], $provided_hash)) {
    http_response_code(403); exit('Invalid token');
}

// Passed checks — show safe content escaped
?>
<!doctype html><html><body>
<h2><?=htmlspecialchars($item['title'])?></h2>
<p><?=nl2br(htmlspecialchars($item['content']))?></p>
<p><i>UUID: <?=htmlspecialchars($item['uuid'])?></i></p>
<p><a href="list.php">Back</a></p>
</body></html>
