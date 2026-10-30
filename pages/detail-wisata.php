<?php
require_once __DIR__ . '/../includes/konten.php';

$id   = (int) ($_GET['id'] ?? 0);
$item = $id > 0 ? ambil_detail('wisata', $id) : null;

if ($item === null) {
    http_response_code(404);
}

$pageTitle  = $item['title'] ?? 'Tidak ditemukan';
$activePage = 'wisata';

$punyaKoordinat = $item !== null && $item['latitude'] !== null && $item['longitude'] !== null;

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
    <span class="mt-4 block text-xs font-semibold uppercase tracking-wide text-marun"><?= e($item['kategori'] ?? 'Wisata') ?></span>
    <h1 class="mt-1 font-serif text-3xl font-bold"><?= e($item['title']) ?></h1>

    <?php if (!empty($item['image'])): ?>
      <img src="<?= e(gambar_url($item['image'])) ?>" alt="<?= e($item['title']) ?>" class="bingkai-emas mt-6 w-full" style="max-height:26rem;object-fit:cover">
    <?php else: ?>
      <div class="pola-songket bingkai-emas mt-6 flex h-64 items-center justify-center bg-hijau">
        <span class="text-xs uppercase tracking-widest text-emas">Foto destinasi menyusul</span>
      </div>
    <?php endif; ?>

    <div class="mt-6 space-y-4 leading-relaxed">
      <?= render_paragraf($item['description']) ?>
    </div>

    <section class="mt-8 rounded border border-emas bg-gading p-4">
      <h2 class="font-serif text-lg font-bold">Informasi Lokasi</h2>
      <?php if (!empty($item['address']) || $punyaKoordinat): ?>
        <?php if (!empty($item['address'])): ?>
          <p class="mt-1 text-sm"><?= e($item['address']) ?></p>
        <?php endif; ?>
        <?php if ($punyaKoordinat): ?>
          <p class="mt-1 text-sm text-hijau/80">Koordinat: <?= e((string) (float) $item['latitude']) ?>, <?= e((string) (float) $item['longitude']) ?></p>
          <a href="<?= e('https://www.google.com/maps?q=' . (float) $item['latitude'] . ',' . (float) $item['longitude']) ?>"
             target="_blank" rel="noopener noreferrer" class="mt-2 inline-block text-sm font-semibold text-marun hover:underline">Buka di Google Maps &rarr;</a>
        <?php endif; ?>
      <?php else: ?>
        <p class="mt-1 text-sm text-hijau/80">Alamat dan koordinat belum dicantumkan.</p>
      <?php endif; ?>
    </section>
  <?php endif; ?>
</main>

<?php require __DIR__ . '/../includes/footer.php'; ?>
