<?php
// logout.php
session_start();
$_SESSION = [];
session_destroy();
header('Location: login_safe.php');
exit;
