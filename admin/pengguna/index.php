<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/admin-helpers.php';
require_role('admin');

$daftar = db()->query('SELECT id, name, username, email, role, is_active, created_at FROM users ORDER BY role, name')->fetchAll();
$saya   = current_user()['id'];

$adminTitle  = 'Pengguna';
$adminActive = 'pengguna';
require __DIR__ . '/../../includes/admin-header.php';
?>
<div class="adm-bar">
  <p class="adm-hint" style="margin:0">Admin dapat mengelola semua menu termasuk pengguna. Editor hanya mengelola konten.</p>
  <a href="<?= e(base_url('admin/pengguna/form.php')) ?>" class="adm-btn">+ Tambah pengguna</a>
</div>

<div class="adm-tabel-wrap">
  <table class="adm-tabel">
    <thead><tr><th>Nama</th><th>Username</th><th>Email</th><th>Peran</th><th>Status</th><th>Dibuat</th><th>Aksi</th></tr></thead>
    <tbody>
    <?php foreach ($daftar as $u): ?>
      <tr>
        <td><strong><?= e($u['name']) ?></strong><?= (int) $u['id'] === $saya ? ' <span class="adm-hint">(Anda)</span>' : '' ?></td>
        <td><?= e($u['username']) ?></td>
        <td><?= e($u['email']) ?></td>
        <td><span class="adm-badge adm-badge--<?= $u['role'] === 'admin' ? 'admin' : 'editor' ?>"><?= e($u['role']) ?></span></td>
        <td><?= (int) $u['is_active'] === 1 ? '<span class="adm-badge adm-badge--terbit">Aktif</span>' : '<span class="adm-badge adm-badge--draf">Nonaktif</span>' ?></td>
        <td><?= e(tanggal_id($u['created_at'])) ?></td>
        <td>
          <div class="adm-aksi">
            <a class="adm-btn adm-btn--garis adm-btn--kecil" href="<?= e(base_url('admin/pengguna/form.php?id=' . (int) $u['id'])) ?>">Edit</a>
            <?php if ((int) $u['id'] !== $saya): ?>
              <form method="post" action="<?= e(base_url('admin/pengguna/hapus.php')) ?>" onsubmit="return confirm('Hapus pengguna ini? Konten yang pernah ia buat tetap ada.');">
                <?= csrf_field() ?>
                <input type="hidden" name="id" value="<?= (int) $u['id'] ?>">
                <button type="submit" class="adm-btn adm-btn--bahaya adm-btn--kecil">Hapus</button>
              </form>
            <?php endif; ?>
          </div>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>

<?php require __DIR__ . '/../../includes/admin-footer.php'; ?>
