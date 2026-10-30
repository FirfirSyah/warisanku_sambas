<?php
// Membuka /admin/ langsung diarahkan ke dashboard (atau halaman login bila belum masuk).
require_once __DIR__ . '/../includes/auth.php';

redirect(current_user() !== null ? 'admin/dashboard.php' : 'admin/login.php');
