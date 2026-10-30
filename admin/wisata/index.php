<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/admin-helpers.php';
require_login();

$q      = trim((string) ($_GET['q'] ?? ''));
$status = (string) ($_GET['status'] ?? '');
$hal    = halaman_aktif();

$where  = ['1 = 1'];
$params = [];
if ($q !== '') {
    $where[] = '(d.name LIKE :q1 OR d.description LIKE :q2 OR d.address LIKE :q3)';
    $params[':q1'] = like_aman($q);
    $params[':q2'] = like_aman($q);
    $params[':q3'] = like_aman($q);
}
if (in_array($status, ['draft', 'published'], true)) {
    $where[] = 'd.status = :st';
    $params[':st'] = $status;
}
$whereSql = implode(' AND ', $where);

$stmt = db()->prepare("SELECT COUNT(*) FROM destinations d WHERE $whereSql");
$stmt->execute($params);
$total = (int) $stmt->fetchColumn();

$stmt = db()->prepare(
    "SELECT d.id, d.name AS title, d.status, d.featured_image AS image, d.address, d.created_at, c.name AS kategori
       FROM destinations d LEFT JOIN categories c ON c.id = d.category_id
      WHERE $whereSql ORDER BY d.created_at DESC, d.id DESC LIMIT :lim OFFSET :off"
);
foreach ($params as $k => $v) {
    $stmt->bindValue($k, $v);
}
$stmt->bindValue(':lim', PER_HALAMAN, PDO::PARAM_INT);
$stmt->bindValue(':off', ($hal - 1) * PER_HALAMAN, PDO::PARAM_INT);
$stmt->execute();
$baris = $stmt->fetchAll();

$adminTitle  = 'Destinasi Wisata';
$adminActive = 'wisata';
require __DIR__ . '/../../includes/admin-header.php';
?>
<div class="adm-bar">
  <form method="get" class="adm-cari">
    <label for="q" class="sr-only">Cari</label>
    <input id="q" name="q" type="search" value="<?= e($q) ?>" placeholder="Cari destinasi..." class="adm-input" style="width:14rem">
    <select name="status" class="adm-select" style="width:auto" aria-label="Status">
      <option value="">Semua status</option>
      <option value="published" <?= $status === 'published' ? 'selected' : '' ?>>Terbit</option>
      <option value="draft" <?= $status === 'draft' ? 'selected' : '' ?>>Draf</option>
    </select>
    <button type="submit" class="adm-btn adm-btn--garis">Cari</button>
  </form>
  <a href="<?= e(base_url('admin/wisata/form.php')) ?>" class="adm-btn">+ Tambah destinasi</a>
</div>

<?php if (!$baris): ?>
  <div class="adm-kartu adm-kosong">Belum ada destinasi wisata.</div>
<?php else: ?>
  <div class="adm-tabel-wrap">
    <table class="adm-tabel">
      <thead><tr><th>Gambar</th><th>Nama</th><th>Kategori</th><th>Alamat</th><th>Status</th><th>Aksi</th></tr></thead>
      <tbody>
      <?php foreach ($baris as $r): ?>
        <tr>
          <td><?php if ($r['image']): ?><img class="adm-thumb" src="<?= e(gambar_url($r['image'])) ?>" alt=""><?php else: ?>-<?php endif; ?></td>
          <td><strong><?= e($r['title']) ?></strong></td>
          <td><?= e($r['kategori'] ?? '-') ?></td>
          <td><?= e($r['address'] ?? '-') ?></td>
          <td><?= badge_status($r['status']) ?></td>
          <td>
            <div class="adm-aksi">
              <a class="adm-btn adm-btn--garis adm-btn--kecil" href="<?= e(base_url('admin/wisata/form.php?id=' . (int) $r['id'])) ?>">Edit</a>
              <form method="post" action="<?= e(base_url('admin/wisata/hapus.php')) ?>" onsubmit="return confirm('Hapus destinasi ini? Tindakan ini tidak bisa dibatalkan.');">
                <?= csrf_field() ?>
                <input type="hidden" name="id" value="<?= (int) $r['id'] ?>">
                <button type="submit" class="adm-btn adm-btn--bahaya adm-btn--kecil">Hapus</button>
              </form>
            </div>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php tampil_pager($total, $hal, PER_HALAMAN, ['q' => $q, 'status' => $status]); ?>
<?php endif; ?>

<?php require __DIR__ . '/../../includes/admin-footer.php'; ?>
