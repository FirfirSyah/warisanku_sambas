<?php
// Form tambah/ubah artikel. Butuh variabel $tipe ('sejarah' atau 'budaya') dari file pemanggil.
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/admin-helpers.php';
require_login();

$cfg = cfg_artikel($tipe);
$id  = (int) ($_GET['id'] ?? 0);

$data = [
    'title' => '', 'category_id' => '', 'summary' => '', 'content' => '',
    'reference_text' => '', 'status' => 'draft', 'image' => null,
];
$gambarLama = null;

if ($id > 0) {
    $stmt = db()->prepare('SELECT * FROM articles WHERE id = :id AND type = :t');
    $stmt->execute([':id' => $id, ':t' => $tipe]);
    $row = $stmt->fetch();
    if (!$row) {
        http_response_code(404);
        exit('Artikel tidak ditemukan.');
    }
    $data       = array_merge($data, $row);
    $gambarLama = $row['image'];
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    $data['title']          = post_teks('title');
    $data['summary']        = post_teks('summary');
    $data['content']        = post_teks('content');
    $data['reference_text'] = post_teks('reference_text');
    $data['status']         = post_teks('status') === 'published' ? 'published' : 'draft';
    $kategoriId = $cfg['kategori'] ? kategori_valid($_POST['category_id'] ?? 0, 'budaya') : null;
    $data['category_id'] = $kategoriId ?? '';

    if (mb_strlen($data['title']) < 3 || mb_strlen($data['title']) > 200) {
        $errors[] = 'Judul harus 3 sampai 200 karakter.';
    }
    if (mb_strlen($data['summary']) < 10 || mb_strlen($data['summary']) > 300) {
        $errors[] = 'Ringkasan harus 10 sampai 300 karakter.';
    }
    if (mb_strlen($data['content']) < 20) {
        $errors[] = 'Isi artikel minimal 20 karakter.';
    }
    if ($cfg['kategori'] && $kategoriId === null) {
        $errors[] = 'Pilih kategori.';
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

        try {
            if ($id > 0) {
                $stmt = db()->prepare(
                    'UPDATE articles SET category_id = :cat, title = :title, summary = :sum, content = :isi,
                            reference_text = :ref, image = :img, status = :st
                      WHERE id = :id AND type = :t'
                );
                $stmt->execute([
                    ':cat' => $kategoriId, ':title' => $data['title'], ':sum' => $data['summary'],
                    ':isi' => $data['content'], ':ref' => $data['reference_text'] !== '' ? $data['reference_text'] : null,
                    ':img' => $gambarSimpan, ':st' => $data['status'], ':id' => $id, ':t' => $tipe,
                ]);
            } else {
                $stmt = db()->prepare(
                    'INSERT INTO articles (type, category_id, title, slug, summary, content, reference_text, image, status, author_id)
                     VALUES (:t, :cat, :title, :slug, :sum, :isi, :ref, :img, :st, :au)'
                );
                $stmt->execute([
                    ':t' => $tipe, ':cat' => $kategoriId, ':title' => $data['title'],
                    ':slug' => slug_unik('articles', $data['title']), ':sum' => $data['summary'],
                    ':isi' => $data['content'], ':ref' => $data['reference_text'] !== '' ? $data['reference_text'] : null,
                    ':img' => $gambarSimpan, ':st' => $data['status'], ':au' => current_user()['id'],
                ]);
            }
            if ($gambarSimpan !== $gambarLama) {
                hapus_gambar($gambarLama);
            }
            set_flash('success', 'Artikel berhasil disimpan.');
            redirect($cfg['folder'] . '/index.php');
        } catch (PDOException $ex) {
            error_log('Simpan artikel gagal: ' . $ex->getMessage());
            hapus_gambar($gambarBaru);
            $errors[] = 'Data gagal disimpan. Coba lagi.';
        }
    }
}

$tampilGambar = $gambarLama;
$kategori     = $cfg['kategori'] ? daftar_kategori('budaya') : [];

$adminTitle  = ($id > 0 ? 'Ubah' : 'Tambah') . ' Artikel ' . $cfg['label'];
$adminActive = $tipe;
require __DIR__ . '/admin-header.php';
?>
<?php if ($errors): ?>
  <ul class="adm-errors" role="alert"><?php foreach ($errors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?></ul>
<?php endif; ?>

<form method="post" enctype="multipart/form-data" class="adm-kartu" style="max-width:52rem">
  <?= csrf_field() ?>

  <div class="adm-field">
    <label for="title" class="adm-label">Judul</label>
    <input id="title" name="title" required maxlength="200" value="<?= e($data['title']) ?>" class="adm-input">
  </div>

  <div class="adm-grid-2">
    <?php if ($cfg['kategori']): ?>
      <div class="adm-field">
        <label for="category_id" class="adm-label">Kategori</label>
        <select id="category_id" name="category_id" required class="adm-select">
          <option value="">- Pilih kategori -</option>
          <?php foreach ($kategori as $k): ?>
            <option value="<?= (int) $k['id'] ?>" <?= (string) $data['category_id'] === (string) $k['id'] ? 'selected' : '' ?>><?= e($k['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
    <?php endif; ?>
    <div class="adm-field">
      <label for="status" class="adm-label">Status</label>
      <select id="status" name="status" class="adm-select">
        <option value="draft" <?= $data['status'] === 'draft' ? 'selected' : '' ?>>Draf (belum tampil di situs)</option>
        <option value="published" <?= $data['status'] === 'published' ? 'selected' : '' ?>>Terbit</option>
      </select>
    </div>
  </div>

  <div class="adm-field">
    <label for="summary" class="adm-label">Ringkasan</label>
    <textarea id="summary" name="summary" required maxlength="300" class="adm-textarea" style="min-height:4.5rem"><?= e($data['summary']) ?></textarea>
    <p class="adm-hint">10 sampai 300 karakter. Tampil di kartu daftar.</p>
  </div>

  <div class="adm-field">
    <label for="content" class="adm-label">Isi artikel</label>
    <textarea id="content" name="content" required class="adm-textarea adm-textarea--besar"><?= e($data['content']) ?></textarea>
    <p class="adm-hint">Pisahkan paragraf dengan satu baris kosong.</p>
  </div>

  <div class="adm-field">
    <label for="reference_text" class="adm-label">Sumber referensi</label>
    <textarea id="reference_text" name="reference_text" class="adm-textarea"><?= e((string) $data['reference_text']) ?></textarea>
    <p class="adm-hint">Satu sumber per baris (buku, jurnal, narasumber, atau tautan).</p>
  </div>

  <div class="adm-field">
    <label for="image" class="adm-label">Gambar</label>
    <?php if ($tampilGambar): ?>
      <p><img src="<?= e(gambar_url($tampilGambar)) ?>" alt="Gambar saat ini" class="adm-thumb adm-thumb--besar"></p>
      <label class="adm-cek"><input type="checkbox" name="hapus_gambar" value="1"> Hapus gambar ini</label>
    <?php endif; ?>
    <input id="image" name="image" type="file" accept="image/jpeg,image/png,image/webp" class="adm-input" style="margin-top:.5rem">
    <p class="adm-hint">JPG, PNG, atau WebP, maksimal 2 MB. Kosongkan jika tidak ingin mengganti.</p>
  </div>

  <div class="adm-aksi">
    <button type="submit" class="adm-btn">Simpan</button>
    <a href="<?= e(base_url($cfg['folder'] . '/index.php')) ?>" class="adm-btn adm-btn--garis">Batal</a>
  </div>
</form>

<?php require __DIR__ . '/admin-footer.php'; ?>
