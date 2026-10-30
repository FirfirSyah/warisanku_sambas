<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/admin-helpers.php';
require_login();

const GALERI_PER_HALAMAN = 12;

$kat = (int) ($_GET['kategori'] ?? 0);
$hal = halaman_aktif();
$daftarKat = daftar_kategori('galeri');
if (!in_array($kat, array_map(fn($k) => (int) $k['id'], $daftarKat), true)) {
    $kat = 0;
}

$where  = '1 = 1';
$params = [];
if ($kat > 0) {
    $where = 'g.category_id = :k';
    $params[':k'] = $kat;
}

$stmt = db()->prepare("SELECT COUNT(*) FROM galleries g WHERE $where");
$stmt->execute($params);
$total = (int) $stmt->fetchColumn();

$stmt = db()->prepare(
    "SELECT g.id, g.title, c.name AS category, CONCAT_WS(' / ', NULLIF(g.source, ''), NULLIF(g.license_info, '')) AS source, g.image_path AS image
       FROM galleries g LEFT JOIN categories c ON c.id = g.category_id
      WHERE $where ORDER BY g.created_at DESC, g.id DESC LIMIT :lim OFFSET :off"
);
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
      <?php foreach ($daftarKat as $k): ?>
        <option value="<?= (int) $k['id'] ?>" <?= $kat === (int) $k['id'] ? 'selected' : '' ?>><?= e($k['name']) ?></option>
      <?php endforeach; ?>
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
          <p class="adm-hint"><?= e($r['category'] ?? '-') ?> &middot; Sumber: <?= e($r['source']) ?></p>
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
  <?php tampil_pager($total, $hal, GALERI_PER_HALAMAN, ['kategori' => $kat ?: '']); ?>
<?php endif; ?>

<?php require __DIR__ . '/../../includes/admin-footer.php'; ?>
