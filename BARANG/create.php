<?php
require_once __DIR__ . '/../includes/config.php';
$errors = [];
$data = ['kode_barang'=>'','nama_barang'=>'','satuan'=>'','harga_beli'=>'',
         'harga_jual'=>'','jumlah'=>'','tanggal_masuk'=>'','keterangan'=>''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data['kode_barang']   = trim($_POST['kode_barang'] ?? '');
    $data['nama_barang']   = trim($_POST['nama_barang'] ?? '');
    $data['satuan']        = trim($_POST['satuan'] ?? '');
    $data['harga_beli']    = trim($_POST['harga_beli'] ?? '');
    $data['harga_jual']    = trim($_POST['harga_jual'] ?? '');
    $data['jumlah']        = trim($_POST['jumlah'] ?? '');
    $data['tanggal_masuk'] = trim($_POST['tanggal_masuk'] ?? '');
    $data['keterangan']    = trim($_POST['keterangan'] ?? '');

    if ($data['kode_barang'] === '') $errors[] = 'Kode barang wajib diisi.';
    if ($data['nama_barang'] === '') $errors[] = 'Nama barang wajib diisi.';
    if ($data['satuan']      === '') $errors[] = 'Satuan wajib diisi.';
    if ($data['harga_beli']  === '') $errors[] = 'Harga beli wajib diisi.';
    if ($data['harga_jual']  === '') $errors[] = 'Harga jual wajib diisi.';
    if ($data['jumlah']      === '') $errors[] = 'Jumlah wajib diisi.';

    $foto = '';
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $allowed = ['image/jpeg','image/png','image/gif','image/webp'];
        if (!in_array($_FILES['foto']['type'], $allowed)) $errors[] = 'Format foto tidak valid.';
        if ($_FILES['foto']['size'] > 2*1024*1024) $errors[] = 'Ukuran foto maks 2MB.';
        if (empty($errors)) {
            $ext  = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
            $foto = uniqid('brg_') . '.' . strtolower($ext);
            if (!move_uploaded_file($_FILES['foto']['tmp_name'], UPLOAD_DIR . $foto)) {
                $errors[] = 'Gagal mengupload foto.';
                $foto = '';
            }
        }
    } else {
        $errors[] = 'Foto barang wajib diupload.';
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO barang
            (kode_barang,nama_barang,satuan,harga_beli,harga_jual,jumlah,tanggal_masuk,keterangan,foto)
            VALUES (?,?,?,?,?,?,?,?,?)");
        $stmt->bind_param('sssddisss',
            $data['kode_barang'],$data['nama_barang'],$data['satuan'],
            $data['harga_beli'],$data['harga_jual'],$data['jumlah'],
            $data['tanggal_masuk'],$data['keterangan'],$foto);
        if ($stmt->execute()) {
            header('Location: ' . BASE_URL . 'barang/index.php?msg=success');
            exit;
        } else {
            $errors[] = 'Gagal menyimpan: ' . $conn->error;
        }
    }
}
include __DIR__ . '/../includes/header.php';
?>

<div class="card">
    <div class="card-title">Tambah Barang Baru</div>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <?php foreach ($errors as $e): ?>&bull; <?= htmlspecialchars($e) ?><br><?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Kode Barang *</label>
                <input type="text" name="kode_barang" class="form-control" value="<?= htmlspecialchars($data['kode_barang']) ?>" placeholder="Contoh: BRG-001">
            </div>
            <div class="form-group">
                <label class="form-label">Nama Barang *</label>
                <input type="text" name="nama_barang" class="form-control" value="<?= htmlspecialchars($data['nama_barang']) ?>">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Satuan *</label>
                <input type="text" name="satuan" class="form-control" value="<?= htmlspecialchars($data['satuan']) ?>" placeholder="pcs / kg / liter">
            </div>
            <div class="form-group">
                <label class="form-label">Jumlah Stok *</label>
                <input type="number" name="jumlah" class="form-control" value="<?= htmlspecialchars($data['jumlah']) ?>" min="0">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Harga Beli (Rp) *</label>
                <input type="number" name="harga_beli" class="form-control" value="<?= htmlspecialchars($data['harga_beli']) ?>" min="0">
            </div>
            <div class="form-group">
                <label class="form-label">Harga Jual (Rp) *</label>
                <input type="number" name="harga_jual" class="form-control" value="<?= htmlspecialchars($data['harga_jual']) ?>" min="0">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Tanggal Masuk</label>
                <input type="date" name="tanggal_masuk" class="form-control" value="<?= htmlspecialchars($data['tanggal_masuk']) ?>">
            </div>
            <div class="form-group">
                <label class="form-label">Foto Barang *</label>
                <input type="file" name="foto" class="form-control" accept="image/*" onchange="previewFoto(this)">
                <small style="color:#64748b;">Format: JPG/PNG, maks 2MB</small><br>
                <img id="preview" src="#" class="photo-preview" style="display:none;" alt="preview">
            </div>
        </div>
        <div class="form-group">
            <label class="form-label">Keterangan</label>
            <textarea name="keterangan" class="form-control" rows="3"><?= htmlspecialchars($data['keterangan']) ?></textarea>
        </div>
        <div style="display:flex;gap:.8rem;">
            <button type="submit" class="btn btn-success">Simpan</button>
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