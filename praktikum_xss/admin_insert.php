<?php
require 'auth_simple.php';
$pdo = pdo_connect();

if($_SERVER['REQUEST_METHOD']==='POST'){
    $name = $_POST['name'] ?? 'LabUser';
    $comment = $_POST['comment'] ?? '';
    // insert as lab helper (user_id null)
    $stmt = $pdo->prepare("INSERT INTO comments (user_id, post_id, comment) VALUES (NULL, :pid, :c)");
    $stmt->execute([':pid'=> (int)($_POST['post_id'] ?? 1), ':c'=>$comment]);
    header('Location: admin_insert.php');
    exit;
}

?>
<!doctype html><html><body>
<h3>Admin Insert (lab only)</h3>
<form method="post">
Post ID: <input name="post_id" value="1"><br>
Comment:<br>
<textarea name="comment" rows="6" cols="60">&lt;img src=x onerror=alert('XSS1')&gt;</textarea><br>
<button>Insert</button>
</form>
<p>Use only in lab environment.</p>
</body></html>
