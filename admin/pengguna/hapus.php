<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/admin-helpers.php';
require_role('admin');
wajib_post('admin/pengguna/index.php');

$id = (int) ($_POST['id'] ?? 0);

if ($id === current_user()['id']) {
    set_flash('error', 'Anda tidak bisa menghapus akun sendiri.');
    redirect('admin/pengguna/index.php');
}

$stmt = db()->prepare('SELECT role, is_active FROM users WHERE id = :id');
$stmt->execute([':id' => $id]);
$u = $stmt->fetch();

if (!$u) {
    set_flash('error', 'Pengguna tidak ditemukan.');
} else {
    // Jangan sampai admin aktif terakhir terhapus
    $stmt = db()->prepare("SELECT COUNT(*) FROM users WHERE role = 'admin' AND is_active = 1 AND id <> :id");
    $stmt->execute([':id' => $id]);
    if ($u['role'] === 'admin' && (int) $stmt->fetchColumn() === 0) {
        set_flash('error', 'Harus ada minimal satu admin aktif.');
    } else {
        db()->prepare('DELETE FROM users WHERE id = :id')->execute([':id' => $id]);
        set_flash('success', 'Pengguna dihapus.');
    }
}
redirect('admin/pengguna/index.php');
