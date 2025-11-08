<?php
session_start();

// Hapus semua session
session_unset();
session_destroy();

// Redirect ke halaman login (atau index.php sesuai kebutuhan)
header("Location: login.php");
exit;

?>