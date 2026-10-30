<?php
require_once __DIR__ . '/functions.php';
$pageTitle  = $pageTitle  ?? SITE_NAME;
$activePage = $activePage ?? '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($pageTitle) ?> | <?= e(SITE_NAME) ?></title>
  <meta name="description" content="Platform digital untuk mengenal dan melestarikan budaya, sejarah, dan wisata Kabupaten Sambas, Kalimantan Barat.">
  <meta name="theme-color" content="#173C30">
  <meta property="og:title" content="<?= e($pageTitle) ?> | <?= e(SITE_NAME) ?>">
  <meta property="og:description" content="Platform digital untuk mengenal dan melestarikan budaya, sejarah, dan wisata Kabupaten Sambas.">
  <meta property="og:type" content="website">
  <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32'><rect width='32' height='32' rx='6' fill='%23173C30'/><polygon points='4,28 16,5 28,28' fill='%23D8B66A'/><polygon points='10,28 16,16 22,28' fill='%23173C30'/></svg>">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,700&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap">
  <link rel="stylesheet" href="<?= e(asset('css/style.css')) ?>">
  <script>document.documentElement.classList.add('js')</script>
</head>
<body class="bg-krem font-sans text-hijau antialiased">
<div id="progres" aria-hidden="true"></div>
<a href="#isi" class="sr-only focus:not-sr-only focus:fixed focus:left-3 focus:top-3 focus:z-[70] focus:rounded focus:bg-emas focus:px-4 focus:py-2 focus:font-semibold">Lewati ke konten</a>
<?php require __DIR__ . '/navbar.php'; ?>