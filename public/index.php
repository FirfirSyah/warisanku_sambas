<?php
require_once __DIR__ . '/../includes/functions.php';

$pageTitle  = 'Beranda';
$activePage = 'beranda';

// Data contoh ditandai secara jelas agar tidak dianggap sebagai informasi sejarah faktual.
$kategori = [
    ['nama' => 'Adat Istiadat', 'kode' => '01', 'deskripsi' => 'Nilai dan tata cara kehidupan'],
    ['nama' => 'Tradisi', 'kode' => '02', 'deskripsi' => 'Ritual dan perayaan masyarakat'],
    ['nama' => 'Kesenian', 'kode' => '03', 'deskripsi' => 'Ekspresi seni dan pertunjukan'],
    ['nama' => 'Musik', 'kode' => '04', 'deskripsi' => 'Irama dan bunyi tradisional'],
    ['nama' => 'Kerajinan', 'kode' => '05', 'deskripsi' => 'Karya dan keterampilan lokal'],
    ['nama' => 'Kuliner', 'kode' => '06', 'deskripsi' => 'Cita rasa khas Sambas'],
];

$sejarah = [];
$wisata  = [];
for ($i = 1; $i <= 3; $i++) {
    $sejarah[] = [
        'judul'     => "Artikel Sejarah Pilihan $i",
        'ringkasan' => 'Ringkasan artikel akan ditampilkan setelah konten terverifikasi ditambahkan melalui pengelolaan website.',
        'kategori'  => 'Contoh konten',
        'url'       => base_url('pages/sejarah.php'),
    ];
    $wisata[] = [
        'judul'     => "Destinasi Pilihan $i",
        'ringkasan' => 'Informasi lokasi, akses, dan daya tarik akan ditampilkan setelah data destinasi dilengkapi.',
        'kategori'  => 'Contoh konten',
        'url'       => base_url('pages/wisata.php'),
    ];
}

require __DIR__ . '/../includes/header.php';
?>

<!-- HERO: editorial, berfokus pada identitas budaya Sambas -->
<section class="hero-premium hero-hias pola-songket bg-hijau text-gading">
  <video class="hero-background-video" poster="<?= e(asset('images/hero-sambas-poster.jpg')) ?>" autoplay muted loop playsinline preload="metadata" aria-hidden="true" tabindex="-1">
    <source src="<?= e(asset('videos/hero-sambas.mp4')) ?>" type="video/mp4">
  </video>
  <div class="hero-background-overlay" aria-hidden="true"></div>
  <div class="hero-premium__inner mx-auto grid max-w-6xl items-center gap-10 px-4 py-14 md:grid-cols-[1.08fr_.92fr] md:py-20 lg:gap-14">
    <div class="hero-premium__copy">
      <p class="hero-eyebrow"><span class="hero-eyebrow__dot"></span> Kabupaten Sambas · Kalimantan Barat</p>
      <h1 class="mt-5 font-serif text-4xl font-bold leading-[1.08] md:text-5xl lg:text-6xl">
        Cerita yang menjaga <span class="kilau block">warisan kita.</span>
      </h1>
      <p class="mt-5 max-w-xl text-base leading-8 text-gading/80 md:text-lg">
        Ruang digital untuk mengenal sejarah, budaya, tradisi, dan tempat istimewa di Sambas—agar kisahnya terus hidup dan dapat diwariskan.
      </p>
      <form action="<?= e(base_url('pages/budaya.php')) ?>" method="get" class="hero-search mt-7 flex max-w-xl overflow-hidden rounded-xl bg-gading">
        <label for="q" class="sr-only">Cari budaya atau wisata</label>
        <span class="hero-search__icon" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="10.8" cy="10.8" r="6.8"/><path d="m16 16 4.5 4.5"/></svg>
        </span>
        <input id="q" name="q" type="search" placeholder="Cari budaya, sejarah, atau wisata..." class="min-w-0 w-full bg-transparent px-1 py-4 text-hijau outline-none">
        <button type="submit" class="hero-search__button">Cari <span aria-hidden="true">↗</span></button>
      </form>
      <div class="mt-6 flex flex-wrap items-center gap-3">
        <a href="<?= e(base_url('pages/budaya.php')) ?>" class="tombol-emas">Jelajahi budaya <span class="panah" aria-hidden="true">→</span></a>
        <a href="<?= e(base_url('pages/wisata.php')) ?>" class="tombol-garis">Temukan wisata</a>
      </div>
      <p class="hero-note mt-6"><span aria-hidden="true">✳</span> Kenali. Hargai. Lestarikan.</p>
    </div>

    <div class="hero-art" aria-label="Ilustrasi dekoratif terinspirasi motif tradisional" role="img">
      <div class="hero-art__orbit hero-art__orbit--one"></div>
      <div class="hero-art__orbit hero-art__orbit--two"></div>
      <div class="hero-art__panel">
        <div class="hero-art__topline"><span>ARSIP BUDAYA</span><span>01 / SAMBAS</span></div>
        <div class="hero-art__motif" aria-hidden="true">
          <svg viewBox="0 0 320 320" fill="none">
            <path d="M160 18 282 160 160 302 38 160 160 18Z" stroke="currentColor" stroke-width="1.4"/>
            <path d="M160 48 256 160 160 272 64 160 160 48Z" stroke="currentColor" stroke-width="1.4"/>
            <path d="M160 78 230 160 160 242 90 160 160 78Z" stroke="currentColor" stroke-width="1.4"/>
            <path d="M160 104 208 160 160 216 112 160 160 104Z" stroke="currentColor" stroke-width="1.4"/>
            <path d="M160 18V302M38 160H282M74 60 246 260M246 60 74 260" stroke="currentColor" stroke-opacity=".45"/>
            <circle cx="160" cy="160" r="22" fill="currentColor" fill-opacity=".16" stroke="currentColor"/>
            <circle cx="160" cy="160" r="6" fill="currentColor"/>
            <circle cx="160" cy="48" r="4" fill="currentColor"/><circle cx="256" cy="160" r="4" fill="currentColor"/><circle cx="160" cy="272" r="4" fill="currentColor"/><circle cx="64" cy="160" r="4" fill="currentColor"/>
          </svg>
        </div>
        <div class="hero-art__caption"><span class="hero-art__caption-mark">✳</span><div><strong>Warisan yang bermakna</strong><p>Jejak budaya untuk generasi mendatang</p></div></div>
      </div>
      <div class="hero-float hero-float--top"><span class="hero-float__icon">✦</span><span>Budaya & tradisi</span></div>
      <div class="hero-float hero-float--bottom"><span class="hero-float__icon">⌖</span><span>Jelajah Sambas</span></div>
    </div>
  </div>
  <div class="pembatas-rebung"></div>
</section>

<main id="isi" class="homepage-premium">
  <!-- PENGANTAR -->
  <section class="section-intro mx-auto max-w-6xl px-4 pt-14 md:pt-20">
    <div class="section-intro__label">JELAJAHI WARISAN</div>
    <div class="grid gap-4 md:grid-cols-[1fr_.75fr] md:items-end">
      <h2 class="font-serif text-3xl font-bold leading-tight md:text-4xl">Satu tempat untuk merawat <span class="text-marun">banyak cerita.</span></h2>
      <p class="max-w-xl text-sm leading-7 text-hijau/75 md:justify-self-end md:text-base">Temukan ragam kisah dan pengetahuan lokal melalui koleksi yang disusun untuk mudah dijelajahi.</p>
    </div>
  </section>

  <!-- KATEGORI BUDAYA -->
  <section class="mx-auto max-w-6xl px-4 pt-8 md:pt-10" aria-labelledby="judul-kategori">
    <div class="section-heading">
      <div><p class="section-kicker">MULAI DARI SINI</p><h2 id="judul-kategori" class="font-serif text-2xl font-bold md:text-3xl">Jelajahi kategori</h2></div>
      <a href="<?= e(base_url('pages/budaya.php')) ?>" class="link-arrow">Semua budaya <span aria-hidden="true">↗</span></a>
    </div>
    <div class="category-grid mt-5">
      <?php foreach ($kategori as $index => $item): ?>
        <a href="<?= e(base_url('pages/budaya.php')) ?>" class="category-card" style="--card-index:<?= (int)$index ?>">
          <span class="category-card__number"><?= e($item['kode']) ?></span>
          <span class="category-card__symbol" aria-hidden="true"><?= ['✳','◇','✦','♫','⌘','◈'][$index] ?></span>
          <span class="category-card__title"><?= e($item['nama']) ?></span>
          <span class="category-card__desc"><?= e($item['deskripsi']) ?></span>
          <span class="category-card__arrow" aria-hidden="true">↗</span>
        </a>
      <?php endforeach; ?>
    </div>
  </section>

  <!-- BUDAYA UNGGULAN -->
  <section class="mx-auto max-w-6xl px-4 pt-14 md:pt-20">
    <div class="featured-culture">
      <div class="featured-culture__visual pola-songket">
        <span class="featured-culture__tag"><span></span> CERITA PILIHAN</span>
        <div class="featured-culture__seal" aria-hidden="true"><span>W</span><small>SAMBAS</small></div>
        <p class="featured-culture__visual-caption">JEJAK · IDENTITAS · WARISAN</p>
      </div>
      <div class="featured-culture__body">
        <p class="section-kicker">MENGENAL LEBIH DEKAT</p>
        <h2 class="mt-3 font-serif text-3xl font-bold leading-tight md:text-4xl">Setiap warisan menyimpan cerita.</h2>
        <p class="mt-4 leading-7 text-hijau/75">Budaya bukan hanya peninggalan masa lalu. Di dalamnya ada nilai, kreativitas, dan identitas yang terus tumbuh bersama masyarakat.</p>
        <a href="<?= e(base_url('pages/budaya.php')) ?>" class="tombol-emas mt-6">Jelajahi koleksi <span class="panah" aria-hidden="true">→</span></a>
        <p class="featured-culture__note">Bagian ini merupakan pengantar editorial. Tambahkan foto dan artikel budaya yang sudah diverifikasi untuk menjadikannya cerita unggulan.</p>
      </div>
    </div>
  </section>

  <!-- SEJARAH PILIHAN -->
  <section class="mx-auto max-w-6xl px-4 pt-14 md:pt-20">
    <div class="section-heading">
      <div><p class="section-kicker">MENGENAL MASA LALU</p><h2 class="font-serif text-2xl font-bold md:text-3xl">Cerita sejarah</h2></div>
      <a href="<?= e(base_url('pages/sejarah.php')) ?>" class="link-arrow">Semua artikel <span aria-hidden="true">↗</span></a>
    </div>
    <div class="mt-5 grid gap-5 md:grid-cols-3">
      <?php foreach ($sejarah as $item) { include __DIR__ . '/../includes/kartu.php'; } ?>
    </div>
  </section>

  <!-- WISATA UNGGULAN -->
  <section class="destination-band mt-14 md:mt-20">
    <div class="mx-auto max-w-6xl px-4 py-12 md:py-16">
      <div class="section-heading section-heading--light">
        <div><p class="section-kicker">MENJELAJAHI DAERAH</p><h2 class="font-serif text-2xl font-bold md:text-3xl">Temukan sisi lain Sambas</h2></div>
        <a href="<?= e(base_url('pages/wisata.php')) ?>" class="link-arrow">Semua destinasi <span aria-hidden="true">↗</span></a>
      </div>
      <div class="mt-5 grid gap-5 md:grid-cols-3">
        <?php foreach ($wisata as $item) { include __DIR__ . '/../includes/kartu.php'; } ?>
      </div>
    </div>
  </section>

  <!-- GALERI TEASER -->
  <section class="mx-auto max-w-6xl px-4 pt-14 md:pt-20">
    <div class="gallery-teaser">
      <div class="gallery-teaser__copy">
        <p class="section-kicker">MOMEN DALAM GAMBAR</p>
        <h2 class="mt-2 font-serif text-3xl font-bold">Lihat. Kenali. Ingat.</h2>
        <p class="mt-3 max-w-lg leading-7 text-hijau/75">Galeri menjadi ruang untuk menyimpan dokumentasi visual budaya dan tempat di Sambas. Lengkapi dengan foto asli, keterangan, dan sumber yang jelas.</p>
        <a href="<?= e(base_url('pages/galeri.php')) ?>" class="link-arrow mt-5">Buka galeri <span aria-hidden="true">↗</span></a>
      </div>
      <div class="gallery-teaser__art" aria-hidden="true">
        <div class="gallery-tile gallery-tile--one"><span>01</span><i></i></div>
        <div class="gallery-tile gallery-tile--two"><span>02</span><i></i></div>
        <div class="gallery-tile gallery-tile--three"><span>03</span><i></i></div>
      </div>
    </div>
  </section>

  <!-- CTA -->
  <section class="mx-auto max-w-6xl px-4 pb-4 pt-14 md:pt-20">
    <div class="closing-cta">
      <div><p class="section-kicker">WARISAN ADALAH TANGGUNG JAWAB BERSAMA</p><h2 class="mt-2 font-serif text-2xl font-bold md:text-3xl">Mari jaga cerita Sambas tetap hidup.</h2><p class="mt-3 max-w-2xl text-sm leading-7 text-gading/75">Mulai dengan mengenal sejarah, menghargai tradisi, dan membagikan informasi yang bersumber jelas.</p></div>
      <a href="<?= e(base_url('pages/tentang.php')) ?>" class="tombol-emas shrink-0">Tentang WarisanKu <span class="panah" aria-hidden="true">→</span></a>
    </div>
  </section>
</main>

<?php require __DIR__ . '/../includes/footer.php'; ?>
