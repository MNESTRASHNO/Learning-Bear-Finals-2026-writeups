<?php

use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var app\models\Chapter $chapter */
/** @var app\models\Manga $manga */
/** @var app\models\Chapter|null $prev */
/** @var app\models\Chapter|null $next */

$this->title = $manga->title . ' — Chapter ' . $chapter->chapter_number;
?>

<div class="reader-header">
    <a href="<?= Url::to(['/manga/view', 'id' => $manga->id]) ?>" class="back-link">&larr; <?= Html::encode($manga->title) ?></a>
    <h1>Chapter <?= Html::encode($chapter->chapter_number) ?><?= $chapter->title ? ' — ' . Html::encode($chapter->title) : '' ?></h1>
    <div class="reader-nav">
        <?php if ($prev): ?>
            <a href="<?= Url::to(['/chapter/read', 'mangaId' => $manga->id, 'id' => $prev->id]) ?>" class="btn btn-sm btn-outline">&laquo; Prev</a>
        <?php else: ?>
            <span class="btn btn-sm btn-outline disabled">&laquo; Prev</span>
        <?php endif; ?>
        <?php if ($next): ?>
            <a href="<?= Url::to(['/chapter/read', 'mangaId' => $manga->id, 'id' => $next->id]) ?>" class="btn btn-sm btn-outline">Next &raquo;</a>
        <?php else: ?>
            <span class="btn btn-sm btn-outline disabled">Next &raquo;</span>
        <?php endif; ?>
    </div>
</div>

<div class="reader-container">
    <?php if ($chapter->pages): ?>
        <?php foreach ($chapter->pages as $page): ?>
            <div class="reader-page">
                <img src="<?= Html::encode($page->image_path) ?>" alt="Page <?= Html::encode($page->page_number) ?>" loading="lazy">
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="empty-state">
            <p>No pages available for this chapter.</p>
        </div>
    <?php endif; ?>
</div>

<div class="reader-footer">
    <div class="reader-nav">
        <?php if ($prev): ?>
            <a href="<?= Url::to(['/chapter/read', 'mangaId' => $manga->id, 'id' => $prev->id]) ?>" class="btn btn-outline">&laquo; Previous Chapter</a>
        <?php endif; ?>
        <?php if ($next): ?>
            <a href="<?= Url::to(['/chapter/read', 'mangaId' => $manga->id, 'id' => $next->id]) ?>" class="btn btn-outline">Next Chapter &raquo;</a>
        <?php endif; ?>
    </div>
</div>
