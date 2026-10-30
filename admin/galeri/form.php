<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/admin-helpers.php';
require_login();

$id = (int) ($_GET['id'] ?? 0);
$data = ['title' => '', 'category' => 'Budaya', 'description' => '', 'source' => '', 'image' => null];
$gambarLama = null;

if ($id > 0) {
    $stmt = db()->prepare('SELECT * FROM galleries WHERE id = :id');
    $stmt->execute([':id' => $id]);
    $row = $stmt->fetch();
    if (!$row) {
        http_response_code(404);
        exit('Foto tidak ditemukan.');
    }
    $data = array_merge($data, $row);
    $gambarLama = $row['image'];
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    $data['title']       = post_teks('title');
    $data['category']    = post_teks('category') === 'Wisata' ? 'Wisata' : 'Budaya';
    $data['description'] = post_teks('description');
    $data['source']      = post_teks('source');

    if (mb_strlen($data['title']) < 3 || mb_strlen($data['title']) > 150) {
        $errors[] = 'Judul foto harus 3 sampai 150 karakter.';
    }
    if (mb_strlen($data['description']) > 300) {
        $errors[] = 'Deskripsi maksimal 300 karakter.';
    }
    if (mb_strlen($data['source']) < 3 || mb_strlen($data['source']) > 255) {
        $errors[] = 'Sumber foto dan lisensi wajib diisi (3 sampai 255 karakter).';
    }

    $gambarBaru = null;
    if (!$errors) {
        $gambarBaru = unggah_gambar('image', $errors);
        if (!$errors && $gambarBaru === null && $gambarLama === null) {
            $errors[] = 'Pilih file foto yang akan diunggah.';
        }
    }

    if (!$errors) {
        $gambarSimpan = $gambarBaru ?? $gambarLama;
        try {
            if ($id > 0) {
                $stmt = db()->prepare('UPDATE galleries SET title = :t, category = :c, description = :d, source = :s, image = :i WHERE id = :id');
                $stmt->execute([
                    ':t' => $data['title'], ':c' => $data['category'], ':d' => $data['description'],
                    ':s' => $data['source'], ':i' => $gambarSimpan, ':id' => $id,
                ]);
            } else {
                $stmt = db()->prepare('INSERT INTO galleries (title, category, description, source, image, created_by) VALUES (:t, :c, :d, :s, :i, :u)');
                $stmt->execute([
                    ':t' => $data['title'], ':c' => $data['category'], ':d' => $data['description'],
                    ':s' => $data['source'], ':i' => $gambarSimpan, ':u' => current_user()['id'],
                ]);
            }
            if ($gambarBaru !== null && $gambarLama !== null) {
                hapus_gambar($gambarLama);
            }
            set_flash('success', 'Foto berhasil disimpan.');
            redirect('admin/galeri/index.php');
        } catch (PDOException $ex) {
            error_log('Simpan galeri gagal: ' . $ex->getMessage());
            hapus_gambar($gambarBaru);
            $errors[] = 'Data gagal disimpan. Coba lagi.';
        }
    }
}

$adminTitle  = ($id > 0 ? 'Ubah' : 'Tambah') . ' Foto Galeri';
$adminActive = 'galeri';
require __DIR__ . '/../../includes/admin-header.php';
?>
<?php if ($errors): ?>
  <ul class="adm-errors" role="alert"><?php foreach ($errors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?></ul>
<?php endif; ?>

<form method="post" enctype="multipart/form-data" class="adm-kartu" style="max-width:40rem">
  <?= csrf_field() ?>

  <div class="adm-field">
    <label for="title" class="adm-label">Judul foto</label>
    <input id="title" name="title" required maxlength="150" value="<?= e($data['title']) ?>" class="adm-input">
  </div>

  <div class="adm-field">
    <label for="category" class="adm-label">Kategori</label>
    <select id="category" name="category" class="adm-select">
      <option value="Budaya" <?= $data['category'] === 'Budaya' ? 'selected' : '' ?>>Budaya</option>
      <option value="Wisata" <?= $data['category'] === 'Wisata' ? 'selected' : '' ?>>Wisata</option>
    </select>
  </div>

  <div class="adm-field">
    <label for="description" class="adm-label">Deskripsi</label>
    <textarea id="description" name="description" maxlength="300" class="adm-textarea" style="min-height:4.5rem"><?= e((string) $data['description']) ?></textarea>
  </div>

  <div class="adm-field">
    <label for="source" class="adm-label">Sumber foto dan lisensi</label>
    <input id="source" name="source" required maxlength="255" value="<?= e($data['source']) ?>" class="adm-input">
    <p class="adm-hint">Contoh: "Dokumentasi pribadi" atau nama fotografer. Cantumkan lisensi bila ada.</p>
  </div>

  <div class="adm-field">
    <label for="image" class="adm-label">Foto <?= $id > 0 ? '' : '(wajib)' ?></label>
    <?php if ($gambarLama): ?>
      <p><img src="<?= e(gambar_url($gambarLama)) ?>" alt="Foto saat ini" class="adm-thumb adm-thumb--besar"></p>
    <?php endif; ?>
    <input id="image" name="image" type="file" accept="image/jpeg,image/png,image/webp" <?= $id > 0 ? '' : 'required' ?> class="adm-input" style="margin-top:.5rem">
    <p class="adm-hint">JPG, PNG, atau WebP, maksimal 2 MB.<?= $id > 0 ? ' Kosongkan jika tidak ingin mengganti.' : '' ?></p>
  </div>

  <div class="adm-aksi">
    <button type="submit" class="adm-btn">Simpan</button>
    <a href="<?= e(base_url('admin/galeri/index.php')) ?>" class="adm-btn adm-btn--garis">Batal</a>
  </div>
</form>

<?php require __DIR__ . '/../../includes/admin-footer.php'; ?>
