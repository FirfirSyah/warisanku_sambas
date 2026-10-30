<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/admin-helpers.php';
require_login();

const GALERI_PER_HALAMAN = 12;

$kat = (string) ($_GET['kategori'] ?? '');
$hal = halaman_aktif();

$where  = '1 = 1';
$params = [];
if (in_array($kat, ['Budaya', 'Wisata'], true)) {
    $where = 'category = :k';
    $params[':k'] = $kat;
} else {
    $kat = '';
}

$stmt = db()->prepare("SELECT COUNT(*) FROM galleries WHERE $where");
$stmt->execute($params);
$total = (int) $stmt->fetchColumn();

$stmt = db()->prepare("SELECT id, title, category, source, image FROM galleries WHERE $where ORDER BY created_at DESC, id DESC LIMIT :lim OFFSET :off");
foreach ($params as $k => $v) {
    $stmt->bindValue($k, $v);
}
$stmt->bindValue(':lim', GALERI_PER_HALAMAN, PDO::PARAM_INT);
$stmt->bindValue(':off', ($hal - 1) * GALERI_PER_HALAMAN, PDO::PARAM_INT);
$stmt->execute();
$baris = $stmt->fetchAll();

$adminTitle  = 'Galeri Foto';
$adminActive = 'galeri';
require __DIR__ . '/../../includes/admin-header.php';
?>
<div class="adm-bar">
  <form method="get" class="adm-cari">
    <label for="kategori" class="sr-only">Kategori</label>
    <select id="kategori" name="kategori" class="adm-select" style="width:auto" onchange="this.form.submit()">
      <option value="">Semua kategori</option>
      <option value="Budaya" <?= $kat === 'Budaya' ? 'selected' : '' ?>>Budaya</option>
      <option value="Wisata" <?= $kat === 'Wisata' ? 'selected' : '' ?>>Wisata</option>
    </select>
    <noscript><button type="submit" class="adm-btn adm-btn--garis">Terapkan</button></noscript>
  </form>
  <a href="<?= e(base_url('admin/galeri/form.php')) ?>" class="adm-btn">+ Tambah foto</a>
</div>

<?php if (!$baris): ?>
  <div class="adm-kartu adm-kosong">Belum ada foto di galeri.</div>
<?php else: ?>
  <div class="adm-galeri">
    <?php foreach ($baris as $r): ?>
      <div class="adm-galeri__item">
        <img src="<?= e(gambar_url($r['image'])) ?>" alt="<?= e($r['title']) ?>" loading="lazy">
        <div class="adm-galeri__isi">
          <strong><?= e($r['title']) ?></strong>
          <p class="adm-hint"><?= e($r['category']) ?> &middot; Sumber: <?= e($r['source']) ?></p>
          <div class="adm-aksi" style="margin-top:.5rem">
            <a class="adm-btn adm-btn--garis adm-btn--kecil" href="<?= e(base_url('admin/galeri/form.php?id=' . (int) $r['id'])) ?>">Edit</a>
            <form method="post" action="<?= e(base_url('admin/galeri/hapus.php')) ?>" onsubmit="return confirm('Hapus foto ini?');">
              <?= csrf_field() ?>
              <input type="hidden" name="id" value="<?= (int) $r['id'] ?>">
              <button type="submit" class="adm-btn adm-btn--bahaya adm-btn--kecil">Hapus</button>
            </form>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
  <?php tampil_pager($total, $hal, GALERI_PER_HALAMAN, ['kategori' => $kat]); ?>
<?php endif; ?>

<?php require __DIR__ . '/../../includes/admin-footer.php'; ?>
