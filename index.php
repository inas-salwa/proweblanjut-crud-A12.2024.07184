<?php
require_once __DIR__ . '/includes/config.php';
$total  = $conn->query("SELECT COUNT(*) FROM barang")->fetch_row()[0];
$result = $conn->query("SELECT SUM(jumlah) FROM barang")->fetch_row()[0];
$stok   = $result ?? 0;
$recent = $conn->query("SELECT * FROM barang ORDER BY id_barang DESC LIMIT 5");
include __DIR__ . '/includes/header.php';
?>

<div class="card">
    <div class="card-title">📊 Dashboard</div>
    <p>Total Jenis Barang: <strong><?= $total ?></strong> &nbsp;|&nbsp; Total Stok: <strong><?= number_format($stok) ?></strong></p>
</div>

<div class="card">
    <div class="card-title">📋 Barang Terbaru</div>
    <table>
        <thead><tr><th>No</th><th>Kode</th><th>Nama Barang</th><th>Stok</th><th>Aksi</th></tr></thead>
        <tbody>
        <?php if($recent->num_rows > 0): $no=1; while($row=$recent->fetch_assoc()): ?>
        <tr>
            <td><?= $no++ ?></td>
            <td><?= htmlspecialchars($row['kode_barang']) ?></td>
            <td><?= htmlspecialchars($row['nama_barang']) ?></td>
            <td><?= $row['jumlah'] ?> <?= $row['satuan'] ?></td>
            <td><a href="<?= BASE_URL ?>barang/index.php" class="btn btn-primary btn-sm">Lihat Semua</a></td>
        </tr>
        <?php endwhile; else: ?>
        <tr><td colspan="5" style="text-align:center;color:#94a3b8;padding:1.5rem;">Belum ada data barang.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>