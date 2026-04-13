<?php
require_once 'C:/xampp/htdocs/INVENTORY/includes/auth.php';
require_once 'C:/xampp/htdocs/INVENTORY/includes/config.php';

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
    if (!preg_match('/^[a-zA-Z\s]+$/', $data['nama_barang'])) $errors[] = 'Nama barang hanya boleh berisi huruf.';
    if ($data['satuan']      === '') $errors[] = 'Satuan wajib diisi.';
    if ($data['harga_beli']  === '') $errors[] = 'Harga beli wajib diisi.';
    if ($data['harga_jual']  === '') $errors[] = 'Harga jual wajib diisi.';
    if ($data['jumlah']      === '') $errors[] = 'Jumlah wajib diisi.';

    $foto_thumb = '';

    if (!isset($_FILES['foto']) || $_FILES['foto']['error'] !== UPLOAD_ERR_OK) {
        $errors[] = 'Foto barang wajib diupload.';
    } else {
        $allowed_mime = ['image/jpeg', 'image/png'];
        $mime         = mime_content_type($_FILES['foto']['tmp_name']);
        $max_size     = 2 * 1024 * 1024; // 2MB

        if (!in_array($mime, $allowed_mime)) $errors[] = 'Format foto tidak valid. Gunakan JPG atau PNG.';
        if ($_FILES['foto']['size'] > $max_size) $errors[] = 'Ukuran foto maksimal 2MB.';

        if (empty($errors)) {
            $dir_original = 'C:/xampp/htdocs/INVENTORY/uploads/original/';
            $dir_thumb    = 'C:/xampp/htdocs/INVENTORY/uploads/thumbs/';

            if (!is_dir($dir_original)) mkdir($dir_original, 0777, true);
            if (!is_dir($dir_thumb))    mkdir($dir_thumb,    0777, true);

            $filename      = time() . '_' . uniqid() . '.jpg';
            $path_original = $dir_original . $filename;
            $path_thumb    = $dir_thumb    . 'thumb_' . $filename;

            list($width, $height) = getimagesize($_FILES['foto']['tmp_name']);

            if ($mime === 'image/jpeg') {
                $src = imagecreatefromjpeg($_FILES['foto']['tmp_name']);
            } else {
                $src = imagecreatefrompng($_FILES['foto']['tmp_name']);
            }

            $max_w = 1024; $max_h = 768;
            if ($width > $max_w || $height > $max_h) {
                $scale   = min($max_w / $width, $max_h / $height);
                $new_w   = floor($width  * $scale);
                $new_h   = floor($height * $scale);
                $resized = imagecreatetruecolor($new_w, $new_h);
                imagecopyresampled($resized, $src, 0, 0, 0, 0, $new_w, $new_h, $width, $height);
                imagejpeg($resized, $path_original, 90);
                imagedestroy($resized);
                $width = $new_w; $height = $new_h;
                $src = imagecreatefromjpeg($path_original);
            } else {
                imagejpeg($src, $path_original, 90);
            }

            $thumb = imagecreatetruecolor(200, 200);
            if ($width > $height) {
                $crop_x = ($width - $height) / 2;
                $crop_y = 0;
                $crop_s = $height;
            } else {
                $crop_x = 0;
                $crop_y = ($height - $width) / 2;
                $crop_s = $width;
            }
            imagecopyresampled($thumb, $src, 0, 0, $crop_x, $crop_y, 200, 200, $crop_s, $crop_s);
            imagejpeg($thumb, $path_thumb, 90);

            imagedestroy($src);
            imagedestroy($thumb);

            $foto_thumb = 'uploads/thumbs/thumb_' . $filename;
        }
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("INSERT INTO barang
            (kode_barang,nama_barang,satuan,harga_beli,harga_jual,jumlah,tanggal_masuk,keterangan,foto)
            VALUES (?,?,?,?,?,?,?,?,?)");
        if ($stmt->execute([
            $data['kode_barang'], $data['nama_barang'], $data['satuan'],
            $data['harga_beli'],  $data['harga_jual'],  $data['jumlah'],
            $data['tanggal_masuk'], $data['keterangan'], $foto_thumb
        ])) {
            header('Location: ' . BASE_URL . 'barang/index.php?msg=success');
            exit;
        } else {
            $errors[] = 'Gagal menyimpan data.';
        }
    }
}

include 'C:/xampp/htdocs/INVENTORY/includes/header.php';
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
                <small style="color:#64748b;">Hanya boleh huruf</small>
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
                <small style="color:#64748b;">Format: JPG/PNG, maks 2MB. Thumbnail otomatis dibuat.</small><br>
                <img id="preview" src="#" class="photo-preview" style="display:none;" alt="preview">
            </div>
        </div>
        <div class="form-group">
            <label class="form-label">Keterangan</label>
            <textarea name="keterangan" class="form-control" rows="3"><?= htmlspecialchars($data['keterangan']) ?></textarea>
        </div>
        <div style="display:flex;gap:.8rem;">
            <button type="submit" class="btn btn-success">💾 Simpan</button>
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

<?php include 'C:/xampp/htdocs/INVENTORY/includes/footer.php'; ?>