<?php
require_once __DIR__ . '/../includes/data-contoh.php';

$pageTitle  = 'Sejarah Sambas';
$activePage = 'sejarah';
$judul      = 'Sejarah Sambas';
$subjudul   = 'Catatan perjalanan Sambas dari masa ke masa.';
$data       = contoh_sejarah();

require __DIR__ . '/../includes/header.php';
require __DIR__ . '/../includes/judul-halaman.php';
?>

<main id="isi" class="mx-auto max-w-6xl px-4 pt-10">
  <div class="grid gap-5 md:grid-cols-3">
    <?php foreach ($data as $item) { include __DIR__ . '/../includes/kartu.php'; } ?>
  </div>

  <!-- Tampilan pagination. Fungsinya baru aktif di Tahap 5. -->
  <nav class="mt-10 flex justify-center gap-2" aria-label="Halaman">
    <span class="rounded border border-emas bg-hijau px-3 py-1 text-emas">1</span>
    <span class="rounded border border-emas bg-gading px-3 py-1">2</span>
    <span class="rounded border border-emas bg-gading px-3 py-1">3</span>
  </nav>
</main>

<?php require __DIR__ . '/../includes/footer.php'; ?>