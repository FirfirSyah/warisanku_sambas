<?php
declare(strict_types=1);

require_once __DIR__ . '/functions.php';

function kategori_budaya(): array
{
    return ['Adat Istiadat', 'Tradisi', 'Kesenian dan Tari', 'Musik', 'Kerajinan', 'Kuliner Khas'];
}

function kategori_wisata(): array
{
    return ['Wisata Alam', 'Wisata Sejarah', 'Wisata Pantai', 'Wisata Religi'];
}

function contoh_budaya(): array
{
    $hasil = [];
    $id = 1;
    foreach (kategori_budaya() as $k) {
        for ($j = 1; $j <= 2; $j++, $id++) {
            $hasil[] = [
                'id'        => $id,
                'judul'     => "Contoh $k $j",
                'ringkasan' => 'Ringkasan singkat akan tampil di sini setelah data dimasukkan lewat dashboard admin.',
                'kategori'  => $k,
                'url'       => base_url("pages/detail-budaya.php?id=$id"),
            ];
        }
    }
    return $hasil;
}

function contoh_wisata(): array
{
    $hasil = [];
    $id = 1;
    foreach (kategori_wisata() as $k) {
        for ($j = 1; $j <= 2; $j++, $id++) {
            $hasil[] = [
                'id'        => $id,
                'judul'     => "Contoh Destinasi $id",
                'ringkasan' => 'Deskripsi destinasi akan tampil di sini setelah data dimasukkan lewat dashboard admin.',
                'kategori'  => $k,
                'url'       => base_url("pages/detail-wisata.php?id=$id"),
            ];
        }
    }
    return $hasil;
}

function contoh_sejarah(): array
{
    $hasil = [];
    for ($i = 1; $i <= 6; $i++) {
        $hasil[] = [
            'id'        => $i,
            'judul'     => "Contoh Artikel Sejarah $i",
            'ringkasan' => 'Ringkasan artikel sejarah akan tampil di sini setelah data dimasukkan lewat dashboard admin.',
            'kategori'  => 'Sejarah',
            'url'       => '#',
        ];
    }
    return $hasil;
}