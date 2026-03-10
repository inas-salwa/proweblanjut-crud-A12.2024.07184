<?php
require_once __DIR__ . '/../includes/config.php';

$id = intval($_GET['id'] ?? 0);
if (!$id) { header('Location: ' . BASE_URL . 'barang/index.php'); exit; }

$stmt = $pdo->prepare("SELECT * FROM barang WHERE id_barang = ?");
$stmt->execute([$id]);
$row = $stmt->fetch();

if (!$row) { header('Location: ' . BASE_URL . 'barang/index.php?del=error'); exit; }

if ($row['foto'] && file_exists(UPLOAD_DIR . $row['foto'])) {
    unlink(UPLOAD_DIR . $row['foto']);
}

$stmt = $pdo->prepare("DELETE FROM barang WHERE id_barang = ?");
if ($stmt->execute([$id])) {
    header('Location: ' . BASE_URL . 'barang/index.php?del=success');
} else {
    header('Location: ' . BASE_URL . 'barang/index.php?del=error');
}
exit;