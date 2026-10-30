<?php /* Butuh variabel: $judul dan (opsional) $subjudul */ ?>
<section class="hero-hias pola-songket bg-hijau text-gading">
  <div class="mx-auto max-w-6xl px-4 py-12 text-center">
    <h1 class="font-serif text-3xl font-bold text-emas md:text-4xl"><?= e($judul) ?></h1>
    <?php if (!empty($subjudul)): ?>
      <p class="mx-auto mt-3 max-w-2xl text-gading/85"><?= e($subjudul) ?></p>
    <?php endif; ?>
  </div>
  <div class="pembatas-rebung"></div>
</section>