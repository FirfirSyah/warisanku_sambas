<?php
require_once __DIR__ . '/../includes/auth.php';

if (current_user() !== null) {
    redirect('admin/dashboard.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    // Pembatasan percobaan login sederhana (per sesi browser)
    if (($_SESSION['kunci_sampai'] ?? 0) > time()) {
        $error = 'Terlalu banyak percobaan. Coba lagi beberapa saat lagi.';
    } else {
        $username = trim($_POST['username'] ?? '');
        $password = (string) ($_POST['password'] ?? '');

        $stmt = db()->prepare('SELECT * FROM users WHERE username = :u AND is_active = 1');
        $stmt->execute([':u' => $username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            unset($_SESSION['gagal'], $_SESSION['kunci_sampai']);
            login_user($user);
            redirect('admin/dashboard.php');
        }

        // Pesan sengaja dibuat umum supaya tidak membocorkan username mana yang ada
        $error = 'Username atau password salah.';
        $_SESSION['gagal'] = ($_SESSION['gagal'] ?? 0) + 1;
        if ($_SESSION['gagal'] >= 5) {
            $_SESSION['kunci_sampai'] = time() + 60;
            $_SESSION['gagal'] = 0;
        }
    }
}

$flash = get_flash();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="robots" content="noindex, nofollow">
  <title>Login Admin | <?= e(SITE_NAME) ?></title>
  <link rel="stylesheet" href="<?= e(asset('css/style.css')) ?>">
</head>
<body class="pola-songket flex min-h-screen items-center justify-center bg-hijau p-4">
  <div class="animasi-masuk bingkai-emas w-full max-w-sm bg-gading p-6">
    <p class="text-center font-serif text-2xl font-bold text-hijau">WARISANKU <span class="text-marun">SAMBAS</span></p>
    <p class="text-center text-xs uppercase tracking-widest text-hijau/70">Login Admin</p>

    <?php if ($flash): ?>
      <p class="mt-4 rounded border px-3 py-2 text-sm <?= $flash['type'] === 'success' ? 'border-hijau bg-hijau/10' : 'border-marun bg-marun/10 text-marun' ?>"><?= e($flash['msg']) ?></p>
    <?php endif; ?>
    <?php if ($error): ?>
      <p class="mt-4 rounded border border-marun bg-marun/10 px-3 py-2 text-sm text-marun getar" role="alert"><?= e($error) ?></p>
    <?php endif; ?>

    <form method="post" class="mt-4 space-y-3">
      <?= csrf_field() ?>
      <div>
        <label for="username" class="text-sm font-semibold">Username</label>
        <input id="username" name="username" required autofocus class="mt-1 w-full rounded border border-emas px-3 py-2">
      </div>
      <div>
        <label for="password" class="text-sm font-semibold">Password</label>
        <input id="password" name="password" type="password" required class="mt-1 w-full rounded border border-emas px-3 py-2">
      </div>
      <button type="submit" class="tombol-emas w-full bg-hijau text-emas hover:bg-marun hover:text-gading">Masuk</button>
    </form>

    <p class="mt-4 text-center text-sm"><a href="<?= e(base_url('public/index.php')) ?>" class="text-marun hover:underline">&larr; Kembali ke situs</a></p>
  </div>
</body>
</html>