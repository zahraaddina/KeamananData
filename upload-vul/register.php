<?php
require 'config.php';

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    try {
        $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
        $stmt->execute([$username, $password]);
        $message = "<p class='success'>Registrasi berhasil! <a href='index.php'>Login</a></p>";
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            $message = "<p class='error'>Username sudah digunakan!</p>";
        } else {
            $message = "<p class='error'>Terjadi kesalahan.</p>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Register - Demo App</title>
    <link rel="stylesheet" href="css/register.css?v=<?php echo time(); ?>">
</head>
</body>
<body>
    <div class="container">
        <div class="form-box">
            <h2>Hello, friend!</h2>
            <p>Create your account to start uploading files</p>
            <?php if ($message): ?>
                <div class="message"><?= $message ?></div>
            <?php endif; ?>
            <form method="post">
                <div class="input-box">
                    <input type="text" name="username" placeholder="Name" required>
                </div>

                <div class="input-box">
                    <input type="password" name="password" placeholder="Password" required>
                </div>

                <button type="submit">Create Account</button>
                <p class="login-link">Already have an account? <a href="index.php">Sign in</a></p>
            </form>
        </div>

        <div class="right-box">
            <h3>Selamat Datang!</h3>
            <p>Ini adalah praktikum uji kerentanan website</p>
        </div>
    </div>
</body>
</html>