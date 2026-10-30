<footer class="mt-16 bg-hijau text-gading">
  <div class="pembatas-rebung"></div>
  <div class="mx-auto grid max-w-6xl gap-8 px-4 py-10 md:grid-cols-3">
    <div>
      <p class="font-serif text-xl font-bold text-emas">WARISANKU SAMBAS</p>
      <p class="mt-2 text-sm text-gading/80">
        Mengenal, mendokumentasikan, dan melestarikan budaya, sejarah, serta wisata Kabupaten Sambas.
      </p>
    </div>
    <div>
      <p class="font-semibold text-emas">Jelajahi</p>
      <ul class="mt-2 space-y-1 text-sm">
        <li><a class="hover:text-emas" href="<?= e(base_url('pages/sejarah.php')) ?>">Sejarah</a></li>
        <li><a class="hover:text-emas" href="<?= e(base_url('pages/budaya.php')) ?>">Budaya dan Tradisi</a></li>
        <li><a class="hover:text-emas" href="<?= e(base_url('pages/wisata.php')) ?>">Wisata</a></li>
        <li><a class="hover:text-emas" href="<?= e(base_url('pages/galeri.php')) ?>">Galeri</a></li>
      </ul>
    </div>
    <div>
      <p class="font-semibold text-emas">Tentang</p>
      <p class="mt-2 text-sm text-gading/80">
        Konten dikumpulkan dari sumber yang dicantumkan pada tiap artikel.
      </p>
    </div>
  </div>
  <div class="border-t border-emas/30 py-4 text-center text-xs text-gading/70">
    &copy; <?= date('Y') ?> <?= e(SITE_NAME) ?>. Hak cipta dilindungi.
  </div>
</footer>
<script src="<?= e(asset('js/main.js')) ?>"></script>
</body>
</html>