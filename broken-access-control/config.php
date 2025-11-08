<?php
session_start();
ini_set('display_errors',1);
error_reporting(E_ALL);

define('DB_HOST','127.0.0.1');
define('DB_PORT','3307');       // <--- port MySQL kamu
define('DB_NAME','lab_security');
define('DB_USER','root');
define('DB_PASS','');          // isi jika punya password

try {
    $dsn = "mysql:host=".DB_HOST.";port=".DB_PORT.";dbname=".DB_NAME.";charset=utf8mb4";
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (Exception $e) {
    die("Database Error: ".$e->getMessage());
}

/* ==== Helper Functions ==== */
function csrf_token() {
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(16));
    }
    return $_SESSION['csrf'];
}
function check_csrf($t) {
    return isset($_SESSION['csrf']) && hash_equals($_SESSION['csrf'], $t);
}
function uuid4() {
    $data = random_bytes(16);
    $data[6] = chr((ord($data[6]) & 0x0f) | 0x40);
    $data[8] = chr((ord($data[8]) & 0x3f) | 0x80);
    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data),4));
}
function token_generate() {
    return bin2hex(random_bytes(32));
}
function token_hash($token) {
    return hash('sha256', $token);
}

?>