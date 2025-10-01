<?php

namespace App\Controllers;

use App\Core\View;

class AuthController
{
    private const U = 'admin';
    private const P = 'test';

    public function login(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $u = trim($_POST['username'] ?? '');
            $p = trim($_POST['password'] ?? '');
            if ($u === self::U && $p === self::P) {
                $_SESSION['user'] = self::U;
                header('Location: /');
                exit;
            }
            $_SESSION['flash'][] = ['type' => 'error', 'msg' => 'Wrong login data'];
            header('Location: /?r=auth/login');
            exit;
        }
        View::render('auth/login');
    }

    public function logout(): void
    {
        $_SESSION = [];
        session_destroy();
        header('Location: /');
        exit;
    }
}
