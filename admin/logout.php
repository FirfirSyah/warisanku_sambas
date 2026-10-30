<?php
require_once __DIR__ . '/../includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('admin/dashboard.php');
}
csrf_verify();
logout_user();

session_start();
set_flash('success', 'Kamu sudah keluar.');
redirect('admin/login.php');