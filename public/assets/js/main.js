(() => {
  const dok = document.documentElement;
  const kurangGerak = matchMedia('(prefers-reduced-motion: reduce)').matches;

  // ===== Menu mobile =====
  const tombol = document.getElementById('menu-btn');
  const menuMobile = document.getElementById('menu-mobile');
  if (tombol && menuMobile) {
    tombol.addEventListener('click', () => {
      const buka = menuMobile.classList.toggle('buka');
      tombol.setAttribute('aria-expanded', String(buka));
    });
  }

  // ===== Navbar mengecil, progres gulir, tombol ke atas =====
  const nav = document.querySelector('.nav-utama');
  const bar = document.getElementById('progres');
  const keAtas = document.createElement('button');
  keAtas.type = 'button';
  keAtas.className = 'ke-atas';
  keAtas.setAttribute('aria-label', 'Kembali ke atas');
  keAtas.innerHTML = '&uarr;';
  keAtas.addEventListener('click', () => scrollTo({ top: 0, behavior: kurangGerak ? 'auto' : 'smooth' }));
  document.body.append(keAtas);

  const saatGulir = () => {
    const y = scrollY;
    const tinggi = dok.scrollHeight - innerHeight;
    if (nav) nav.classList.toggle('mengecil', y > 30);
    if (bar) bar.style.transform = `scaleX(${tinggi > 0 ? y / tinggi : 0})`;
    keAtas.classList.toggle('tampil', y > 500);
  };
  addEventListener('scroll', saatGulir, { passive: true });
  saatGulir();

  // ===== Muncul saat digulir (otomatis di semua halaman) =====
  const io = new IntersectionObserver((daftar) => {
    daftar.forEach((d) => {
      if (!d.isIntersecting) return;
      const el = d.target;
      io.unobserve(el);
      el.classList.add('tampil');
      // Lepas kelas reveal setelah selesai supaya efek hover kartu tidak ikut tertunda
      const bersih = () => { el.classList.remove('reveal', 'tampil'); el.style.removeProperty('--d'); };
      el.addEventListener('transitionend', (ev) => { if (ev.target === el && ev.propertyName === 'transform') bersih(); });
      setTimeout(bersih, 2200);
    });
  }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });

  document.querySelectorAll('main > *, main .grid > *, footer > div').forEach((el) => {
    el.classList.add('reveal');
    const urutan = [...el.parentElement.children].indexOf(el);
    el.style.setProperty('--d', Math.min(urutan, 5) * 90 + 'ms');
    io.observe(el);
  });

  // ===== Hitung naik untuk angka (mis. dashboard admin) =====
  document.querySelectorAll('[data-count]').forEach((el) => {
    const akhir = Number(el.dataset.count);
    if (kurangGerak || !akhir) return;
    el.textContent = '0';
    const pantau = new IntersectionObserver(([d], o) => {
      if (!d.isIntersecting) return;
      o.disconnect();
      const mulai = performance.now();
      const jalan = (t) => {
        const p = Math.min((t - mulai) / 1200, 1);
        el.textContent = Math.round(akhir * (1 - Math.pow(1 - p, 3)));
        if (p < 1) requestAnimationFrame(jalan);
      };
      requestAnimationFrame(jalan);
    });
    pantau.observe(el);
  });

  // ===== Hero: hiasan bergerak mengikuti kursor =====
  const hero = document.querySelector('.hero-hias');
  if (hero && !kurangGerak && matchMedia('(hover: hover)').matches) {
    hero.addEventListener('mousemove', (ev) => {
      const r = hero.getBoundingClientRect();
      hero.style.setProperty('--mx', ((ev.clientX - r.left) / r.width - 0.5).toFixed(3));
      hero.style.setProperty('--my', ((ev.clientY - r.top) / r.height - 0.5).toFixed(3));
    });
  }

  // ===== Galeri: filter dan modal =====
  const filterGaleri = document.getElementById('filter-galeri');
  const itemGaleri = document.querySelectorAll('.item-galeri');
  const modal = document.getElementById('modal-galeri');

  if (filterGaleri) {
    filterGaleri.addEventListener('click', (ev) => {
      const tombolFilter = ev.target.closest('button[data-filter]');
      if (!tombolFilter) return;
      const pilihan = tombolFilter.dataset.filter;

      filterGaleri.querySelectorAll('button[data-filter]').forEach((b) => {
        const aktif = b === tombolFilter;
        b.setAttribute('aria-pressed', String(aktif));
        b.classList.toggle('bg-hijau', aktif);
        b.classList.toggle('text-emas', aktif);
        b.classList.toggle('bg-gading', !aktif);
      });

      let urutan = 0;
      itemGaleri.forEach((it) => {
        const tampil = pilihan === 'semua' || it.dataset.kategori === pilihan;
        it.classList.toggle('hidden', !tampil);
        it.classList.remove('muncul');
        it.tabIndex = tampil ? 0 : -1;
        it.setAttribute('aria-hidden', String(!tampil));
        if (tampil) {
          void it.offsetWidth;
          it.style.animationDelay = kurangGerak ? '0ms' : (urutan++ * 60) + 'ms';
          if (!kurangGerak) it.classList.add('muncul');
        }
      });
      const kosong = document.getElementById('galeri-kosong');
      if (kosong) kosong.classList.toggle('hidden', [...itemGaleri].some((it) => !it.classList.contains('hidden')));
    });
  }

  if (modal) {
    let pemicu = null;
    const tombolTutup = document.getElementById('modal-tutup');
    const elemenFokus = () => [...modal.querySelectorAll('a[href], button:not([disabled]), input:not([disabled]), [tabindex]:not([tabindex="-1"])')]
      .filter((el) => el.offsetParent !== null);
    const tutupModal = () => {
      modal.classList.add('hidden');
      modal.classList.remove('flex');
      document.body.style.overflow = '';
      if (pemicu) pemicu.focus();
    };
    itemGaleri.forEach((it) => {
      it.addEventListener('click', () => {
        pemicu = it;
        const foto = document.getElementById('modal-gambar');
        if (foto) { foto.src = it.dataset.gambar || ''; foto.alt = it.dataset.judul || ''; }
        document.getElementById('modal-judul').textContent = it.dataset.judul || 'Dokumentasi';
        document.getElementById('modal-deskripsi').textContent = it.dataset.deskripsi || '';
        document.getElementById('modal-sumber').textContent = it.dataset.sumber || 'Belum dicantumkan';
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
        tombolTutup?.focus();
      });
    });
    tombolTutup?.addEventListener('click', tutupModal);
    modal.addEventListener('click', (ev) => { if (ev.target === modal) tutupModal(); });
    document.addEventListener('keydown', (ev) => {
      if (modal.classList.contains('hidden')) return;
      if (ev.key === 'Escape') { ev.preventDefault(); tutupModal(); return; }
      if (ev.key === 'Tab') {
        const fokus = elemenFokus();
        if (!fokus.length) { ev.preventDefault(); return; }
        const pertama = fokus[0];
        const terakhir = fokus[fokus.length - 1];
        if (ev.shiftKey && document.activeElement === pertama) { ev.preventDefault(); terakhir.focus(); }
        else if (!ev.shiftKey && document.activeElement === terakhir) { ev.preventDefault(); pertama.focus(); }
      }
    });
  }
})();
