<?php
require_once __DIR__ . '/../includes/config.php';

$id  = intval($_GET['id'] ?? 0);
if (!$id) { header('Location: ' . BASE_URL . 'barang/index.php'); exit; }

$stmt = $pdo->prepare("SELECT * FROM barang WHERE id_barang = ?");
$stmt->execute([$id]);
$row = $stmt->fetch();
if (!$row) { header('Location: ' . BASE_URL . 'barang/index.php'); exit; }

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kode_barang   = trim($_POST['kode_barang'] ?? '');
    $nama_barang   = trim($_POST['nama_barang'] ?? '');
    $satuan        = trim($_POST['satuan'] ?? '');
    $harga_beli    = trim($_POST['harga_beli'] ?? '');
    $harga_jual    = trim($_POST['harga_jual'] ?? '');
    $jumlah        = trim($_POST['jumlah'] ?? '');
    $tanggal_masuk = trim($_POST['tanggal_masuk'] ?? '');
    $keterangan    = trim($_POST['keterangan'] ?? '');

    if ($kode_barang === '') $errors[] = 'Kode barang wajib diisi.';
    if ($nama_barang === '') $errors[] = 'Nama barang wajib diisi.';
    if ($satuan      === '') $errors[] = 'Satuan wajib diisi.';
    if ($harga_beli  === '') $errors[] = 'Harga beli wajib diisi.';
    if ($harga_jual  === '') $errors[] = 'Harga jual wajib diisi.';
    if ($jumlah      === '') $errors[] = 'Jumlah wajib diisi.';

    $foto = $row['foto'];
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $allowed = ['image/jpeg','image/png','image/gif','image/webp'];
        if (!in_array($_FILES['foto']['type'], $allowed)) $errors[] = 'Format foto tidak valid.';
        if ($_FILES['foto']['size'] > 2*1024*1024) $errors[] = 'Ukuran foto maks 2MB.';
        if (empty($errors)) {
            if ($row['foto'] && file_exists(UPLOAD_DIR . $row['foto'])) {
                unlink(UPLOAD_DIR . $row['foto']);
            }
            $ext  = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
            $foto = uniqid('brg_') . '.' . strtolower($ext);
            if (!move_uploaded_file($_FILES['foto']['tmp_name'], UPLOAD_DIR . $foto)) {
                $errors[] = 'Gagal mengupload foto.';
                $foto = $row['foto'];
            }
        }
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("UPDATE barang SET
            kode_barang=?, nama_barang=?, satuan=?, harga_beli=?,
            harga_jual=?, jumlah=?, tanggal_masuk=?, keterangan=?, foto=?
            WHERE id_barang=?");
        if ($stmt->execute([
            $kode_barang, $nama_barang, $satuan,
            $harga_beli, $harga_jual, $jumlah,
            $tanggal_masuk, $keterangan, $foto, $id
        ])) {
            header('Location: ' . BASE_URL . 'barang/index.php?msg=success');
            exit;
        } else {
            $errors[] = 'Gagal update data.';
        }
    }
}

include __DIR__ . '/../includes/header.php';
?>

<div class="card">
    <div class="card-title">✏️ Edit Barang</div>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <?php foreach ($errors as $e): ?>&bull; <?= htmlspecialchars($e) ?><br><?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Kode Barang *</label>
                <input type="text" name="kode_barang" class="form-control" value="<?= htmlspecialchars($row['kode_barang']) ?>">
            </div>
            <div class="form-group">
                <label class="form-label">Nama Barang *</label>
                <input type="text" name="nama_barang" class="form-control" value="<?= htmlspecialchars($row['nama_barang']) ?>">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Satuan *</label>
                <input type="text" name="satuan" class="form-control" value="<?= htmlspecialchars($row['satuan']) ?>">
            </div>
            <div class="form-group">
                <label class="form-label">Jumlah Stok *</label>
                <input type="number" name="jumlah" class="form-control" value="<?= htmlspecialchars($row['jumlah']) ?>" min="0">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Harga Beli (Rp) *</label>
                <input type="number" name="harga_beli" class="form-control" value="<?= htmlspecialchars($row['harga_beli']) ?>" min="0">
            </div>
            <div class="form-group">
                <label class="form-label">Harga Jual (Rp) *</label>
                <input type="number" name="harga_jual" class="form-control" value="<?= htmlspecialchars($row['harga_jual']) ?>" min="0">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Tanggal Masuk</label>
                <input type="date" name="tanggal_masuk" class="form-control" value="<?= htmlspecialchars($row['tanggal_masuk']) ?>">
            </div>
            <div class="form-group">
                <label class="form-label">Foto Barang (kosongkan jika tidak diganti)</label>
                <input type="file" name="foto" class="form-control" accept="image/*" onchange="previewFoto(this)">
                <small style="color:#64748b;">Format: JPG/PNG, maks 2MB</small><br>
                <?php if ($row['foto'] && file_exists(UPLOAD_DIR . $row['foto'])): ?>
                    <img id="preview" src="<?= BASE_URL ?>uploads/<?= $row['foto'] ?>" class="photo-preview" alt="foto">
                <?php else: ?>
                    <img id="preview" src="#" class="photo-preview" style="display:none;" alt="preview">
                <?php endif; ?>
            </div>
        </div>
        <div class="form-group">
            <label class="form-label">Keterangan</label>
            <textarea name="keterangan" class="form-control" rows="3"><?= htmlspecialchars($row['keterangan']) ?></textarea>
        </div>
        <div style="display:flex;gap:.8rem;">
            <button type="submit" class="btn btn-warning">💾 Update</button>
            <a href="<?= BASE_URL ?>barang/index.php" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>

<script>
function previewFoto(input) {
    const preview = document.getElementById('preview');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => { preview.src = e.target.result; preview.style.display = 'block'; }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>