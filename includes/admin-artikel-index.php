<?php
// Daftar artikel. Butuh variabel $tipe ('sejarah' atau 'budaya') dari file pemanggil.
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/admin-helpers.php';
require_login();

$cfg = cfg_artikel($tipe);
$q      = trim((string) ($_GET['q'] ?? ''));
$status = (string) ($_GET['status'] ?? '');
$hal    = halaman_aktif();

$where  = ['a.type = :tipe'];
$params = [':tipe' => $tipe];
if ($q !== '') {
    $where[] = '(a.title LIKE :q1 OR a.summary LIKE :q2)';
    $params[':q1'] = '%' . $q . '%';
    $params[':q2'] = '%' . $q . '%';
}
if (in_array($status, ['draft', 'published'], true)) {
    $where[] = 'a.status = :st';
    $params[':st'] = $status;
}
$whereSql = implode(' AND ', $where);

$stmt = db()->prepare("SELECT COUNT(*) FROM articles a WHERE $whereSql");
$stmt->execute($params);
$total = (int) $stmt->fetchColumn();

$stmt = db()->prepare(
    "SELECT a.id, a.title, a.status, a.image, a.created_at, c.name AS kategori
       FROM articles a LEFT JOIN categories c ON c.id = a.category_id
      WHERE $whereSql ORDER BY a.created_at DESC, a.id DESC LIMIT :lim OFFSET :off"
);
foreach ($params as $k => $v) {
    $stmt->bindValue($k, $v);
}
$stmt->bindValue(':lim', PER_HALAMAN, PDO::PARAM_INT);
$stmt->bindValue(':off', ($hal - 1) * PER_HALAMAN, PDO::PARAM_INT);
$stmt->execute();
$baris = $stmt->fetchAll();

$adminTitle  = 'Artikel ' . $cfg['label'];
$adminActive = $tipe;
require __DIR__ . '/admin-header.php';
?>
<div class="adm-bar">
  <form method="get" class="adm-cari">
    <label for="q" class="sr-only">Cari</label>
    <input id="q" name="q" type="search" value="<?= e($q) ?>" placeholder="Cari judul..." class="adm-input" style="width:14rem">
    <select name="status" class="adm-select" style="width:auto" aria-label="Status">
      <option value="">Semua status</option>
      <option value="published" <?= $status === 'published' ? 'selected' : '' ?>>Terbit</option>
      <option value="draft" <?= $status === 'draft' ? 'selected' : '' ?>>Draf</option>
    </select>
    <button type="submit" class="adm-btn adm-btn--garis">Cari</button>
  </form>
  <a href="<?= e(base_url($cfg['folder'] . '/form.php')) ?>" class="adm-btn">+ Tambah artikel</a>
</div>

<?php if (!$baris): ?>
  <div class="adm-kartu adm-kosong">Belum ada artikel <?= e(strtolower($cfg['label'])) ?>.</div>
<?php else: ?>
  <div class="adm-tabel-wrap">
    <table class="adm-tabel">
      <thead><tr><th>Gambar</th><th>Judul</th><?php if ($cfg['kategori']): ?><th>Kategori</th><?php endif; ?><th>Status</th><th>Dibuat</th><th>Aksi</th></tr></thead>
      <tbody>
      <?php foreach ($baris as $r): ?>
        <tr>
          <td><?php if ($r['image']): ?><img class="adm-thumb" src="<?= e(gambar_url($r['image'])) ?>" alt=""><?php else: ?>-<?php endif; ?></td>
          <td><strong><?= e($r['title']) ?></strong></td>
          <?php if ($cfg['kategori']): ?><td><?= e($r['kategori'] ?? '-') ?></td><?php endif; ?>
          <td><?= badge_status($r['status']) ?></td>
          <td><?= e(tanggal_id($r['created_at'])) ?></td>
          <td>
            <div class="adm-aksi">
              <a class="adm-btn adm-btn--garis adm-btn--kecil" href="<?= e(base_url($cfg['folder'] . '/form.php?id=' . (int) $r['id'])) ?>">Edit</a>
              <form method="post" action="<?= e(base_url($cfg['folder'] . '/hapus.php')) ?>" onsubmit="return confirm('Hapus artikel ini? Tindakan ini tidak bisa dibatalkan.');">
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

<?php require __DIR__ . '/admin-footer.php'; ?>
