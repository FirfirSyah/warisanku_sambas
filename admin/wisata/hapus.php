<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/admin-helpers.php';
require_login();
wajib_post('admin/wisata/index.php');

$id = (int) ($_POST['id'] ?? 0);
$stmt = db()->prepare('SELECT featured_image AS image FROM destinations WHERE id = :id');
$stmt->execute([':id' => $id]);
$row = $stmt->fetch();

if ($row) {
    db()->prepare('DELETE FROM destinations WHERE id = :id')->execute([':id' => $id]);
    hapus_gambar($row['image']);
    set_flash('success', 'Destinasi dihapus.');
} else {
    set_flash('error', 'Destinasi tidak ditemukan.');
}
redirect('admin/wisata/index.php');
