<?php
require_once __DIR__ . '/../includes/config.php';

$id = intval($_GET['id'] ?? 0);
if (!$id) { header('Location: ' . BASE_URL . 'barang/index.php'); exit; }

$row = $conn->query("SELECT * FROM barang WHERE id_barang=$id")->fetch_assoc();
if (!$row) { header('Location: ' . BASE_URL / 'barang/index.php'); exit;}

if ($row['foto'] && file_exists(UPLOAD_DIR . $row['foto'])) {
    unlink(UPLOAD_DIR . $row['foto']);
}

if ($conn->query("DELETE FROM barang WHERE id_barang=$id")) {
    header('Location: ' . BASE_URL . 'barang/index.php?del=success');
} else {
    header('Location: ' . BASE_URL . 'barang/index.php?del=error');
}
exit;