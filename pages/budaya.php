<?php
require_once __DIR__ . '/../includes/data-contoh.php';

$pageTitle  = 'Budaya dan Tradisi';
$activePage = 'budaya';
$judul      = 'Budaya dan Tradisi';
$subjudul   = 'Adat istiadat, kesenian, musik, kerajinan, dan kuliner khas Sambas.';

$q   = trim($_GET['q'] ?? '');
$kat = trim($_GET['kategori'] ?? '');

$data = array_filter(contoh_budaya(), function (array $d) use ($q, $kat): bool {
    if ($kat !== '' && $d['kategori'] !== $kat) return false;
    if ($q !== '' && stripos($d['judul'] . ' ' . $d['ringkasan'], $q) === false) return false;
    return true;
});

require __DIR__ . '/../includes/header.php';
require __DIR__ . '/../includes/judul-halaman.php';
?>

<main id="isi" class="mx-auto max-w-6xl px-4 pt-10">
  <form method="get" class="flex overflow-hidden rounded border border-emas bg-gading">
    <label for="q" class="sr-only">Cari budaya</label>
    <input id="q" name="q" type="search" value="<?= e($q) ?>" placeholder="Cari budaya..."
           class="w-full bg-gading px-4 py-3 outline-none">
    <?php if ($kat !== ''): ?>
      <input type="hidden" name="kategori" value="<?= e($kat) ?>">
    <?php endif; ?>
    <button type="submit" class="bg-emas px-5 font-semibold hover:bg-krem">Cari</button>
  </form>

  <div class="mt-4 flex flex-wrap gap-2">
    <a href="<?= e(base_url('pages/budaya.php')) ?>"
       class="rounded-full border border-emas px-4 py-1 text-sm <?= $kat === '' ? 'bg-hijau text-emas' : 'bg-gading hover:bg-emas' ?>">Semua</a>
    <?php foreach (kategori_budaya() as $k): ?>
      <a href="?kategori=<?= urlencode($k) ?>"
         class="rounded-full border border-emas px-4 py-1 text-sm <?= $kat === $k ? 'bg-hijau text-emas' : 'bg-gading hover:bg-emas' ?>">
        <?= e($k) ?>
      </a>
    <?php endforeach; ?>
  </div>

  <?php if (!$data): ?>
    <p class="mt-10 rounded border border-emas bg-gading p-6 text-center">Tidak ada hasil yang cocok.</p>
  <?php else: ?>
    <div class="mt-6 grid gap-5 md:grid-cols-3">
      <?php foreach ($data as $item) { include __DIR__ . '/../includes/kartu.php'; } ?>
    </div>
  <?php endif; ?>
</main>

<?php require __DIR__ . '/../includes/footer.php'; ?>