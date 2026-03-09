<?php
require_once __DIR__ . '/../includes/config.php';
$result = $conn->query("SELECT * FROM barang ORDER BY id_barang DESC");
include __DIR__ . '/../includes/header.php';
?>

<?php if(isset($_GET['msg'])): ?>
    <div class="alert alert-<?= $_GET['msg']==='success'?'success':'danger' ?>">
        <?= $_GET['msg']==='success' ? 'Data berhasil disimpan.' : 'Terjadi kesalahan.' ?>
    </div>
<?php endif; ?>
<?php if(isset($_GET['del'])): ?>
    <div class="alert alert-<?= $_GET['del']==='success'?'success':'danger' ?>">
        <?= $_GET['del']==='success' ? 'Data berhasil dihapus.' : 'Gagal menghapus.' ?>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-title">Data Barang</div>
    <div style="margin-bottom:1rem;">
        <a href="<?= BASE_URL ?>barang/create.php" class="btn btn-success">+ Tambah Barang</a>
    </div>
    <table>
        <thead>
            <tr>
                <th>No</th><th>Foto</th><th>Kode</th><th>Nama Barang</th>
                <th>Satuan</th><th>Harga Jual</th><th>Stok</th><th>Aksi</th>
            </tr>
        </thead>
        <tbody>
        <?php $no=1; while($row=$result->fetch_assoc()): ?>
        <tr>
            <td><?= $no++ ?></td>
            <td>
                <?php if($row['foto'] && file_exists(UPLOAD_DIR.$row['foto'])): ?>
                    <img src="<?= BASE_URL ?>uploads/<?= $row['foto'] ?>" class="photo-thumb">
                <?php else: ?>
                    <div style="width:50px;height:50px;background:#f1f5f9;border-radius:6px;display:flex;align-items:center;"></div>
                <?php endif; ?>
            </td>
            <td><?= htmlspecialchars($row['kode_barang']) ?></td>
            <td><?= htmlspecialchars($row['nama_barang']) ?></td>
            <td><?= htmlspecialchars($row['satuan']) ?></td>
            <td>Rp <?= number_format($row['harga_jual'],0,',','.') ?></td>
            <td><?= number_format($row['jumlah']) ?></td>
            <td style="white-space:nowrap;">
                <a href="<?= BASE_URL ?>barang/edit.php?id=<?= $row['id_barang'] ?>" class="btn btn-warning btn-sm">Edit</a>
                <a href="<?= BASE_URL ?>barang/delete.php?id=<?= $row['id_barang'] ?>" class="btn btn-danger btn-sm"
                   onclick="return confirm('Yakin hapus barang ini?')">Hapus</a>
            </td>
        </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>