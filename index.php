<?php
session_start();

const ADMIN_USER = 'admin';
const ADMIN_PASS = 'test';

$DB_HOST = getenv('DB_HOST') ?: '127.0.0.1';
$DB_NAME = getenv('DB_NAME') ?: 'cgrd';
$DB_USER = getenv('DB_USER') ?: 'cgrd_user';
$DB_PASS = getenv('DB_PASS') ?: 'cgrd';
$DB_PORT = getenv('DB_PORT') ?: '3306';

try {
    $dsn = "mysql:host={$DB_HOST};port={$DB_PORT};dbname={$DB_NAME};charset=utf8mb4";
    $pdo = new PDO($dsn, $DB_USER, $DB_PASS, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (Throwable $e) {
    http_response_code(500);
    echo '<pre style="padding:16px;font-family:monospace">DB error: '
        . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . '</pre>';
    exit;
}

function h($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
function isLogged(){ return !empty($_SESSION['user']) && $_SESSION['user'] === ADMIN_USER; }
function flash_add($type,$msg){ $_SESSION['flash'][] = ['type'=>$type,'msg'=>$msg]; }
function flash_pull(){ $m = $_SESSION['flash'] ?? []; unset($_SESSION['flash']); return $m; }
function back(){ header('Location: index.php'); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    switch ($action) {
        case 'login': {
            $u = trim($_POST['username'] ?? '');
            $p = trim($_POST['password'] ?? '');
            if ($u === ADMIN_USER && $p === ADMIN_PASS) {
                $_SESSION['user'] = ADMIN_USER;
            } else {
                flash_add('error', 'Wrong login data!');
            }
            back();
        }

        case 'logout': {
            $_SESSION = [];
            session_destroy();
            back();
        }
    }

    if (!isLogged()) {
        flash_add('error', 'Unauthorized.');
        back();
    }

    switch ($action) {
        case 'create': {
            $title = trim($_POST['title'] ?? '');
            $desc  = trim($_POST['description'] ?? '');
            if ($title === '' || $desc === '') {
                flash_add('error', 'Title and description are required.');
                back();
            }
            $stmt = $pdo->prepare("INSERT INTO news (title, description) VALUES (:t,:d)");
            $stmt->execute([':t'=>$title, ':d'=>$desc]);
            flash_add('success', 'News was successfull created!');
            back();
        }

        case 'update': {
            $id    = (int)($_POST['id'] ?? 0);
            $title = trim($_POST['title'] ?? '');
            $desc  = trim($_POST['description'] ?? '');
            if ($id <= 0) { flash_add('error','Invalid ID.'); back(); }
            if ($title === '' || $desc === '') { flash_add('error','Title and description are required.'); back(); }
            $stmt = $pdo->prepare("UPDATE news SET title=:t, description=:d WHERE id=:id");
            $stmt->execute([':t'=>$title, ':d'=>$desc, ':id'=>$id]);
            flash_add('success', 'News was successfull changed!');
            back();
        }

        case 'delete': {
            $id = (int)($_POST['id'] ?? 0);
            if ($id <= 0) { flash_add('error','Invalid ID.'); back(); }
            $stmt = $pdo->prepare("DELETE FROM news WHERE id=:id");
            $stmt->execute([':id'=>$id]);
            flash_add('success', 'News was deleted');
            back();
        }

        default: {
            back();
        }
    }
}

$news  = isLogged() ? $pdo->query("SELECT * FROM news ORDER BY id DESC")->fetchAll() : [];
$flash = flash_pull();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <title>CGRD</title>
    <link rel="stylesheet" href="/style.css"/>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script defer src="/script.js"></script>
</head>
<body>
<div class="logo-container">
    <img src="images/logo.svg" alt="Logo" class="logo">
</div>

<main class="container">
    <?php if ($flash): ?>
        <div class="flash-wrapper">
            <?php foreach ($flash as $f): ?>
                <div class="flash <?=h($f['type'])?>"><?=h($f['msg'])?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if (!isLogged()): ?>
        <form method="post" autocomplete="off">
            <h2>Login</h2>
            <label>
                <input type="text" name="username" placeholder="Username" required>
            </label>
            <label>
                <input type="password" placeholder="Password" name="password" required>
            </label>
            <input type="hidden" name="action" value="login">
            <button type="submit" class="btn">Sign in</button>
        </form>
    <?php else: ?>

        <?php if ($news): ?>
            <section class="list">
                <h2>All News</h2>
                <ul id="news-list" class="news-list">
                    <?php foreach ($news as $n): ?>
                        <li class="card"
                            data-id="<?=h($n['id'])?>"
                            data-title="<?=h($n['title'])?>"
                            data-description="<?=h($n['description'])?>">
                            <div class="item-head">
                                <strong><?=h($n['title'])?></strong> <?=h($n['description'])?>
                            </div>
                            <div class="item-actions">
                                <button class="edit-btn" type="button">
                                    <img src="images/pencil.svg" alt="Edit">
                                </button>
                                <form method="post">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?=h($n['id'])?>">
                                    <button type="submit">
                                        <img src="images/close.svg" alt="Delete">
                                    </button>
                                </form>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </section>
        <?php endif; ?>


        <section class="form">
            <div class="form-header">
                <h2 id="form-title">Create News</h2>
                <button id="cancel-edit" class="icon-btn" type="button" aria-label="Cancel edit" style="display:none;">
                    <img src="images/close.svg" alt="">
                </button>
            </div>

            <form id="news-form" method="post">
                <input type="hidden" name="action" value="create">
                <input type="hidden" name="id" value="">
                <label>
                    <input type="text" name="title" placeholder="Title" required maxlength="255">
                </label>
                <label>
                    <textarea name="description" placeholder="Description" rows="8" required></textarea>
                </label>
                <div class="actions">
                    <button type="submit" id="submit-btn" class="btn">Create</button>
                </div>
            </form>

            <form method="post" class="logout-form">
                <input type="hidden" name="action" value="logout">
                <button type="submit" class="btn">Logout</button>
            </form>
        </section>

    <?php endif; ?>
</main>
</body>
</html>
