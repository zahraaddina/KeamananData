<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? AND password = ?");
    $stmt->execute([$username, $password]);
    $user = $stmt->fetch();

    if ($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $username;
        header('Location: dashboard.php');
        exit();
    } else {
        $error = "Username atau password salah!";
    }
}
?>


<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Login - Demo App</title>
    <link rel="stylesheet" href="css/register.css?v=<?php echo time(); ?>">
</head>
<body>
    <div class="container">
        <div class="form-box">
            <h2>Welcome back!</h2>
            <p>Sign in to continue to your dashboard</p>
            <?php if (isset($error)): ?>
                <div class="message"><p class="error"><?= htmlspecialchars($error) ?></p></div>
            <?php endif; ?>

            <form method="post">
                <div class="input-box">
                    <input type="text" name="username" placeholder="Name" required>
                </div>

                <div class="input-box">
                    <input type="password" name="password" placeholder="Password" required>
                </div>

                <button type="submit">Sign In</button>
                <p class="login-link">Belum punya akun? <a href="register.php">Daftar</a></p>
            </form>
        </div>

        <div class="right-box">
            <h3>Glad to see You!</h3>
        </div>
    </div>
</body>
</html>