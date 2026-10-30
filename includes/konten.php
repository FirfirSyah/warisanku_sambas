<?php
declare(strict_types=1);

// Pengambilan konten terbit dari database untuk halaman publik.

require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/../config/database.php';

const PUBLIK_PER_HALAMAN = 9;

if (!function_exists('gambar_url')) {
    function gambar_url(?string $nama): string
    {
        return base_url('public/uploads/' . rawurlencode(basename((string) $nama)));
    }
}

/** Nama-nama kategori untuk filter. $tipe: 'budaya' atau 'wisata'. */
function nama_kategori(string $tipe): array
{
    $stmt = db()->prepare('SELECT name FROM categories WHERE type = :t ORDER BY name');
    $stmt->execute([':t' => $tipe]);
    return array_column($stmt->fetchAll(), 'name');
}

/** Ringkasan pendek dari teks panjang: paragraf pertama, dipotong di batas kata. */
function ringkas(string $teks, int $panjang = 160): string
{
    $paragraf = preg_split('/\R{2,}/', trim($teks))[0] ?? '';
    $paragraf = trim((string) preg_replace('/\s+/u', ' ', $paragraf));
    if (mb_strlen($paragraf) <= $panjang) {
        return $paragraf;
    }
    $potong = mb_substr($paragraf, 0, $panjang);
    $spasi  = mb_strrpos($potong, ' ');
    return rtrim($spasi !== false && $spasi > $panjang * 0.6 ? mb_substr($potong, 0, $spasi) : $potong, ' ,.;:') . '...';
}

/** Ubah satu baris database menjadi item untuk includes/kartu.php. */
function item_kartu(array $r, string $jenis): array
{
    $halaman = ['sejarah' => 'detail-sejarah.php', 'budaya' => 'detail-budaya.php', 'wisata' => 'detail-wisata.php'][$jenis];
    return [
        'id'        => (int) $r['id'],
        'judul'     => $r['title'],
        'ringkasan' => $r['summary'] ?? ringkas((string) ($r['deskripsi'] ?? '')),
        'kategori'  => $r['kategori'] ?? ($jenis === 'sejarah' ? 'Sejarah' : 'Warisan Sambas'),
        'url'       => base_url('pages/' . $halaman . '?id=' . (int) $r['id']),
        'gambar'    => !empty($r['image']) ? gambar_url($r['image']) : null,
    ];
}

/**
 * Cari konten yang sudah terbit.
 * $jenis: 'sejarah', 'budaya', atau 'wisata'.
 * Mengembalikan ['items' => [...], 'total' => int].
 */
function cari_konten(string $jenis, string $q = '', string $kat = '', int $hal = 1, int $per = PUBLIK_PER_HALAMAN): array
{
    if (!in_array($jenis, ['sejarah', 'budaya', 'wisata'], true)) {
        throw new InvalidArgumentException('Jenis konten tidak dikenal.');
    }
    $wisata = $jenis === 'wisata';
    $dari   = $wisata ? 'destinations' : 'articles';
    $hal    = max(1, $hal);

    $where  = ["x.status = 'published'"];
    $params = [];
    if (!$wisata) {
        $where[] = 'x.type = :tipe';
        $params[':tipe'] = $jenis;
    }
    if ($q !== '') {
        $like = '%' . addcslashes($q, '%_\\') . '%';
        $where[] = $wisata ? '(x.name LIKE :q1 OR x.description LIKE :q2)' : '(x.title LIKE :q1 OR x.summary LIKE :q2)';
        $params[':q1'] = $like;
        $params[':q2'] = $like;
    }
    if ($kat !== '') {
        $where[] = 'c.name = :kat';
        $params[':kat'] = $kat;
    }
    $kolom = $wisata
        ? 'x.id, x.name AS title, x.description AS deskripsi, x.featured_image AS image, c.name AS kategori'
        : 'x.id, x.title, x.summary, x.featured_image AS image, c.name AS kategori';
    $dasar = "FROM $dari x LEFT JOIN categories c ON c.id = x.category_id WHERE " . implode(' AND ', $where);

    $stmt = db()->prepare("SELECT COUNT(*) $dasar");
    $stmt->execute($params);
    $total = (int) $stmt->fetchColumn();

    $stmt = db()->prepare(
        "SELECT $kolom $dasar
          ORDER BY x.created_at DESC, x.id DESC LIMIT :lim OFFSET :off"
    );
    foreach ($params as $k => $v) {
        $stmt->bindValue($k, $v);
    }
    $stmt->bindValue(':lim', $per, PDO::PARAM_INT);
    $stmt->bindValue(':off', ($hal - 1) * $per, PDO::PARAM_INT);
    $stmt->execute();

    return [
        'items' => array_map(fn(array $r) => item_kartu($r, $jenis), $stmt->fetchAll()),
        'total' => $total,
    ];
}

/** Beberapa konten terbaru untuk beranda. */
function terbaru(string $jenis, int $jumlah = 3): array
{
    return cari_konten($jenis, '', '', 1, $jumlah)['items'];
}

/** Satu konten terbit berdasarkan id, atau null bila tidak ada atau masih draf. */
function ambil_detail(string $jenis, int $id): ?array
{
    if ($jenis === 'wisata') {
        $stmt = db()->prepare(
            "SELECT x.*, x.name AS title, x.featured_image AS image, c.name AS kategori FROM destinations x
               LEFT JOIN categories c ON c.id = x.category_id
              WHERE x.id = :id AND x.status = 'published'"
        );
        $stmt->execute([':id' => $id]);
    } else {
        $stmt = db()->prepare(
            "SELECT x.*, x.featured_image AS image, c.name AS kategori FROM articles x
               LEFT JOIN categories c ON c.id = x.category_id
              WHERE x.id = :id AND x.type = :t AND x.status = 'published'"
        );
        $stmt->execute([':id' => $id, ':t' => $jenis]);
    }
    $row = $stmt->fetch();
    if (!$row) {
        return null;
    }
    if ($jenis !== 'wisata') {
        $row['referensi'] = ambil_referensi((int) $row['id']);
    }
    return $row;
}

/** Daftar sumber referensi sebuah artikel (tabel reference_sources). */
function ambil_referensi(int $artikelId): array
{
    $stmt = db()->prepare('SELECT title, url, publisher, publication_year FROM reference_sources WHERE article_id = :a ORDER BY id');
    $stmt->execute([':a' => $artikelId]);
    return $stmt->fetchAll();
}

function ambil_galeri(int $batas = 60): array
{
    $stmt = db()->prepare(
        "SELECT g.id, g.title, COALESCE(c.name, '') AS category, COALESCE(g.description, '') AS description,
                CONCAT_WS(' / ', NULLIF(g.source, ''), NULLIF(g.license_info, '')) AS source, g.image_path AS image
           FROM galleries g LEFT JOIN categories c ON c.id = g.category_id
          ORDER BY g.created_at DESC, g.id DESC LIMIT :n"
    );
    $stmt->bindValue(':n', $batas, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

/** Teks biasa -> paragraf HTML yang aman (dipisah baris kosong). */
function render_paragraf(string $teks): string
{
    $hasil = '';
    foreach (preg_split('/\R{2,}/', trim($teks)) ?: [] as $p) {
        $p = trim($p);
        if ($p !== '') {
            $hasil .= '<p>' . nl2br(e($p), false) . '</p>';
        }
    }
    return $hasil;
}

/** Satu baris satu butir daftar. */
function render_daftar(string $teks): string
{
    $hasil = '';
    foreach (preg_split('/\R/', trim($teks)) ?: [] as $baris) {
        $baris = trim($baris);
        if ($baris !== '') {
            $hasil .= '<li>' . e($baris) . '</li>';
        }
    }
    return $hasil === '' ? '' : '<ul class="mt-2 list-disc space-y-1 pl-8 text-sm text-hijau/80">' . $hasil . '</ul>';
}

/** Daftar referensi artikel. Judul menjadi tautan bila ada URL http(s). */
function render_referensi(array $baris): string
{
    $hasil = '';
    foreach ($baris as $r) {
        $judul = e($r['title']);
        if (!empty($r['url']) && preg_match('#^https?://#i', $r['url'])) {
            $judul = '<a href="' . e($r['url']) . '" target="_blank" rel="noopener noreferrer" class="text-marun hover:underline">' . $judul . '</a>';
        }
        $tambahan = array_filter([$r['publisher'] ?? '', $r['publication_year'] ?? ''], fn($v) => $v !== '' && $v !== null);
        $hasil .= '<li>' . $judul . ($tambahan ? ' &mdash; ' . e(implode(', ', $tambahan)) : '') . '</li>';
    }
    return $hasil === '' ? '' : '<ul class="mt-2 list-disc space-y-1 pl-8 text-sm text-hijau/80">' . $hasil . '</ul>';
}

function pager_publik(int $total, int $hal, int $per, array $query = []): void
{
    $jumlah = (int) ceil($total / $per);
    if ($jumlah <= 1) {
        return;
    }
    $query = array_filter($query, fn($v) => $v !== '' && $v !== null);
    echo '<nav class="mt-10 flex flex-wrap justify-center gap-2" aria-label="Halaman">';
    for ($i = 1; $i <= $jumlah; $i++) {
        $href = '?' . http_build_query(array_merge($query, ['page' => $i]));
        $kelas = $i === $hal ? 'bg-hijau text-emas' : 'bg-gading hover:bg-emas';
        echo '<a href="' . e($href) . '" class="rounded border border-emas px-3 py-1 ' . $kelas . '"'
           . ($i === $hal ? ' aria-current="page"' : '') . '>' . $i . '</a>';
    }
    echo '</nav>';
}
