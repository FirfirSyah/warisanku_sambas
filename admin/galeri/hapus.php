<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/admin-helpers.php';
require_login();
wajib_post('admin/galeri/index.php');

$id = (int) ($_POST['id'] ?? 0);
$stmt = db()->prepare('SELECT image_path AS image FROM galleries WHERE id = :id');
$stmt->execute([':id' => $id]);
$row = $stmt->fetch();

if ($row) {
    db()->prepare('DELETE FROM galleries WHERE id = :id')->execute([':id' => $id]);
    hapus_gambar($row['image']);
    set_flash('success', 'Foto dihapus.');
} else {
    set_flash('error', 'Foto tidak ditemukan.');
}
redirect('admin/galeri/index.php');
