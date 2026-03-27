<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/includes/config.php';

if (isset($_SESSION['username'])) {
    header('Location: /inventory/index.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($username === '' || $password === '') {
        $error = 'Username dan Password wajib diisi';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

       if ($user && $password === $user['password']) {
            $_SESSION['user_id'] =  $user['id_user'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['nama'] = $user['nama'];

            header('Location: /inventory/index.php');
            exit;
        } else {
            $error = 'Username atau password salah!';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Inventaris Barang</title>
    <link rel="stylesheet" href="/inventory/assets/css/style.css">
    <style>
         body { display:flex; align-items:center; justify-content:center; min-height:100vh; background:#f1f5f9; }
        .login-box { background:#fff; border-radius:12px; box-shadow:0 4px 24px rgba(0,0,0,.1); padding:2.5rem; width:100%; max-width:400px; }
        .login-title { text-align:center; font-size:1.4rem; font-weight:800; color:#2563eb; margin-bottom:.3rem; }
        .login-sub { text-align:center; color:#64748b; font-size:.85rem; margin-bottom:1.8rem; }
    </style>
</head>
<body>
<div class="login-box">
    <div class="login-sub">Silahkan Login untuk melanjutkan</div>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label class="form-label">Username</label>
           <input type="text" name="username" class="form-control" placeholder="Masukkan username" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" placeholder="Masukan Password">
        </div>
        <button type="submit" class="btn btn-primary" style="width:100%;margin-top:.5rem;">Login</button>
    </form>
</div>
</body>
</html>