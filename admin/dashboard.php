<?php
$adminTitle  = 'Dashboard';
$adminActive = 'dashboard';
require __DIR__ . '/../includes/admin-header.php';

$pdo = db();
$stat = [
    'Artikel Sejarah'   => (int) $pdo->query("SELECT COUNT(*) FROM articles WHERE type = 'sejarah'")->fetchColumn(),
    'Artikel Budaya'    => (int) $pdo->query("SELECT COUNT(*) FROM articles WHERE type = 'budaya'")->fetchColumn(),
    'Destinasi Wisata'  => (int) $pdo->query('SELECT COUNT(*) FROM destinations')->fetchColumn(),
    'Foto Galeri'       => (int) $pdo->query('SELECT COUNT(*) FROM galleries')->fetchColumn(),
    'Kategori'          => (int) $pdo->query('SELECT COUNT(*) FROM categories')->fetchColumn(),
    'Pengguna'          => (int) $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn(),
];
?>
<p class="mb-4">Halo, <strong><?= e($user['name']) ?></strong>. Berikut ringkasan isi website.</p>

<div class="grid grid-cols-2 gap-4 lg:grid-cols-3">
  <?php foreach ($stat as $label => $jumlah): ?>
    <div class="rounded-lg border border-emas bg-gading p-4">
      <p class="text-sm text-hijau/70"><?= e($label) ?></p>
      <p class="mt-1 font-serif text-3xl font-bold text-marun"><span data-count="<?= $jumlah ?>"><?= $jumlah ?></span></p>
    </div>
  <?php endforeach; ?>
</div>

<?php require __DIR__ . '/../includes/admin-footer.php'; ?>