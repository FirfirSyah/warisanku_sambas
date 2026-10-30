<?php
require_once __DIR__ . '/../includes/konten.php';

$pageTitle  = 'Galeri';
$activePage = 'galeri';
$judul      = 'Galeri Foto';
$subjudul   = 'Dokumentasi visual budaya dan wisata Sambas.';
$data       = ambil_galeri();

require __DIR__ . '/../includes/header.php';
require __DIR__ . '/../includes/judul-halaman.php';
?>

<main id="isi" class="mx-auto max-w-6xl px-4 pt-10">
  <?php if (!$data): ?>
    <p class="rounded border border-emas bg-gading p-6 text-center">Belum ada foto di galeri.</p>
  <?php else: ?>
  <div class="flex flex-wrap gap-2" id="filter-galeri">
    <button type="button" data-filter="semua" aria-pressed="true" class="rounded-full border border-emas bg-hijau px-4 py-1 text-sm text-emas">Semua</button>
    <button type="button" data-filter="Budaya" aria-pressed="false" class="rounded-full border border-emas bg-gading px-4 py-1 text-sm hover:bg-emas">Budaya</button>
    <button type="button" data-filter="Wisata" aria-pressed="false" class="rounded-full border border-emas bg-gading px-4 py-1 text-sm hover:bg-emas">Wisata</button>
  </div>

  <div class="mt-6 grid grid-cols-2 gap-4 md:grid-cols-4">
    <?php foreach ($data as $f): ?>
      <button type="button"
              class="item-galeri pola-songket flex aspect-square items-center justify-center rounded border border-emas bg-hijau text-emas hover:opacity-90"
              style="background-image:url('<?= e(gambar_url($f['image'])) ?>');background-size:cover;background-position:center"
              data-kategori="<?= e($f['category']) ?>"
              data-judul="<?= e($f['title']) ?>"
              data-deskripsi="<?= e($f['description']) ?>"
              data-sumber="<?= e($f['source']) ?>"
              data-gambar="<?= e(gambar_url($f['image'])) ?>" aria-label="Lihat <?= e($f['title']) ?>" aria-haspopup="dialog">
        <span class="text-xs"><?= e($f['title']) ?></span>
      </button>
    <?php endforeach; ?>
  </div>
  <p id="galeri-kosong" class="mt-5 hidden rounded-xl border border-emas bg-gading p-5 text-center text-sm" role="status">Belum ada foto pada kategori ini.</p>
  <?php endif; ?>
</main>

<!-- Modal -->
<div id="modal-galeri" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/70 p-4" role="dialog" aria-modal="true" aria-labelledby="modal-judul" aria-describedby="modal-deskripsi">
  <div class="modal-kartu w-full max-w-lg rounded-lg bg-gading p-5">
    <img id="modal-gambar" src="" alt="" class="w-full rounded bg-hijau" style="max-height:20rem;object-fit:contain">
    <h2 id="modal-judul" class="mt-4 font-serif text-xl font-bold"></h2>
    <p id="modal-deskripsi" class="mt-1 text-sm"></p>
    <p class="mt-2 text-xs text-hijau/70">Sumber: <span id="modal-sumber"></span></p>
    <button type="button" id="modal-tutup" class="tombol-emas mt-4">Tutup</button>
  </div>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
