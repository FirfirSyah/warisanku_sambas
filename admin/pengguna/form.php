<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/admin-helpers.php';
require_role('admin');

$id   = (int) ($_GET['id'] ?? 0);
$saya = current_user()['id'];
$data = ['name' => '', 'username' => '', 'email' => '', 'role' => 'editor', 'is_active' => 1];

if ($id > 0) {
    $stmt = db()->prepare('SELECT id, name, username, email, role, is_active FROM users WHERE id = :id');
    $stmt->execute([':id' => $id]);
    $row = $stmt->fetch();
    if (!$row) {
        http_response_code(404);
        exit('Pengguna tidak ditemukan.');
    }
    $data = $row;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    $data['name']     = post_teks('name');
    $data['username'] = post_teks('username');
    $data['email']    = post_teks('email');
    $data['role']     = post_teks('role') === 'admin' ? 'admin' : 'editor';
    $data['is_active'] = isset($_POST['is_active']) ? 1 : 0;
    $password   = (string) ($_POST['password'] ?? '');
    $konfirmasi = (string) ($_POST['konfirmasi'] ?? '');

    // Akun sendiri tidak boleh dinonaktifkan atau diturunkan perannya (supaya tidak terkunci)
    if ($id === $saya) {
        $data['is_active'] = 1;
        $data['role']      = 'admin';
    }

    if (mb_strlen($data['name']) < 2 || mb_strlen($data['name']) > 100) {
        $errors[] = 'Nama harus 2 sampai 100 karakter.';
    }
    if (!preg_match('/^[A-Za-z0-9_]{4,30}$/', $data['username'])) {
        $errors[] = 'Username 4 sampai 30 karakter: huruf, angka, atau garis bawah.';
    }
    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL) || mb_strlen($data['email']) > 120) {
        $errors[] = 'Format email tidak valid.';
    }
    if ($id === 0 || $password !== '') {
        if (strlen($password) < 10) {
            $errors[] = 'Password minimal 10 karakter.';
        }
        if ($password !== $konfirmasi) {
            $errors[] = 'Konfirmasi password tidak sama.';
        }
    }

    if (!$errors) {
        $stmt = db()->prepare('SELECT COUNT(*) FROM users WHERE username = :u AND id <> :id');
        $stmt->execute([':u' => $data['username'], ':id' => $id]);
        if ((int) $stmt->fetchColumn() > 0) {
            $errors[] = 'Username sudah dipakai.';
        }
        $stmt = db()->prepare('SELECT COUNT(*) FROM users WHERE email = :e AND id <> :id');
        $stmt->execute([':e' => $data['email'], ':id' => $id]);
        if ((int) $stmt->fetchColumn() > 0) {
            $errors[] = 'Email sudah dipakai.';
        }
    }

    // Harus selalu ada minimal satu admin aktif
    if (!$errors && $id > 0 && ($data['role'] !== 'admin' || (int) $data['is_active'] !== 1)) {
        $stmt = db()->prepare("SELECT COUNT(*) FROM users WHERE role = 'admin' AND is_active = 1 AND id <> :id");
        $stmt->execute([':id' => $id]);
        if ((int) $stmt->fetchColumn() === 0) {
            $errors[] = 'Harus ada minimal satu admin aktif.';
        }
    }

    if (!$errors) {
        try {
            if ($id > 0) {
                $sql = 'UPDATE users SET name = :n, username = :u, email = :e, role = :r, is_active = :a'
                     . ($password !== '' ? ', password_hash = :h' : '') . ' WHERE id = :id';
                $params = [
                    ':n' => $data['name'], ':u' => $data['username'], ':e' => $data['email'],
                    ':r' => $data['role'], ':a' => $data['is_active'], ':id' => $id,
                ];
                if ($password !== '') {
                    $params[':h'] = password_hash($password, PASSWORD_DEFAULT);
                }
                db()->prepare($sql)->execute($params);
            } else {
                db()->prepare(
                    'INSERT INTO users (name, username, email, password_hash, role, is_active) VALUES (:n, :u, :e, :h, :r, :a)'
                )->execute([
                    ':n' => $data['name'], ':u' => $data['username'], ':e' => $data['email'],
                    ':h' => password_hash($password, PASSWORD_DEFAULT), ':r' => $data['role'], ':a' => $data['is_active'],
                ]);
            }
            set_flash('success', 'Pengguna berhasil disimpan.');
            redirect('admin/pengguna/index.php');
        } catch (PDOException $ex) {
            error_log('Simpan pengguna gagal: ' . $ex->getMessage());
            $errors[] = 'Data gagal disimpan. Coba lagi.';
        }
    }
}

$adminTitle  = ($id > 0 ? 'Ubah' : 'Tambah') . ' Pengguna';
$adminActive = 'pengguna';
require __DIR__ . '/../../includes/admin-header.php';
?>
<?php if ($errors): ?>
  <ul class="adm-errors" role="alert"><?php foreach ($errors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?></ul>
<?php endif; ?>

<form method="post" class="adm-kartu" style="max-width:36rem" autocomplete="off">
  <?= csrf_field() ?>

  <div class="adm-field">
    <label for="name" class="adm-label">Nama lengkap</label>
    <input id="name" name="name" required maxlength="100" value="<?= e($data['name']) ?>" class="adm-input">
  </div>
  <div class="adm-field">
    <label for="username" class="adm-label">Username</label>
    <input id="username" name="username" required maxlength="30" value="<?= e($data['username']) ?>" class="adm-input">
  </div>
  <div class="adm-field">
    <label for="email" class="adm-label">Email</label>
    <input id="email" name="email" type="email" required maxlength="120" value="<?= e($data['email']) ?>" class="adm-input">
  </div>

  <div class="adm-field">
    <label for="role" class="adm-label">Peran</label>
    <select id="role" name="role" class="adm-select" <?= $id === $saya ? 'disabled' : '' ?>>
      <option value="editor" <?= $data['role'] === 'editor' ? 'selected' : '' ?>>Editor (kelola konten)</option>
      <option value="admin" <?= $data['role'] === 'admin' ? 'selected' : '' ?>>Admin (kelola konten dan pengguna)</option>
    </select>
    <?php if ($id === $saya): ?><p class="adm-hint">Anda tidak bisa mengubah peran akun sendiri.</p><?php endif; ?>
  </div>

  <div class="adm-field">
    <label class="adm-cek"><input type="checkbox" name="is_active" value="1" <?= (int) $data['is_active'] === 1 ? 'checked' : '' ?> <?= $id === $saya ? 'disabled' : '' ?>> Akun aktif</label>
  </div>

  <div class="adm-field">
    <label for="password" class="adm-label">Password <?= $id > 0 ? '(kosongkan jika tidak diganti)' : '' ?></label>
    <input id="password" name="password" type="password" <?= $id > 0 ? '' : 'required' ?> minlength="10" autocomplete="new-password" class="adm-input">
    <p class="adm-hint">Minimal 10 karakter.</p>
  </div>
  <div class="adm-field">
    <label for="konfirmasi" class="adm-label">Ulangi password</label>
    <input id="konfirmasi" name="konfirmasi" type="password" <?= $id > 0 ? '' : 'required' ?> autocomplete="new-password" class="adm-input">
  </div>

  <div class="adm-aksi">
    <button type="submit" class="adm-btn">Simpan</button>
    <a href="<?= e(base_url('admin/pengguna/index.php')) ?>" class="adm-btn adm-btn--garis">Batal</a>
  </div>
</form>

<?php require __DIR__ . '/../../includes/admin-footer.php'; ?>
