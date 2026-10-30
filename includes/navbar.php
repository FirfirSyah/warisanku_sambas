<?php
$menu = [
    'beranda' => ['Beranda', 'public/index.php'],
    'sejarah' => ['Sejarah', 'pages/sejarah.php'],
    'budaya'  => ['Budaya', 'pages/budaya.php'],
    'wisata'  => ['Wisata', 'pages/wisata.php'],
    'galeri'  => ['Galeri', 'pages/galeri.php'],
    'tentang' => ['Tentang', 'pages/tentang.php'],
];
?>
<header class="nav-utama sticky top-0 z-50 border-b-4 border-emas bg-hijau text-gading shadow">
  <nav class="mx-auto flex max-w-6xl items-center justify-between px-4 py-3" aria-label="Navigasi utama">
    <a href="<?= e(base_url('public/index.php')) ?>" class="font-serif text-xl font-bold tracking-wide text-emas">
      WARISANKU <span class="text-gading">SAMBAS</span>
    </a>

    <ul class="hidden items-center gap-6 md:flex">
      <?php foreach ($menu as $kunci => [$label, $url]): ?>
        <li>
          <a href="<?= e(base_url($url)) ?>"
             class="tautan-nav pb-1 hover:text-emas <?= $activePage === $kunci ? 'aktif text-emas' : '' ?>">
            <?= e($label) ?>
          </a>
        </li>
      <?php endforeach; ?>
    </ul>

    <button id="menu-btn" type="button" class="rounded border border-emas p-2 md:hidden"
            aria-controls="menu-mobile" aria-expanded="false" aria-label="Buka menu">
      <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" d="M4 6h16M4 12h16M4 18h16"/>
      </svg>
    </button>
  </nav>

  <ul id="menu-mobile" class="menu-mobile bg-hijau md:hidden">
    <?php foreach ($menu as $kunci => [$label, $url]): ?>
      <li>
        <a href="<?= e(base_url($url)) ?>"
           class="block py-2 hover:text-emas <?= $activePage === $kunci ? 'text-emas' : '' ?>">
          <?= e($label) ?>
        </a>
      </li>
    <?php endforeach; ?>
  </ul>
</header>