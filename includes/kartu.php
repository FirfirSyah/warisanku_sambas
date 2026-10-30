<?php
/* Komponen kartu editorial. Butuh variabel $item: judul, ringkasan, kategori, url. Opsional: gambar (URL). */
$punyaGambar = !empty($item['gambar']);
$gayaKartu   = $punyaGambar
    ? "background-image:linear-gradient(0deg,rgba(11,31,23,.6),rgba(11,31,23,.05) 70%),url('" . e($item['gambar']) . "');background-size:cover;background-position:center"
    : '';
?>
<article class="kartu-hidup content-card overflow-hidden">
  <a href="<?= e($item['url']) ?>" class="content-card__visual pola-songket"<?= $punyaGambar ? ' style="' . $gayaKartu . '"' : '' ?> aria-label="Buka <?= e($item['judul']) ?>">
    <span class="content-card__eyebrow"><?= e($item['kategori'] ?? 'Warisan Sambas') ?></span>
    <?php if (!$punyaGambar): ?><span class="content-card__visual-mark" aria-hidden="true">✳</span><?php endif; ?>
    <span class="content-card__visual-caption">ARSIP WARISANKU</span>
  </a>
  <div class="content-card__body">
    <span class="content-card__category"><?= e($item['kategori'] ?? 'Warisan Sambas') ?></span>
    <h3 class="mt-2 font-serif text-xl font-bold"><?= e($item['judul']) ?></h3>
    <p class="mt-2 text-sm text-hijau/75"><?= e($item['ringkasan']) ?></p>
    <a href="<?= e($item['url']) ?>" class="content-card__link">Baca selengkapnya <span class="panah" aria-hidden="true">→</span></a>
  </div>
</article>
