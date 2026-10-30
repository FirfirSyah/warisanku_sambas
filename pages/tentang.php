<?php
require_once __DIR__ . '/../includes/functions.php';

$pageTitle  = 'Tentang';
$activePage = 'tentang';
$judul      = 'Tentang ' . SITE_NAME;
$subjudul   = 'Platform digital untuk mengenal dan melestarikan warisan Sambas.';

require __DIR__ . '/../includes/header.php';
require __DIR__ . '/../includes/judul-halaman.php';
?>

<main id="isi" class="mx-auto max-w-3xl space-y-6 px-4 pt-10">
  <section class="rounded border border-emas bg-gading p-5">
    <h2 class="font-serif text-xl font-bold">Profil Website</h2>
    <p class="mt-2">
      <?= e(SITE_NAME) ?> adalah website informasi budaya, sejarah, tradisi, kesenian, kuliner,
      dan destinasi wisata Kabupaten Sambas, Kalimantan Barat.
    </p>
  </section>

  <section class="rounded border border-emas bg-gading p-5">
    <h2 class="font-serif text-xl font-bold">Tujuan Pelestarian Budaya</h2>
    <p class="mt-2">Mendokumentasikan dan memperkenalkan warisan budaya Sambas agar mudah diakses dan dikenal generasi sekarang maupun yang akan datang.</p>
  </section>

  <section class="rounded border border-emas bg-gading p-5">
    <h2 class="font-serif text-xl font-bold">Pengelola</h2>
    <p class="mt-2">[Isi nama pengelola, instansi atau kampus, dan kontak di sini.]</p>
  </section>

  <section class="rounded border border-emas bg-gading p-5">
    <h2 class="font-serif text-xl font-bold">Sumber Informasi</h2>
    <p class="mt-2">[Isi daftar sumber yang kamu pakai, misalnya buku, jurnal, atau narasumber, di sini.]</p>
  </section>
</main>

<?php require __DIR__ . '/../includes/footer.php'; ?>