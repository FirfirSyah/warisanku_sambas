<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/admin-helpers.php';
require_login();
wajib_post('admin/kategori/index.php');

$id = (int) ($_POST['id'] ?? 0);

$stmt = db()->prepare(
    'SELECT (SELECT COUNT(*) FROM articles WHERE category_id = :a)
          + (SELECT COUNT(*) FROM destinations WHERE category_id = :d)'
);
$stmt->execute([':a' => $id, ':d' => $id]);
$dipakai = (int) $stmt->fetchColumn();

if ($dipakai > 0) {
    set_flash('error', "Kategori masih dipakai oleh $dipakai konten. Pindahkan kontennya ke kategori lain dulu.");
} else {
    $del = db()->prepare('DELETE FROM categories WHERE id = :id');
    $del->execute([':id' => $id]);
    set_flash($del->rowCount() ? 'success' : 'error', $del->rowCount() ? 'Kategori dihapus.' : 'Kategori tidak ditemukan.');
}
redirect('admin/kategori/index.php');
