<?php
$adminTitle  = 'Dashboard';
$adminActive = 'dashboard';
require __DIR__ . '/../includes/admin-header.php';
require_once __DIR__ . '/../includes/admin-helpers.php';

$pdo = db();

/** Jumlah baris sebuah query COUNT(*). */
$hitung = fn(string $sql): int => (int) $pdo->query($sql)->fetchColumn();

// [label, total, jumlah draf (null = tidak ada status), halaman kelola]
$stat = [
    ['Artikel Sejarah',  $hitung("SELECT COUNT(*) FROM articles WHERE type = 'sejarah'"),
                         $hitung("SELECT COUNT(*) FROM articles WHERE type = 'sejarah' AND status = 'draft'"), 'admin/artikel/index.php'],
    ['Artikel Budaya',   $hitung("SELECT COUNT(*) FROM articles WHERE type = 'budaya'"),
                         $hitung("SELECT COUNT(*) FROM articles WHERE type = 'budaya' AND status = 'draft'"),  'admin/budaya/index.php'],
    ['Destinasi Wisata', $hitung('SELECT COUNT(*) FROM destinations'),
                         $hitung("SELECT COUNT(*) FROM destinations WHERE status = 'draft'"),                  'admin/wisata/index.php'],
    ['Foto Galeri',      $hitung('SELECT COUNT(*) FROM galleries'),  null, 'admin/galeri/index.php'],
    ['Kategori',         $hitung('SELECT COUNT(*) FROM categories'), null, 'admin/kategori/index.php'],
];
if ($user['role'] === 'admin') {
    $stat[] = ['Pengguna', $hitung('SELECT COUNT(*) FROM users'), null, 'admin/pengguna/index.php'];
}
$totalDraf = array_sum(array_map(fn($s) => (int) $s[2], $stat));

// Konten terbaru dari tiga tabel, digabung dan diurutkan
$terbaru = $pdo->query(
    "SELECT 'Sejarah' AS jenis, id, title, status, created_at FROM articles WHERE type = 'sejarah'
     UNION ALL
     SELECT 'Budaya', id, title, status, created_at FROM articles WHERE type = 'budaya'
     UNION ALL
     SELECT 'Wisata', id, name, status, created_at FROM destinations
     ORDER BY created_at DESC, id DESC LIMIT 8"
)->fetchAll();

$formEdit = ['Sejarah' => 'admin/artikel/form.php', 'Budaya' => 'admin/budaya/form.php', 'Wisata' => 'admin/wisata/form.php'];
?>
<p class="mb-4">Halo, <strong><?= e($user['name']) ?></strong>. Berikut ringkasan isi website.</p>

<?php if ($totalDraf > 0): ?>
  <div class="adm-kartu" style="margin-bottom:1rem">
    Ada <strong><?= $totalDraf ?></strong> konten berstatus <span class="adm-badge adm-badge--draf">Draf</span> yang belum tampil di situs.
  </div>
<?php endif; ?>

<div class="grid grid-cols-2 gap-4 lg:grid-cols-3">
  <?php foreach ($stat as [$label, $jumlah, $draf, $url]): ?>
    <div class="rounded-lg border border-emas bg-gading p-4">
      <p class="text-sm text-hijau/70"><?= e($label) ?></p>
      <p class="mt-1 font-serif text-3xl font-bold text-marun"><span data-count="<?= $jumlah ?>"><?= $jumlah ?></span></p>
      <p class="adm-hint"><?= $draf !== null ? $draf . ' draf' : '&nbsp;' ?></p>
      <a href="<?= e(base_url($url)) ?>" class="text-sm font-semibold text-marun hover:underline">Kelola &rarr;</a>
    </div>
  <?php endforeach; ?>
</div>

<h2 class="mt-8 mb-3 font-serif text-lg font-bold">Tambah konten</h2>
<div class="adm-aksi" style="flex-wrap:wrap">
  <a class="adm-btn" href="<?= e(base_url('admin/artikel/form.php')) ?>">+ Sejarah</a>
  <a class="adm-btn" href="<?= e(base_url('admin/budaya/form.php')) ?>">+ Budaya</a>
  <a class="adm-btn" href="<?= e(base_url('admin/wisata/form.php')) ?>">+ Wisata</a>
  <a class="adm-btn" href="<?= e(base_url('admin/galeri/form.php')) ?>">+ Foto galeri</a>
</div>

<h2 class="mt-8 mb-3 font-serif text-lg font-bold">Konten terbaru</h2>
<?php if (!$terbaru): ?>
  <div class="adm-kartu adm-kosong">Belum ada konten. Mulai dengan tombol "Tambah konten" di atas.</div>
<?php else: ?>
  <div class="adm-tabel-wrap">
    <table class="adm-tabel">
      <thead><tr><th>Jenis</th><th>Judul</th><th>Status</th><th>Dibuat</th><th>Aksi</th></tr></thead>
      <tbody>
      <?php foreach ($terbaru as $r): ?>
        <tr>
          <td><?= e($r['jenis']) ?></td>
          <td><strong><?= e($r['title']) ?></strong></td>
          <td><?= badge_status($r['status']) ?></td>
          <td><?= e(tanggal_id($r['created_at'])) ?></td>
          <td><a class="adm-btn adm-btn--garis adm-btn--kecil" href="<?= e(base_url($formEdit[$r['jenis']] . '?id=' . (int) $r['id'])) ?>">Edit</a></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
<?php endif; ?>

<?php require __DIR__ . '/../includes/admin-footer.php'; ?>
