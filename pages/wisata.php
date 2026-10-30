<?php
require_once __DIR__ . '/../includes/konten.php';

$pageTitle  = 'Wisata';
$activePage = 'wisata';
$judul      = 'Destinasi Wisata';
$subjudul   = 'Tempat-tempat yang bisa dikunjungi di Kabupaten Sambas.';

$q   = trim((string) ($_GET['q'] ?? ''));
$kat = trim((string) ($_GET['kategori'] ?? ''));
$hal = max(1, (int) ($_GET['page'] ?? 1));

$daftarKategori = nama_kategori('wisata');
if ($kat !== '' && !in_array($kat, $daftarKategori, true)) {
    $kat = '';
}

$hasil = cari_konten('wisata', $q, $kat, $hal);
$data  = $hasil['items'];

require __DIR__ . '/../includes/header.php';
require __DIR__ . '/../includes/judul-halaman.php';
?>

<main id="isi" class="mx-auto max-w-6xl px-4 pt-10">
  <form method="get" class="flex overflow-hidden rounded border border-emas bg-gading">
    <label for="q" class="sr-only">Cari destinasi</label>
    <input id="q" name="q" type="search" value="<?= e($q) ?>" placeholder="Cari destinasi..."
           class="w-full bg-gading px-4 py-3 outline-none">
    <?php if ($kat !== ''): ?>
      <input type="hidden" name="kategori" value="<?= e($kat) ?>">
    <?php endif; ?>
    <button type="submit" class="bg-emas px-5 font-semibold hover:bg-krem">Cari</button>
  </form>

  <div class="mt-4 flex flex-wrap gap-2">
    <a href="<?= e(base_url('pages/wisata.php')) ?>"
       class="rounded-full border border-emas px-4 py-1 text-sm <?= $kat === '' ? 'bg-hijau text-emas' : 'bg-gading hover:bg-emas' ?>">Semua</a>
    <?php foreach ($daftarKategori as $k): ?>
      <a href="?kategori=<?= urlencode($k) ?>"
         class="rounded-full border border-emas px-4 py-1 text-sm <?= $kat === $k ? 'bg-hijau text-emas' : 'bg-gading hover:bg-emas' ?>">
        <?= e($k) ?>
      </a>
    <?php endforeach; ?>
  </div>

  <?php if (!$data): ?>
    <p class="mt-10 rounded border border-emas bg-gading p-6 text-center">
      <?= ($q !== '' || $kat !== '') ? 'Tidak ada hasil yang cocok.' : 'Belum ada konten yang diterbitkan.' ?>
    </p>
  <?php else: ?>
    <div class="mt-6 grid gap-5 md:grid-cols-3">
      <?php foreach ($data as $item) { include __DIR__ . '/../includes/kartu.php'; } ?>
    </div>
    <?php pager_publik($hasil['total'], $hal, PUBLIK_PER_HALAMAN, ['q' => $q, 'kategori' => $kat]); ?>
  <?php endif; ?>
</main>

<?php require __DIR__ . '/../includes/footer.php'; ?>
