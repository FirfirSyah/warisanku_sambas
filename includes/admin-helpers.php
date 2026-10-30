<?php
declare(strict_types=1);

require_once __DIR__ . '/functions.php';

const UPLOAD_DIR     = __DIR__ . '/../public/uploads';
const UPLOAD_MAX_BYTE = 2 * 1024 * 1024; // 2 MB
const PER_HALAMAN    = 10;

/** Buat slug URL dari teks, contoh: "Rumah Adat Sambas" -> "rumah-adat-sambas" */
function slugify(string $teks): string
{
    $teks = mb_strtolower(trim($teks), 'UTF-8');
    $teks = preg_replace('/[^a-z0-9]+/u', '-', $teks) ?? '';
    $teks = trim($teks, '-');
    return $teks !== '' ? mb_substr($teks, 0, 100) : 'konten';
}

/** Slug unik pada tabel tertentu. $abaikanId dipakai saat mengedit data yang sama. */
function slug_unik(string $tabel, string $judul, ?int $abaikanId = null, ?string $tipe = null): string
{
    $diizinkan = ['articles', 'destinations', 'categories'];
    if (!in_array($tabel, $diizinkan, true)) {
        throw new InvalidArgumentException('Tabel tidak diizinkan.');
    }

    $dasar = slugify($judul);
    $slug  = $dasar;
    $n     = 2;

    while (true) {
        $sql    = "SELECT COUNT(*) FROM `$tabel` WHERE slug = :s AND id <> :id";
        $params = [':s' => $slug, ':id' => $abaikanId ?? 0];
        if ($tabel === 'categories' && $tipe !== null) {
            $sql .= ' AND type = :t';
            $params[':t'] = $tipe;
        }
        $stmt = db()->prepare($sql);
        $stmt->execute($params);
        if ((int) $stmt->fetchColumn() === 0) {
            return $slug;
        }
        $slug = $dasar . '-' . $n++;
    }
}

/**
 * Proses unggah gambar dari $_FILES[$field].
 * Mengembalikan nama file baru, atau null jika tidak ada file diunggah.
 * Pesan kesalahan dimasukkan ke $errors.
 */
function unggah_gambar(string $field, array &$errors): ?string
{
    $f = $_FILES[$field] ?? null;
    if ($f === null || ($f['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return null;
    }
    if ($f['error'] !== UPLOAD_ERR_OK) {
        $errors[] = in_array($f['error'], [UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE], true)
            ? 'Ukuran gambar terlalu besar (maksimal 2 MB).'
            : 'Gambar gagal diunggah. Coba lagi.';
        return null;
    }
    if ($f['size'] > UPLOAD_MAX_BYTE) {
        $errors[] = 'Ukuran gambar terlalu besar (maksimal 2 MB).';
        return null;
    }
    if (!is_uploaded_file($f['tmp_name'])) {
        $errors[] = 'Berkas unggahan tidak valid.';
        return null;
    }

    // Cek tipe asli dari isi berkas, bukan dari nama atau ekstensi
    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($f['tmp_name']);
    $ekstensi = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'][$mime] ?? null;
    if ($ekstensi === null || @getimagesize($f['tmp_name']) === false) {
        $errors[] = 'Gambar harus berformat JPG, PNG, atau WebP.';
        return null;
    }

    if (!is_dir(UPLOAD_DIR) && !mkdir(UPLOAD_DIR, 0755, true) && !is_dir(UPLOAD_DIR)) {
        $errors[] = 'Folder public/uploads tidak bisa dibuat. Periksa izin folder.';
        return null;
    }

    $nama = bin2hex(random_bytes(12)) . '.' . $ekstensi; // nama acak, bukan nama asli pengguna
    if (!move_uploaded_file($f['tmp_name'], UPLOAD_DIR . '/' . $nama)) {
        $errors[] = 'Gambar tidak bisa disimpan. Periksa izin folder public/uploads.';
        return null;
    }
    return $nama;
}

function hapus_gambar(?string $nama): void
{
    if ($nama === null || $nama === '') {
        return;
    }
    $path = UPLOAD_DIR . '/' . basename($nama);
    if (is_file($path)) {
        @unlink($path);
    }
}

function gambar_url(?string $nama): string
{
    return base_url('public/uploads/' . rawurlencode(basename((string) $nama)));
}

/** Ambil teks dari POST, dipangkas spasi. */
function post_teks(string $kunci): string
{
    $v = $_POST[$kunci] ?? '';
    return is_string($v) ? trim($v) : '';
}

/** Halaman aktif dari ?page= (minimal 1). */
function halaman_aktif(): int
{
    return max(1, (int) ($_GET['page'] ?? 1));
}

/** Navigasi halaman. $query = parameter GET lain yang ikut dipertahankan. */
function tampil_pager(int $total, int $halaman, int $perHalaman, array $query = []): void
{
    $jumlah = (int) ceil($total / $perHalaman);
    if ($jumlah <= 1) {
        return;
    }
    echo '<nav class="adm-pager" aria-label="Halaman">';
    for ($i = 1; $i <= $jumlah; $i++) {
        $q    = http_build_query(array_merge($query, ['page' => $i]));
        $aktif = $i === $halaman ? ' adm-pager__aktif' : '';
        echo '<a class="adm-pager__item' . $aktif . '" href="?' . e($q) . '">' . $i . '</a>';
    }
    echo '</nav>';
}

function badge_status(string $status): string
{
    return $status === 'published'
        ? '<span class="adm-badge adm-badge--terbit">Terbit</span>'
        : '<span class="adm-badge adm-badge--draf">Draf</span>';
}

function tanggal_id(?string $waktu): string
{
    if (!$waktu) {
        return '-';
    }
    $bulan = ['', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
    $t = strtotime($waktu);
    return $t ? date('j', $t) . ' ' . $bulan[(int) date('n', $t)] . ' ' . date('Y', $t) : '-';
}

/** Dipakai oleh halaman hapus: hanya menerima POST dengan token CSRF yang benar. */
function wajib_post(string $kembali): void
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        redirect($kembali);
    }
    csrf_verify();
}

/** Daftar kategori untuk dropdown. */
function daftar_kategori(string $tipe): array
{
    $stmt = db()->prepare('SELECT id, name FROM categories WHERE type = :t ORDER BY name');
    $stmt->execute([':t' => $tipe]);
    return $stmt->fetchAll();
}

/** Pastikan kategori ada dan bertipe yang benar. Mengembalikan id atau null. */
function kategori_valid(mixed $id, string $tipe): ?int
{
    $id = (int) $id;
    if ($id <= 0) {
        return null;
    }
    $stmt = db()->prepare('SELECT id FROM categories WHERE id = :id AND type = :t');
    $stmt->execute([':id' => $id, ':t' => $tipe]);
    return $stmt->fetchColumn() ? $id : null;
}

/** Pengaturan untuk dua jenis artikel yang berbagi kode yang sama. */
function cfg_artikel(string $tipe): array
{
    return match ($tipe) {
        'sejarah' => ['tipe' => 'sejarah', 'label' => 'Sejarah', 'folder' => 'admin/artikel', 'kategori' => false],
        'budaya'  => ['tipe' => 'budaya',  'label' => 'Budaya',  'folder' => 'admin/budaya',  'kategori' => true],
        default   => throw new InvalidArgumentException('Tipe artikel tidak dikenal.'),
    };
}
