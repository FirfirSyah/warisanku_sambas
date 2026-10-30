<?php
require_once __DIR__ . '/../includes/auth.php';

$jumlah = (int) db()->query('SELECT COUNT(*) FROM users')->fetchColumn();
if ($jumlah > 0) {
    http_response_code(403);
    exit('Setup sudah pernah dilakukan. Hapus file admin/setup.php dari server.');
}

$errors = [];
$old = ['name' => '', 'username' => '', 'email' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    $old['name']     = trim($_POST['name'] ?? '');
    $old['username'] = trim($_POST['username'] ?? '');
    $old['email']    = trim($_POST['email'] ?? '');
    $password        = (string) ($_POST['password'] ?? '');
    $konfirmasi      = (string) ($_POST['konfirmasi'] ?? '');

    if (mb_strlen($old['name']) < 2 || mb_strlen($old['name']) > 100) {
        $errors[] = 'Nama harus 2 sampai 100 karakter.';
    }
    if (!preg_match('/^[A-Za-z0-9_]{4,30}$/', $old['username'])) {
        $errors[] = 'Username 4 sampai 30 karakter: huruf, angka, atau garis bawah.';
    }
    if (!filter_var($old['email'], FILTER_VALIDATE_EMAIL) || mb_strlen($old['email']) > 120) {
        $errors[] = 'Format email tidak valid.';
    }
    if (strlen($password) < 10) {
        $errors[] = 'Password minimal 10 karakter.';
    }
    if ($password !== $konfirmasi) {
        $errors[] = 'Konfirmasi password tidak sama.';
    }

    if (!$errors) {
        $stmt = db()->prepare(
            'INSERT INTO users (name, username, email, password_hash, role)
             VALUES (:name, :username, :email, :hash, "admin")'
        );
        $stmt->execute([
            ':name'     => $old['name'],
            ':username' => $old['username'],
            ':email'    => $old['email'],
            ':hash'     => password_hash($password, PASSWORD_DEFAULT),
        ]);
        set_flash('success', 'Akun admin dibuat. Silakan login, lalu HAPUS file admin/setup.php.');
        redirect('admin/login.php');
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="robots" content="noindex, nofollow">
  <title>Setup Admin | <?= e(SITE_NAME) ?></title>
  <link rel="stylesheet" href="<?= e(asset('css/style.css')) ?>">
</head>
<body class="pola-songket flex min-h-screen items-center justify-center bg-hijau p-4">
  <div class="animasi-masuk bingkai-emas w-full max-w-md bg-gading p-6">
    <h1 class="font-serif text-2xl font-bold">Buat Admin Pertama</h1>
    <p class="mt-1 text-sm text-hijau/80">Halaman ini hanya bisa dipakai sekali, selama belum ada pengguna.</p>

    <?php if ($errors): ?>
      <ul class="mt-4 list-disc rounded border border-marun bg-marun/10 py-2 pl-8 pr-3 text-sm text-marun">
        <?php foreach ($errors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?>
      </ul>
    <?php endif; ?>

    <form method="post" class="mt-4 space-y-3" autocomplete="off">
      <?= csrf_field() ?>
      <div>
        <label for="name" class="text-sm font-semibold">Nama lengkap</label>
        <input id="name" name="name" required value="<?= e($old['name']) ?>" class="mt-1 w-full rounded border border-emas px-3 py-2">
      </div>
      <div>
        <label for="username" class="text-sm font-semibold">Username</label>
        <input id="username" name="username" required value="<?= e($old['username']) ?>" class="mt-1 w-full rounded border border-emas px-3 py-2">
      </div>
      <div>
        <label for="email" class="text-sm font-semibold">Email</label>
        <input id="email" name="email" type="email" required value="<?= e($old['email']) ?>" class="mt-1 w-full rounded border border-emas px-3 py-2">
      </div>
      <div>
        <label for="password" class="text-sm font-semibold">Password (minimal 10 karakter)</label>
        <input id="password" name="password" type="password" required class="mt-1 w-full rounded border border-emas px-3 py-2">
      </div>
      <div>
        <label for="konfirmasi" class="text-sm font-semibold">Ulangi password</label>
        <input id="konfirmasi" name="konfirmasi" type="password" required class="mt-1 w-full rounded border border-emas px-3 py-2">
      </div>
      <button type="submit" class="tombol-emas w-full bg-hijau text-emas hover:bg-marun hover:text-gading">Buat Admin</button>
    </form>
  </div>
</body>
</html>