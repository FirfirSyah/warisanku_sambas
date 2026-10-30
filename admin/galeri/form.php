<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/admin-helpers.php';
require_login();

$id = (int) ($_GET['id'] ?? 0);
$data = ['title' => '', 'category_id' => '', 'description' => '', 'source' => '', 'license_info' => ''];
$gambarLama = null;

if ($id > 0) {
    $stmt = db()->prepare('SELECT * FROM galleries WHERE id = :id');
    $stmt->execute([':id' => $id]);
    $row = $stmt->fetch();
    if (!$row) {
        http_response_code(404);
        exit('Foto tidak ditemukan.');
    }
    $data = array_merge($data, array_map(fn($v) => $v ?? '', $row));
    $gambarLama = $row['image_path'];
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    $data['title']       = post_teks('title');
    $kategoriId          = kategori_valid($_POST['category_id'] ?? 0, 'galeri');
    $data['category_id'] = $kategoriId ?? '';
    $data['description'] = post_teks('description');
    $data['source']      = post_teks('source');
    $data['license_info'] = post_teks('license_info');

    if (mb_strlen($data['title']) < 3 || mb_strlen($data['title']) > 150) {
        $errors[] = 'Judul foto harus 3 sampai 150 karakter.';
    }
    if ($kategoriId === null) {
        $errors[] = 'Pilih kategori foto.';
    }
    if (mb_strlen($data['description']) > 1000) {
        $errors[] = 'Deskripsi maksimal 1000 karakter.';
    }
    if (mb_strlen($data['source']) < 3 || mb_strlen($data['source']) > 255) {
        $errors[] = 'Sumber foto wajib diisi (3 sampai 255 karakter).';
    }
    if (mb_strlen($data['license_info']) > 255) {
        $errors[] = 'Informasi lisensi maksimal 255 karakter.';
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
                $stmt = db()->prepare('UPDATE galleries SET title = :t, category_id = :c, description = :d, source = :s, license_info = :l, image_path = :i WHERE id = :id');
                $stmt->execute([
                    ':t' => $data['title'], ':c' => $kategoriId, ':d' => $data['description'] !== '' ? $data['description'] : null,
                    ':s' => $data['source'], ':l' => $data['license_info'] !== '' ? $data['license_info'] : null,
                    ':i' => $gambarSimpan, ':id' => $id,
                ]);
            } else {
                $stmt = db()->prepare('INSERT INTO galleries (title, category_id, description, source, license_info, image_path, uploaded_by) VALUES (:t, :c, :d, :s, :l, :i, :u)');
                $stmt->execute([
                    ':t' => $data['title'], ':c' => $kategoriId, ':d' => $data['description'] !== '' ? $data['description'] : null,
                    ':s' => $data['source'], ':l' => $data['license_info'] !== '' ? $data['license_info'] : null,
                    ':i' => $gambarSimpan, ':u' => current_user()['id'],
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

$kategori    = daftar_kategori('galeri');
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
    <label for="category_id" class="adm-label">Kategori</label>
    <select id="category_id" name="category_id" required class="adm-select">
      <option value="">- Pilih kategori -</option>
      <?php foreach ($kategori as $k): ?>
        <option value="<?= (int) $k['id'] ?>" <?= (string) $data['category_id'] === (string) $k['id'] ? 'selected' : '' ?>><?= e($k['name']) ?></option>
      <?php endforeach; ?>
    </select>
    <?php if (!$kategori): ?><p class="adm-hint">Belum ada kategori galeri. Buat dulu di menu Kategori (pilih &quot;Foto galeri&quot;).</p><?php endif; ?>
  </div>

  <div class="adm-field">
    <label for="description" class="adm-label">Deskripsi</label>
    <textarea id="description" name="description" maxlength="1000" class="adm-textarea" style="min-height:4.5rem"><?= e((string) $data['description']) ?></textarea>
  </div>

  <div class="adm-field">
    <label for="source" class="adm-label">Sumber foto</label>
    <input id="source" name="source" required maxlength="255" value="<?= e($data['source']) ?>" class="adm-input">
    <p class="adm-hint">Contoh: "Dokumentasi pribadi" atau nama fotografer.</p>
  </div>

  <div class="adm-field">
    <label for="license_info" class="adm-label">Lisensi (opsional)</label>
    <input id="license_info" name="license_info" maxlength="255" value="<?= e($data['license_info']) ?>" class="adm-input">
    <p class="adm-hint">Contoh: "CC BY-SA 4.0" atau "Izin tertulis pemilik foto".</p>
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
