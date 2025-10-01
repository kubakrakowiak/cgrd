<?php
namespace App\Controllers;

use App\Core\View;
use App\Models\News;

class NewsController
{
    public function __construct(private News $news) {}

    private function requireAuth(): void
    {
        if (empty($_SESSION['user'])) {
            header('Location: /?r=auth/login');
            exit;
        }
    }

    public function index(): void
    {
        $this->requireAuth();

        $items = $this->news->all();
        View::render('news/index', [
            'news' => $items
        ]);
    }

    public function create(): void
    {
        $this->requireAuth();

        $title = trim($_POST['title'] ?? '');
        $desc  = trim($_POST['description'] ?? '');

        if ($title === '' || $desc === '') {
            $_SESSION['flash'][] = [
                'type' => 'error',
                'msg'  => 'Title & description required'
            ];
            header('Location: /');
            exit;
        }

        $this->news->create($title, $desc);

        $_SESSION['flash'][] = [
            'type' => 'success',
            'msg'  => 'News created'
        ];

        header('Location: /');
        exit;
    }

    public function update(): void
    {
        $this->requireAuth();

        $id    = (int)($_POST['id'] ?? 0);
        $title = trim($_POST['title'] ?? '');
        $desc  = trim($_POST['description'] ?? '');

        if ($id <= 0 || $title === '' || $desc === '') {
            $_SESSION['flash'][] = [
                'type' => 'error',
                'msg'  => 'Invalid data'
            ];
            header('Location: /');
            exit;
        }

        $this->news->update($id, $title, $desc);

        $_SESSION['flash'][] = [
            'type' => 'success',
            'msg'  => 'News updated'
        ];

        header('Location: /');
        exit;
    }

    public function delete(): void
    {
        $this->requireAuth();

        $id = (int)($_POST['id'] ?? 0);

        if ($id <= 0) {
            $_SESSION['flash'][] = [
                'type' => 'error',
                'msg'  => 'Invalid ID'
            ];
            header('Location: /');
            exit;
        }

        $this->news->delete($id);

        $_SESSION['flash'][] = [
            'type' => 'success',
            'msg'  => 'News deleted'
        ];

        header('Location: /');
        exit;
    }
}
