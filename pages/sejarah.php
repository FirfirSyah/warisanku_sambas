<?php
require_once __DIR__ . '/../includes/konten.php';

$pageTitle  = 'Sejarah Sambas';
$activePage = 'sejarah';
$judul      = 'Sejarah Sambas';
$subjudul   = 'Catatan perjalanan Sambas dari masa ke masa.';

$q   = trim((string) ($_GET['q'] ?? ''));
$kat = trim((string) ($_GET['kategori'] ?? ''));
$hal = max(1, (int) ($_GET['page'] ?? 1));

$daftarKategori = [];
if ($kat !== '' && !in_array($kat, $daftarKategori, true)) {
    $kat = '';
}

$hasil = cari_konten('sejarah', $q, $kat, $hal);
$data  = $hasil['items'];

require __DIR__ . '/../includes/header.php';
require __DIR__ . '/../includes/judul-halaman.php';
?>

<main id="isi" class="mx-auto max-w-6xl px-4 pt-10">
  <form method="get" class="flex overflow-hidden rounded border border-emas bg-gading">
    <label for="q" class="sr-only">Cari artikel sejarah</label>
    <input id="q" name="q" type="search" value="<?= e($q) ?>" placeholder="Cari artikel sejarah..."
           class="w-full bg-gading px-4 py-3 outline-none">
    <?php if ($kat !== ''): ?>
      <input type="hidden" name="kategori" value="<?= e($kat) ?>">
    <?php endif; ?>
    <button type="submit" class="bg-emas px-5 font-semibold hover:bg-krem">Cari</button>
  </form>

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
