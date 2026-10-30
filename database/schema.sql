-- Skema database WARISANKU SAMBAS
-- Cara pakai: impor file ini lewat phpMyAdmin (tab Import) atau
--   mysql -u root < database/schema.sql
-- Setelah itu buka /admin/setup.php untuk membuat akun admin pertama.

CREATE DATABASE IF NOT EXISTS warisanku_sambas
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE warisanku_sambas;

CREATE TABLE IF NOT EXISTS users (
  id            INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  name          VARCHAR(100) NOT NULL,
  username      VARCHAR(30)  NOT NULL UNIQUE,
  email         VARCHAR(150) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  role          ENUM('admin','editor') NOT NULL DEFAULT 'editor',
  is_active     TINYINT(1) NOT NULL DEFAULT 1,
  created_at    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Kategori dipakai artikel budaya (type = budaya) dan destinasi wisata (type = wisata)
CREATE TABLE IF NOT EXISTS categories (
  id   INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(80)  NOT NULL,
  slug VARCHAR(100) NOT NULL,
  type ENUM('budaya','wisata') NOT NULL,
  UNIQUE KEY uq_kategori (type, slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Artikel sejarah (type = sejarah) dan budaya (type = budaya)
CREATE TABLE IF NOT EXISTS articles (
  id             INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  type           ENUM('sejarah','budaya') NOT NULL,
  category_id    INT UNSIGNED NULL,
  title          VARCHAR(200) NOT NULL,
  slug           VARCHAR(220) NOT NULL UNIQUE,
  summary        VARCHAR(300) NOT NULL,
  content        MEDIUMTEXT   NOT NULL,
  reference_text TEXT NULL,
  image          VARCHAR(100) NULL,
  status         ENUM('draft','published') NOT NULL DEFAULT 'draft',
  author_id      INT UNSIGNED NULL,
  created_at     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY idx_tipe_status (type, status),
  CONSTRAINT fk_artikel_kategori FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
  CONSTRAINT fk_artikel_penulis  FOREIGN KEY (author_id)   REFERENCES users(id)      ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS destinations (
  id          INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  category_id INT UNSIGNED NULL,
  title       VARCHAR(200) NOT NULL,
  slug        VARCHAR(220) NOT NULL UNIQUE,
  summary     VARCHAR(300) NOT NULL,
  description MEDIUMTEXT   NOT NULL,
  address     VARCHAR(255) NULL,
  latitude    DECIMAL(10,7) NULL,
  longitude   DECIMAL(10,7) NULL,
  image       VARCHAR(100) NULL,
  status      ENUM('draft','published') NOT NULL DEFAULT 'draft',
  author_id   INT UNSIGNED NULL,
  created_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY idx_status (status),
  CONSTRAINT fk_wisata_kategori FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
  CONSTRAINT fk_wisata_penulis  FOREIGN KEY (author_id)   REFERENCES users(id)      ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS galleries (
  id          INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  title       VARCHAR(150) NOT NULL,
  category    ENUM('Budaya','Wisata') NOT NULL,
  description VARCHAR(300) NOT NULL DEFAULT '',
  source      VARCHAR(255) NOT NULL,
  image       VARCHAR(100) NOT NULL,
  created_by  INT UNSIGNED NULL,
  created_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_galeri_pembuat FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Kategori awal (sama dengan daftar di includes/data-contoh.php)
INSERT IGNORE INTO categories (name, slug, type) VALUES
  ('Adat Istiadat',     'adat-istiadat',     'budaya'),
  ('Tradisi',           'tradisi',           'budaya'),
  ('Kesenian dan Tari', 'kesenian-dan-tari', 'budaya'),
  ('Musik',             'musik',             'budaya'),
  ('Kerajinan',         'kerajinan',         'budaya'),
  ('Kuliner Khas',      'kuliner-khas',      'budaya'),
  ('Wisata Alam',       'wisata-alam',       'wisata'),
  ('Wisata Sejarah',    'wisata-sejarah',    'wisata'),
  ('Wisata Pantai',     'wisata-pantai',     'wisata'),
  ('Wisata Religi',     'wisata-religi',     'wisata');
