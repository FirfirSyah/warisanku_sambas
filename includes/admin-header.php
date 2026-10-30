<?php
require_once __DIR__ . '/auth.php';
require_login();

$adminTitle  = $adminTitle  ?? 'Dashboard';
$adminActive = $adminActive ?? '';
$user        = current_user();
$flash       = get_flash();

// 'siap' => true kalau halamannya sudah dibuat. Ubah ke true seiring kita bangun.
$menuAdmin = [
    'dashboard' => ['Dashboard',     'admin/dashboard.php',     true],
    'sejarah'   => ['Sejarah',       'admin/artikel/index.php', false],
    'budaya'    => ['Budaya',        'admin/budaya/index.php',  false],
    'wisata'    => ['Wisata',        'admin/wisata/index.php',  false],
    'galeri'    => ['Galeri',        'admin/galeri/index.php',  false],
    'kategori'  => ['Kategori',      'admin/kategori/index.php', false],
];
if ($user['role'] === 'admin') {
    $menuAdmin['pengguna'] = ['Pengguna', 'admin/pengguna/index.php', false];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="robots" content="noindex, nofollow">
  <title><?= e($adminTitle) ?> | Admin <?= e(SITE_NAME) ?></title>
  <link rel="stylesheet" href="<?= e(asset('css/style.css')) ?>">
</head>
<body class="bg-krem text-hijau antialiased">
<div class="min-h-screen md:flex">

  <aside class="bg-hijau text-gading md:w-60 md:shrink-0">
    <div class="border-b-4 border-emas px-4 py-4">
      <p class="font-serif text-lg font-bold text-emas">WARISANKU</p>
      <p class="text-xs uppercase tracking-widest text-gading/70">Panel Admin</p>
    </div>
    <nav class="flex gap-1 overflow-x-auto p-2 md:block md:space-y-1" aria-label="Menu admin">
      <?php foreach ($menuAdmin as $kunci => [$label, $url, $siap]): ?>
        <?php if ($siap): ?>
          <a href="<?= e(base_url($url)) ?>"
             class="block whitespace-nowrap rounded px-3 py-2 hover:bg-emas hover:text-hijau <?= $adminActive === $kunci ? 'bg-emas text-hijau font-semibold' : '' ?>">
            <?= e($label) ?>
          </a>
        <?php else: ?>
          <span class="block cursor-not-allowed whitespace-nowrap rounded px-3 py-2 text-gading/40" title="Segera hadir">
            <?= e($label) ?>
          </span>
        <?php endif; ?>
      <?php endforeach; ?>
    </nav>
  </aside>

  <div class="flex-1">
    <div class="flex items-center justify-between border-b border-emas bg-gading px-4 py-3">
      <h1 class="font-serif text-xl font-bold"><?= e($adminTitle) ?></h1>
      <div class="flex items-center gap-3 text-sm">
        <span><?= e($user['name']) ?> <span class="text-marun">(<?= e($user['role']) ?>)</span></span>
        <a href="<?= e(base_url('public/index.php')) ?>" class="text-marun hover:underline" target="_blank" rel="noopener">Lihat situs</a>
        <form method="post" action="<?= e(base_url('admin/logout.php')) ?>">
          <?= csrf_field() ?>
          <button type="submit" class="rounded border border-marun px-3 py-1 text-marun hover:bg-marun hover:text-gading">Keluar</button>
        </form>
      </div>
    </div>

    <main class="p-4 md:p-6">
      <?php if ($flash): ?>
        <div class="mb-4 rounded border px-4 py-3 <?= $flash['type'] === 'success' ? 'border-hijau bg-hijau/10' : 'border-marun bg-marun/10 text-marun' ?>" role="alert">
          <?= e($flash['msg']) ?>
        </div>
      <?php endif; ?>