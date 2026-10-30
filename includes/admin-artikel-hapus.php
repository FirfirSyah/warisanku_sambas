<?php
// Hapus artikel (hanya lewat POST + CSRF). Butuh variabel $tipe dari file pemanggil.
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/admin-helpers.php';
require_login();

$cfg = cfg_artikel($tipe);
wajib_post($cfg['folder'] . '/index.php');

$id = (int) ($_POST['id'] ?? 0);
$stmt = db()->prepare('SELECT image FROM articles WHERE id = :id AND type = :t');
$stmt->execute([':id' => $id, ':t' => $tipe]);
$row = $stmt->fetch();

if ($row) {
    $del = db()->prepare('DELETE FROM articles WHERE id = :id AND type = :t');
    $del->execute([':id' => $id, ':t' => $tipe]);
    hapus_gambar($row['image']);
    set_flash('success', 'Artikel dihapus.');
} else {
    set_flash('error', 'Artikel tidak ditemukan.');
}
redirect($cfg['folder'] . '/index.php');
