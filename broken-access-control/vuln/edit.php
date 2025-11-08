<?php
// vuln/edit.php
require_once __DIR__ . '/../config.php';
if (empty($_SESSION['user'])) header('Location: ../login.php');

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) { http_response_code(400); exit('Bad Request'); }

// Load (no ownership check)
$row = $pdo->query("SELECT * FROM items_vuln WHERE id = $id")->fetch(PDO::FETCH_ASSOC);
if (!$row) { http_response_code(404); exit('Not found'); }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $content = $_POST['content'] ?? '';
    // VULNERABLE: direct concatenation
    $sql = "UPDATE items_vuln SET title = '{$title}', content = '{$content}' WHERE id = $id";
    $pdo->exec($sql);
    header('Location: list.php'); exit;
}
?>
<!doctype html><html><body>
<h2>Edit VULN Item (ID <?= $row['id'] ?>)</h2>
<form method="post">
  <input name="title" value="<?= htmlspecialchars($row['title']) ?>" style="width:300px"><br><br>
  <textarea name="content" rows=6 cols=60><?= htmlspecialchars($row['content']) ?></textarea><br><br>
  <button>Save</button>
</form>
<p><a href="list.php">Back</a></p>
</body></html>
