<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var string|null $search */
/** @var string|null $status */

$this->title = 'Browse Manga';
?>

<div class="manga-browse">
    <div class="browse-header">
        <h1><?= Html::encode($this->title) ?></h1>

        <form class="search-bar" method="get" action="<?= Url::to(['/manga/index']) ?>">
            <input type="text" name="q" class="form-control" placeholder="Search manga, author..."
                   value="<?= Html::encode($search) ?>">
            <button type="submit" class="btn btn-accent">Search</button>
        </form>
    </div>

    <div class="filter-bar">
        <a href="<?= Url::to(['/manga/index']) ?>" class="filter-tag <?= !$status ? 'active' : '' ?>">All</a>
        <a href="<?= Url::to(['/manga/index', 'status' => 'ongoing']) ?>" class="filter-tag <?= $status === 'ongoing' ? 'active' : '' ?>">Ongoing</a>
        <a href="<?= Url::to(['/manga/index', 'status' => 'completed']) ?>" class="filter-tag <?= $status === 'completed' ? 'active' : '' ?>">Completed</a>
        <a href="<?= Url::to(['/manga/index', 'status' => 'hiatus']) ?>" class="filter-tag <?= $status === 'hiatus' ? 'active' : '' ?>">Hiatus</a>
    </div>

    <div class="manga-grid">
        <?php foreach ($dataProvider->getModels() as $manga): ?>
            <a href="<?= Url::to(['/manga/view', 'id' => $manga->id]) ?>" class="manga-card">
                <div class="manga-cover">
                    <?php if ($manga->cover_image): ?>
                        <img src="<?= Html::encode($manga->cover_image) ?>" alt="<?= Html::encode($manga->title) ?>">
                    <?php else: ?>
                        <div class="cover-placeholder"><?= Html::encode(mb_substr($manga->title, 0, 1)) ?></div>
                    <?php endif; ?>
                </div>
                <div class="manga-info">
                    <h3><?= Html::encode($manga->title) ?></h3>
                    <span class="manga-author"><?= Html::encode($manga->author) ?></span>
                </div>
            </a>
        <?php endforeach; ?>
    </div>

    <?php if ($dataProvider->totalCount === 0): ?>
        <div class="empty-state">
            <p>No manga found.</p>
        </div>
    <?php endif; ?>

    <div class="pagination-wrap">
        <?= LinkPager::widget([
            'pagination' => $dataProvider->pagination,
            'options' => ['class' => 'pagination'],
            'linkOptions' => ['class' => 'page-link'],
        ]) ?>
    </div>
</div>
