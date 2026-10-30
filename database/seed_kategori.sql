-- Jalankan di database yang sudah ada (phpMyAdmin > pilih warisanku_sambas > tab SQL).
USE warisanku_sambas;

-- Kategori awal. INSERT IGNORE: aman dijalankan berulang (slug bersifat unik).
INSERT IGNORE INTO `categories` (`name`, `slug`, `type`) VALUES
  ('Adat Istiadat',     'adat-istiadat',     'budaya'),
  ('Tradisi',           'tradisi',           'budaya'),
  ('Kesenian dan Tari', 'kesenian-dan-tari', 'budaya'),
  ('Musik',             'musik',             'budaya'),
  ('Kerajinan',         'kerajinan',         'budaya'),
  ('Kuliner Khas',      'kuliner-khas',      'budaya'),
  ('Wisata Alam',       'wisata-alam',       'wisata'),
  ('Wisata Sejarah',    'wisata-sejarah',    'wisata'),
  ('Wisata Pantai',     'wisata-pantai',     'wisata'),
  ('Wisata Religi',     'wisata-religi',     'wisata'),
  ('Budaya',            'galeri-budaya',     'galeri'),
  ('Wisata',            'galeri-wisata',     'galeri');
