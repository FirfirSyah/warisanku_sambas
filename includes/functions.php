<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/app.php';

// Escape output supaya aman dari XSS
function e(?string $nilai): string
{
    return htmlspecialchars($nilai ?? '', ENT_QUOTES, 'UTF-8');
}

function base_url(string $path = ''): string
{
    return BASE_URL . '/' . ltrim($path, '/');
}

function asset(string $path): string
{
    return base_url('public/assets/' . ltrim($path, '/'));
}

function redirect(string $path): never
{
    header('Location: ' . base_url($path));
    exit;
}

function set_flash(string $type, string $pesan): void
{
    $_SESSION['flash'] = ['type' => $type, 'msg' => $pesan];
}

function get_flash(): ?array
{
    $f = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $f;
}