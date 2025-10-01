<?php
spl_autoload_register(function ($class) {
    $file = __DIR__ . '/../' . str_replace('\\', '/', $class) . '.php';
    if (is_file($file)) { require $file; return; }
});

use App\Controllers\AuthController;
use App\Controllers\NewsController;
use App\Models\News;

session_start();

function h($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }

$pdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=cgrd;charset=utf8mb4','cgrd_user','cgrd', [
    PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC
]);

$route = $_GET['r'] ?? '';
$newsCtrl = new NewsController(new News($pdo));
$authCtrl = new AuthController();

switch ($route) {
    case 'auth/login':
        $authCtrl->login();
        break;

    case 'auth/logout':
        $authCtrl->logout();
        break;

    case 'news/create':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $newsCtrl->create();
        } else {
            header('Location: /');
        }
        break;

    case 'news/update':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $newsCtrl->update();
        } else {
            header('Location: /');
        }
        break;

    case 'news/delete':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $newsCtrl->delete();
        } else {
            header('Location: /');
        }
        break;

    default:
        $newsCtrl->index();
        break;
}
