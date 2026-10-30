<?php
require_once __DIR__ . '/../includes/data-contoh.php';

$id   = (int) ($_GET['id'] ?? 0);
$item = null;
foreach (contoh_wisata() as $d) {
    if ($d['id'] === $id) { $item = $d; break; }
}

if ($item === null) {
    http_response_code(404);
}

$pageTitle  = $item['judul'] ?? 'Tidak ditemukan';
$activePage = 'wisata';

require __DIR__ . '/../includes/header.php';
?>

<main id="isi" class="mx-auto max-w-3xl px-4 pt-10">
  <?php if ($item === null): ?>
    <div class="rounded border border-emas bg-gading p-6 text-center">
      <h1 class="font-serif text-2xl font-bold">Destinasi tidak ditemukan</h1>
      <a href="<?= e(base_url('pages/wisata.php')) ?>" class="mt-3 inline-block font-semibold text-marun hover:underline">&larr; Kembali ke daftar wisata</a>
    </div>
  <?php else: ?>
    <a href="<?= e(base_url('pages/wisata.php')) ?>" class="text-sm font-semibold text-marun hover:underline">&larr; Kembali ke daftar wisata</a>
    <span class="mt-4 block text-xs font-semibold uppercase tracking-wide text-marun"><?= e($item['kategori']) ?></span>
    <h1 class="mt-1 font-serif text-3xl font-bold"><?= e($item['judul']) ?></h1>

    <div class="pola-songket bingkai-emas mt-6 flex h-64 items-center justify-center bg-hijau">
      <span class="text-xs uppercase tracking-widest text-emas">Foto destinasi menyusul</span>
    </div>

    <div class="mt-6 space-y-4 leading-relaxed">
      <p><?= e($item['ringkasan']) ?></p>
      <p>Deskripsi lengkap akan ditampilkan di sini setelah dimasukkan lewat dashboard admin.</p>
    </div>

    <section class="mt-8 rounded border border-emas bg-gading p-4">
      <h2 class="font-serif text-lg font-bold">Informasi Lokasi</h2>
      <p class="mt-1 text-sm text-hijau/80">Alamat dan koordinat akan ditampilkan di sini setelah diverifikasi.</p>
    </section>
  <?php endif; ?>
</main>

<?php require __DIR__ . '/../includes/footer.php'; ?>