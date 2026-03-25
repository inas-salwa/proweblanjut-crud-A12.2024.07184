<?php $current = basename($_SERVER['PHP_SELF']); ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Inventaris Barang</title>
    <link rel="stylesheet" href="/inventory/assets/css/style.css">
</head>
<body>
    <nav class="navbar">
        <a href="/inventory/index.php" class="brand">Inventaris Barang</a>
        <nav style="display:flex;align-items:center;gap:1rem;">
            <a href="/inventory/index.php">Beranda</a>
            <a href="/inventory/barang/index.php">Data Barang</a>
            <a href="/inventory/barang/create.php">Tambah Barang</a>
            <span style="color:rgba(255,255,255,.7);font-size:.85rem;">
                <?= htmlspecialchars($_SESSION['nama'] ?? '') ?>
            </span>
            <a href="/inventory/logout.php" style="background:rgba(255,255,255,.2);padding:.3rem .8rem;border-radius:6px;color:#fff;font-size:.85rem;">Logout</a>
        </nav>
    </nav>
    <div class="container"></div>
</body>
</html>