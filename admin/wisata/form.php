<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/admin-helpers.php';
require_login();

$id = (int) ($_GET['id'] ?? 0);
$data = [
    'name' => '', 'category_id' => '', 'description' => '',
    'address' => '', 'latitude' => '', 'longitude' => '', 'is_verified' => 0, 'status' => 'draft',
];
$gambarLama = null;

if ($id > 0) {
    $stmt = db()->prepare('SELECT * FROM destinations WHERE id = :id');
    $stmt->execute([':id' => $id]);
    $row = $stmt->fetch();
    if (!$row) {
        http_response_code(404);
        exit('Destinasi tidak ditemukan.');
    }
    $data = array_merge($data, array_map(fn($v) => $v ?? '', $row));
    $gambarLama = $row['featured_image'];
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    $data['name']        = post_teks('name');
    $data['description'] = post_teks('description');
    $data['address']     = post_teks('address');
    $data['latitude']    = str_replace(',', '.', post_teks('latitude'));
    $data['longitude']   = str_replace(',', '.', post_teks('longitude'));
    $data['status']      = post_teks('status') === 'published' ? 'published' : 'draft';
    $data['is_verified'] = isset($_POST['is_verified']) ? 1 : 0;
    $kategoriId          = kategori_valid($_POST['category_id'] ?? 0, 'wisata');
    $data['category_id'] = $kategoriId ?? '';

    if (mb_strlen($data['name']) < 3 || mb_strlen($data['name']) > 150) {
        $errors[] = 'Nama destinasi harus 3 sampai 150 karakter.';
    }
    if ($kategoriId === null) {
        $errors[] = 'Pilih kategori.';
    }
    if (mb_strlen($data['description']) < 20) {
        $errors[] = 'Deskripsi minimal 20 karakter.';
    }
    if (mb_strlen($data['address']) > 255) {
        $errors[] = 'Alamat maksimal 255 karakter.';
    }

    // Koordinat: boleh kosong, tetapi kalau diisi harus lengkap dan masuk akal
    $lat = $lng = null;
    if ($data['latitude'] !== '' || $data['longitude'] !== '') {
        if (!is_numeric($data['latitude']) || !is_numeric($data['longitude'])) {
            $errors[] = 'Latitude dan longitude harus berupa angka, dan diisi keduanya.';
        } else {
            $lat = (float) $data['latitude'];
            $lng = (float) $data['longitude'];
            if ($lat < -90 || $lat > 90 || $lng < -180 || $lng > 180) {
                $errors[] = 'Koordinat di luar rentang (latitude -90 sampai 90, longitude -180 sampai 180).';
            }
        }
    }

    if ($data['is_verified'] === 1 && $lat === null) {
        $errors[] = 'Koordinat baru bisa ditandai terverifikasi bila latitude dan longitude diisi.';
    }

    $gambarBaru = null;
    if (!$errors) {
        $gambarBaru = unggah_gambar('image', $errors);
    }

    if (!$errors) {
        $gambarSimpan = $gambarLama;
        if ($gambarBaru !== null) {
            $gambarSimpan = $gambarBaru;
        } elseif (!empty($_POST['hapus_gambar'])) {
            $gambarSimpan = null;
        }
        $alamat = $data['address'] !== '' ? $data['address'] : null;

        try {
            if ($id > 0) {
                $stmt = db()->prepare(
                    'UPDATE destinations SET category_id = :cat, name = :name, description = :des,
                            address = :adr, latitude = :lat, longitude = :lng, is_verified = :ver,
                            featured_image = :img, status = :st
                      WHERE id = :id'
                );
                $stmt->execute([
                    ':cat' => $kategoriId, ':name' => $data['name'], ':des' => $data['description'],
                    ':adr' => $alamat, ':lat' => $lat, ':lng' => $lng, ':ver' => $data['is_verified'],
                    ':img' => $gambarSimpan, ':st' => $data['status'], ':id' => $id,
                ]);
            } else {
                $stmt = db()->prepare(
                    'INSERT INTO destinations (category_id, name, slug, description, address, latitude, longitude, is_verified, featured_image, status, author_id)
                     VALUES (:cat, :name, :slug, :des, :adr, :lat, :lng, :ver, :img, :st, :au)'
                );
                $stmt->execute([
                    ':cat' => $kategoriId, ':name' => $data['name'], ':slug' => slug_unik('destinations', $data['name']),
                    ':des' => $data['description'], ':adr' => $alamat, ':lat' => $lat, ':lng' => $lng,
                    ':ver' => $data['is_verified'], ':img' => $gambarSimpan, ':st' => $data['status'],
                    ':au' => current_user()['id'],
                ]);
            }
            if ($gambarSimpan !== $gambarLama) {
                hapus_gambar($gambarLama);
            }
            set_flash('success', 'Destinasi berhasil disimpan.');
            redirect('admin/wisata/index.php');
        } catch (PDOException $ex) {
            error_log('Simpan destinasi gagal: ' . $ex->getMessage());
            hapus_gambar($gambarBaru);
            $errors[] = 'Data gagal disimpan. Coba lagi.';
        }
    }
}

$kategori    = daftar_kategori('wisata');
$adminTitle  = ($id > 0 ? 'Ubah' : 'Tambah') . ' Destinasi';
$adminActive = 'wisata';
require __DIR__ . '/../../includes/admin-header.php';
?>
<?php if ($errors): ?>
  <ul class="adm-errors" role="alert"><?php foreach ($errors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?></ul>
<?php endif; ?>

<form method="post" enctype="multipart/form-data" class="adm-kartu" style="max-width:52rem">
  <?= csrf_field() ?>

  <div class="adm-field">
    <label for="name" class="adm-label">Nama destinasi</label>
    <input id="name" name="name" required maxlength="150" value="<?= e($data['name']) ?>" class="adm-input">
  </div>

  <div class="adm-grid-2">
    <div class="adm-field">
      <label for="category_id" class="adm-label">Kategori</label>
      <select id="category_id" name="category_id" required class="adm-select">
        <option value="">- Pilih kategori -</option>
        <?php foreach ($kategori as $k): ?>
          <option value="<?= (int) $k['id'] ?>" <?= (string) $data['category_id'] === (string) $k['id'] ? 'selected' : '' ?>><?= e($k['name']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="adm-field">
      <label for="status" class="adm-label">Status</label>
      <select id="status" name="status" class="adm-select">
        <option value="draft" <?= $data['status'] === 'draft' ? 'selected' : '' ?>>Draf (belum tampil di situs)</option>
        <option value="published" <?= $data['status'] === 'published' ? 'selected' : '' ?>>Terbit</option>
      </select>
    </div>
  </div>

  <div class="adm-field">
    <label for="description" class="adm-label">Deskripsi lengkap</label>
    <textarea id="description" name="description" required class="adm-textarea adm-textarea--besar"><?= e($data['description']) ?></textarea>
    <p class="adm-hint">Pisahkan paragraf dengan satu baris kosong. Paragraf pertama otomatis menjadi ringkasan di kartu daftar.</p>
  </div>

  <div class="adm-field">
    <label for="address" class="adm-label">Alamat</label>
    <input id="address" name="address" maxlength="255" value="<?= e((string) $data['address']) ?>" class="adm-input">
  </div>

  <div class="adm-grid-2">
    <div class="adm-field">
      <label for="latitude" class="adm-label">Latitude</label>
      <input id="latitude" name="latitude" inputmode="decimal" value="<?= e((string) $data['latitude']) ?>" placeholder="contoh: 1.3667" class="adm-input">
    </div>
    <div class="adm-field">
      <label for="longitude" class="adm-label">Longitude</label>
      <input id="longitude" name="longitude" inputmode="decimal" value="<?= e((string) $data['longitude']) ?>" placeholder="contoh: 109.3000" class="adm-input">
    </div>
  </div>
  <p class="adm-hint" style="margin-top:-.5rem;margin-bottom:.5rem">Opsional. Isi hanya setelah koordinat diperiksa (mis. lewat Google Maps).</p>
  <div class="adm-field">
    <label class="adm-cek"><input type="checkbox" name="is_verified" value="1" <?= (int) $data['is_verified'] === 1 ? 'checked' : '' ?>> Koordinat sudah diverifikasi</label>
  </div>

  <div class="adm-field">
    <label for="image" class="adm-label">Gambar</label>
    <?php if ($gambarLama): ?>
      <p><img src="<?= e(gambar_url($gambarLama)) ?>" alt="Gambar saat ini" class="adm-thumb adm-thumb--besar"></p>
      <label class="adm-cek"><input type="checkbox" name="hapus_gambar" value="1"> Hapus gambar ini</label>
    <?php endif; ?>
    <input id="image" name="image" type="file" accept="image/jpeg,image/png,image/webp" class="adm-input" style="margin-top:.5rem">
    <p class="adm-hint">JPG, PNG, atau WebP, maksimal 2 MB. Kosongkan jika tidak ingin mengganti.</p>
  </div>

  <div class="adm-aksi">
    <button type="submit" class="adm-btn">Simpan</button>
    <a href="<?= e(base_url('admin/wisata/index.php')) ?>" class="adm-btn adm-btn--garis">Batal</a>
  </div>
</form>

<?php require __DIR__ . '/../../includes/admin-footer.php'; ?>
