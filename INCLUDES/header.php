<?php $current = basename($_SERVER['PHP_SELF']); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Inventaris Barang</title>
    <link rel="stylesheet" href="/inventory/assets/css/style.css">
</head>
<body>
    <nav class="navbar">
        <a href="/inventory/index.php" class="brand">Inventaris Barang</a>
        <nav>
            <a href="/inventory/index.php">Beranda</a>
            <a href="/inventory/barang/index.php">Data Barang</a>
            <a href="/inventory/barang/create.php">Tambah Barang</a>
        </nav>
    </nav>
    <div class="container"></div>
</body>
</html>