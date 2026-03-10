<?php
define('DB_HOST', 'localhost');
define('DB_USER','root');
define('DB_PASS', '');
define('DB_NAME','db_inventaris');

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8",
        DB_USER,
        DB_PASS
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Koneksi gagal: " . $e->getMessage());
}

define('UPLOAD_DIR', 'C:/xampp/htdocs/INVENTORY/uploads/');
define('BASE_URL', 'http://localhost/inventory/');
?>