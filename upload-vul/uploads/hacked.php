<?php session_start(); if (!isset($_SESSION['user_id'])) header('Location: index.php'); ?>
<!DOCTYPE html>
<html>
<head><title>Diambil Alih!</title></head>
<body onload="alert('⚠️ WEB SUDAH DIAMBIL ALIH!')">
    <h1 style="color:red;">🚨 SISTEM TELAH DIKUASAI! 🚨</h1>
</body>
</html>
