<?php
declare(strict_types=1);

require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/csrf.php';
require_once __DIR__ . '/../config/database.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    $https = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'secure'   => $https,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_name('warisanku_sess');
    session_start();
}

const SESSION_IDLE_MAX = 1800; // 30 menit tanpa aktivitas = otomatis keluar

function current_user(): ?array
{
    return $_SESSION['user'] ?? null;
}

function login_user(array $user): void
{
    session_regenerate_id(true); // cegah session fixation
    $_SESSION['user'] = [
        'id'       => (int) $user['id'],
        'name'     => $user['name'],
        'username' => $user['username'],
        'role'     => $user['role'],
    ];
    $_SESSION['last_activity'] = time();
}

function logout_user(): void
{
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
    }
    session_destroy();
}

function require_login(): void
{
    $user = current_user();

    if ($user === null) {
        redirect('admin/login.php');
    }

    // Keluar otomatis kalau terlalu lama diam
    if (time() - ($_SESSION['last_activity'] ?? 0) > SESSION_IDLE_MAX) {
        logout_user();
        session_start();
        set_flash('error', 'Sesi berakhir karena tidak ada aktivitas. Silakan login lagi.');
        redirect('admin/login.php');
    }
    $_SESSION['last_activity'] = time();

    // Cek ulang ke database: akun masih aktif dan role terbaru
    $stmt = db()->prepare('SELECT role, is_active FROM users WHERE id = :id');
    $stmt->execute([':id' => $user['id']]);
    $row = $stmt->fetch();

    if (!$row || (int) $row['is_active'] !== 1) {
        logout_user();
        session_start();
        set_flash('error', 'Akun tidak aktif.');
        redirect('admin/login.php');
    }
    $_SESSION['user']['role'] = $row['role'];
}

function require_role(string ...$roles): void
{
    require_login();
    if (!in_array(current_user()['role'], $roles, true)) {
        http_response_code(403);
        exit('Akses ditolak.');
    }
}