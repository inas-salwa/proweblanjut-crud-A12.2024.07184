<?php
define('DB_HOST', 'localhost');
define('DB_USER','root');
define('DB_PASS', '');
define('DB_NAME','db_inventaris');

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

IF ($conn->connect_error) {
    die("koneksi gagal : " . $conn->connect_error);
}

$conn->set_charset("utf8");

define('UPLOAD_DIR', 'C:/xampp/htdocs/INVENTORY/uploads/');
define('BASE_URL', 'http://localhost/inventory/');