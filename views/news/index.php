<?php if (!empty($_SESSION['user'])): ?>
    <?php if ($news): ?>
        <section class="list">
            <h2>All News</h2>
            <ul id="news-list" class="news-list">
                <?php foreach($news as $n): ?>
                    <li class="card" data-id="<?=h($n['id'])?>" data-title="<?=h($n['title'])?>" data-description="<?=h($n['description'])?>">
                        <div class="item-head"><strong><?=h($n['title'])?></strong> <?=h($n['description'])?></div>
                        <div class="item-actions">
                            <button class="edit-btn" type="button"><img src="assets/images/pencil.svg" alt="Edit"></button>
                            <form method="post" action="/?r=news/delete">
                                <input type="hidden" name="id" value="<?=h($n['id'])?>">
                                <button type="submit"><img src="assets//images/close.svg" alt="Delete"></button>
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
            <button id="cancel-edit" class="icon-btn" type="button" style="display:none;"><img src="assets/images/close.svg" alt=""></button>
        </div>
        <form id="news-form" method="post" action="/?r=news/create">
            <input type="hidden" name="id" value="">
            <label><input name="title" placeholder="Title" maxlength="255" required></label>
            <label><textarea name="description" placeholder="Description" rows="6" required></textarea></label>
            <button id="submit-btn" class="btn" type="submit">Create</button>
        </form>
        <form class="logout-form" method="post" action="/?r=auth/logout"><button class="btn" type="submit">Logout</button></form>
    </section>
<?php else: ?>
    <?php require __DIR__ . '/../auth/login.php'; ?>
<?php endif; ?>
