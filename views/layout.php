<?php
$flash = $_SESSION['flash'] ?? []; unset($_SESSION['flash']);
?>
<!doctype html><html lang="en"><head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>News Admin</title>
    <link rel="stylesheet" href="/assets/style.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script defer src="/assets/script.js"></script>
</head><body>
<div class="logo-container"><img class="logo" src="assets/images/logo.svg" alt=""></div>
<main class="container">
    <?php if($flash): ?><div class="flash-wrapper">
        <?php foreach($flash as $f): ?><div class="flash <?=h($f['type'])?>"><?=h($f['msg'])?></div><?php endforeach; ?>
        </div><?php endif; ?>
    <?= $content ?>
</main>
</body></html>
