<?php if(!isset($_SESSION)) session_start(); ?>
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
            <?php if(isset($_SESSION['username'])): ?>
            <a href="/inventory/barang/index.php">Data Barang</a>
            <a href="/inventory/barang/create.php">Tambah Barang</a>
           <a href="/inventory/logout.php" style="background: #8cae91;padding:.3rem .8rem;border-radius:6px;color:#fff;font-size:.85rem;">Logout</a>
            <?php else: ?>
            <a href="/inventory/login.php" style="background: #8cae91;padding:.3rem .8rem;border-radius:6px;color:#fff;font-size:.85rem;">Login</a>
        <?php endif; ?>
        </nav>
    </nav>
    <div class="container"></div>
</body>
</html>