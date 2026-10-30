<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/admin-helpers.php';
require_login();

$editId = (int) ($_GET['edit'] ?? 0);
$form   = ['name' => '', 'type' => 'budaya'];
$errors = [];

if ($editId > 0) {
    $stmt = db()->prepare('SELECT id, name, type FROM categories WHERE id = :id');
    $stmt->execute([':id' => $editId]);
    $row = $stmt->fetch();
    if (!$row) {
        set_flash('error', 'Kategori tidak ditemukan.');
        redirect('admin/kategori/index.php');
    }
    $form = $row;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    $form['name'] = post_teks('name');
    if ($editId === 0) {
        $form['type'] = post_teks('type') === 'wisata' ? 'wisata' : 'budaya';
    }

    if (mb_strlen($form['name']) < 2 || mb_strlen($form['name']) > 80) {
        $errors[] = 'Nama kategori harus 2 sampai 80 karakter.';
    }

    if (!$errors) {
        // Nama yang sama pada jenis yang sama tidak boleh ada dua kali
        $stmt = db()->prepare('SELECT COUNT(*) FROM categories WHERE type = :t AND LOWER(name) = LOWER(:n) AND id <> :id');
        $stmt->execute([':t' => $form['type'], ':n' => $form['name'], ':id' => $editId]);
        if ((int) $stmt->fetchColumn() > 0) {
            $errors[] = 'Kategori dengan nama itu sudah ada.';
        }
    }

    if (!$errors) {
        $slug = slug_unik('categories', $form['name'], $editId ?: null, $form['type']);
        try {
            if ($editId > 0) {
                db()->prepare('UPDATE categories SET name = :n, slug = :s WHERE id = :id')
                    ->execute([':n' => $form['name'], ':s' => $slug, ':id' => $editId]);
                set_flash('success', 'Kategori diperbarui.');
            } else {
                db()->prepare('INSERT INTO categories (name, slug, type) VALUES (:n, :s, :t)')
                    ->execute([':n' => $form['name'], ':s' => $slug, ':t' => $form['type']]);
                set_flash('success', 'Kategori ditambahkan.');
            }
            redirect('admin/kategori/index.php');
        } catch (PDOException $ex) {
            error_log('Simpan kategori gagal: ' . $ex->getMessage());
            $errors[] = 'Data gagal disimpan. Coba lagi.';
        }
    }
}

$daftar = db()->query(
    "SELECT c.id, c.name, c.type,
            (SELECT COUNT(*) FROM articles a WHERE a.category_id = c.id)
          + (SELECT COUNT(*) FROM destinations d WHERE d.category_id = c.id) AS dipakai
       FROM categories c ORDER BY c.type, c.name"
)->fetchAll();

$adminTitle  = 'Kategori';
$adminActive = 'kategori';
require __DIR__ . '/../../includes/admin-header.php';
?>
<div class="adm-grid-2" style="align-items:start">
  <form method="post" class="adm-kartu">
    <h2 class="font-serif text-lg font-bold" style="margin-bottom:.75rem"><?= $editId > 0 ? 'Ubah kategori' : 'Tambah kategori' ?></h2>
    <?php if ($errors): ?>
      <ul class="adm-errors" role="alert"><?php foreach ($errors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?></ul>
    <?php endif; ?>
    <?= csrf_field() ?>
    <div class="adm-field">
      <label for="name" class="adm-label">Nama kategori</label>
      <input id="name" name="name" required maxlength="80" value="<?= e($form['name']) ?>" class="adm-input">
    </div>
    <div class="adm-field">
      <label for="type" class="adm-label">Untuk</label>
      <select id="type" name="type" class="adm-select" <?= $editId > 0 ? 'disabled' : '' ?>>
        <option value="budaya" <?= $form['type'] === 'budaya' ? 'selected' : '' ?>>Artikel budaya</option>
        <option value="wisata" <?= $form['type'] === 'wisata' ? 'selected' : '' ?>>Destinasi wisata</option>
      </select>
      <?php if ($editId > 0): ?><p class="adm-hint">Jenis kategori tidak bisa diubah setelah dibuat.</p><?php endif; ?>
    </div>
    <div class="adm-aksi">
      <button type="submit" class="adm-btn">Simpan</button>
      <?php if ($editId > 0): ?><a href="<?= e(base_url('admin/kategori/index.php')) ?>" class="adm-btn adm-btn--garis">Batal</a><?php endif; ?>
    </div>
  </form>

  <div class="adm-tabel-wrap">
    <table class="adm-tabel">
      <thead><tr><th>Nama</th><th>Untuk</th><th>Dipakai</th><th>Aksi</th></tr></thead>
      <tbody>
      <?php foreach ($daftar as $k): ?>
        <tr>
          <td><strong><?= e($k['name']) ?></strong></td>
          <td><?= $k['type'] === 'budaya' ? 'Budaya' : 'Wisata' ?></td>
          <td><?= (int) $k['dipakai'] ?></td>
          <td>
            <div class="adm-aksi">
              <a class="adm-btn adm-btn--garis adm-btn--kecil" href="<?= e(base_url('admin/kategori/index.php?edit=' . (int) $k['id'])) ?>">Edit</a>
              <form method="post" action="<?= e(base_url('admin/kategori/hapus.php')) ?>" onsubmit="return confirm('Hapus kategori ini?');">
                <?= csrf_field() ?>
                <input type="hidden" name="id" value="<?= (int) $k['id'] ?>">
                <button type="submit" class="adm-btn adm-btn--bahaya adm-btn--kecil">Hapus</button>
              </form>
            </div>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if (!$daftar): ?><tr><td colspan="4" class="adm-kosong">Belum ada kategori.</td></tr><?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require __DIR__ . '/../../includes/admin-footer.php'; ?>
