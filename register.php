<?php
if (session_status()== PHP_SESSION_NONE) session_start();
require_once __DIR__ . 'includes/config.php';

if(isset($_SESSION['username'])) {
    header('location: /inventory/index.php');
    exit;
}

$errors = [];
$success = '';

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $konfirm = trim($_POST['konfirm_password'] ?? '');

    if ($nama === '') $errors[] = 'Nama lengkap wajib diisi.';
    if ($username === '') $errors[] = 'Username wajib diisi.';
    if ($password === '') $errors[] = 'Password wajib diisi.';
    if (strlen($password) < 6) $errors[] = 'Password minimal 6 karakter.';
    if ($password !== $konfirm) $errors[] = 'Konfirmasi password tidak cocok.';

    if (empty($errors)) {
        $cek = $pdo->prepare("SELECT id_user FROM users WHERE username = ?");
        $cek->execute([$username]);
        if ($cek->fetch()) {
            $errors[] = 'Username sudah digunakan, pilih username lain.';
        }
    }

    if(empty($errors)) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (nama, username, password) VALUES (?, ?, ?)");
        if ($stmt->execute([$nama, $username, $hash])){
            $success = 'Registrasi berhasil! silahkan login.';
        } else {
            $errors[] = 'Gagal menyimpan data, coba lagi.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi - Inventaris Barang</title>
    <link rel="stylesheet" href="/inventory/assets/css/style.css">
    <style>
        body { display:flex; align-items:center; justify-content:center; min-height:100vh; background:#f1f5f9; }
        .login-box { background:#fff; border-radius:12px; box-shadow:0 4px 24px rgba(0,0,0,.1); padding:2.5rem; width:100%; max-width:420px; }
        .login-title { text-align:center; font-size:1.4rem; font-weight:800; color:#2563eb; margin-bottom:.3rem; }
        .login-sub { text-align:center; color:#64748b; font-size:.85rem; margin-bottom:1.8rem; }
        .link-login { text-align:center; margin-top:1rem; font-size:.85rem; color:#64748b; }
        .link-login a { color:#2563eb; text-decoration:none; font-weight:600; }
    </style>
</head>
<body>
<div class="login-box">
    <div class="login-title">Inventaris Barang</div>
    <div class="login-sub">Buat akun baru</div>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <?php foreach ($errors as $e): ?>&bull; <?= htmlspecialchars($e) ?><br><?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <?php if (!$success): ?>
    <form method="POST">
        <div class="form-group">
            <label class="form-label">Nama Lengkap</label>
            <input type="text" name="nama" class="form-control" placeholder="Masukkan nama lengkap" value="<?= htmlspecialchars($_POST['nama'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label class="form-label">Username</label>
            <input type="text" name="username" class="form-control" placeholder="Masukkan username" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" placeholder="Minimal 6 karakter">
        </div>
        <div class="form-group">
            <label class="form-label">Konfirmasi Password</label>
            <input type="password" name="konfirm_password" class="form-control" placeholder="Ulangi password">
        </div>
        <button type="submit" class="btn btn-success" style="width:100%;margin-top:.5rem;">📝 Daftar</button>
    </form>
    <?php endif; ?>

    <div class="link-login">
        Sudah punya akun? <a href="/inventory/login.php">Login di sini</a>
    </div>
</div>
</body>
</html>