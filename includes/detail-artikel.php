<?php
// Halaman detail artikel sejarah/budaya. Butuh variabel $jenis ('sejarah' atau 'budaya').
require_once __DIR__ . '/konten.php';

$namaDaftar = ['sejarah' => ['pages/sejarah.php', 'daftar sejarah'], 'budaya' => ['pages/budaya.php', 'daftar budaya']][$jenis];

$id   = (int) ($_GET['id'] ?? 0);
$item = $id > 0 ? ambil_detail($jenis, $id) : null;

if ($item === null) {
    http_response_code(404);
}

$pageTitle  = $item['title'] ?? 'Tidak ditemukan';
$activePage = $jenis;

require __DIR__ . '/header.php';
?>

<main id="isi" class="mx-auto max-w-3xl px-4 pt-10">
  <?php if ($item === null): ?>
    <div class="rounded border border-emas bg-gading p-6 text-center">
      <h1 class="font-serif text-2xl font-bold">Konten tidak ditemukan</h1>
      <a href="<?= e(base_url($namaDaftar[0])) ?>" class="mt-3 inline-block font-semibold text-marun hover:underline">&larr; Kembali ke <?= e($namaDaftar[1]) ?></a>
    </div>
  <?php else: ?>
    <a href="<?= e(base_url($namaDaftar[0])) ?>" class="text-sm font-semibold text-marun hover:underline">&larr; Kembali ke <?= e($namaDaftar[1]) ?></a>
    <span class="mt-4 block text-xs font-semibold uppercase tracking-wide text-marun"><?= e($item['kategori'] ?? ($jenis === 'sejarah' ? 'Sejarah' : 'Budaya')) ?></span>
    <h1 class="mt-1 font-serif text-3xl font-bold"><?= e($item['title']) ?></h1>

    <?php if (!empty($item['image'])): ?>
      <img src="<?= e(gambar_url($item['image'])) ?>" alt="<?= e($item['title']) ?>" class="bingkai-emas mt-6 w-full" style="max-height:26rem;object-fit:cover">
    <?php else: ?>
      <div class="pola-songket bingkai-emas mt-6 flex h-64 items-center justify-center bg-hijau">
        <span class="text-xs uppercase tracking-widest text-emas">Foto dokumentasi menyusul</span>
      </div>
    <?php endif; ?>

    <div class="mt-6 space-y-4 leading-relaxed">
      <p class="font-semibold"><?= e($item['summary']) ?></p>
      <?= render_paragraf($item['content']) ?>
    </div>

    <section class="mt-8 rounded border border-emas bg-gading p-4">
      <h2 class="font-serif text-lg font-bold">Sumber Referensi</h2>
      <?php $daftarRef = render_daftar((string) ($item['reference_text'] ?? '')); ?>
      <?php if ($daftarRef !== ''): ?>
        <?= $daftarRef ?>
      <?php else: ?>
        <p class="mt-1 text-sm text-hijau/80">Sumber belum dicantumkan.</p>
      <?php endif; ?>
    </section>
  <?php endif; ?>
</main>

<?php require __DIR__ . '/footer.php'; ?>
