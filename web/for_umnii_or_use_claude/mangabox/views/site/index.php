<?php

use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var app\models\Manga[] $latest */
/** @var app\models\Manga[] $popular */

$this->title = 'Home';
?>

<div class="hero-section">
    <h1>Welcome to MangaBox</h1>
    <p>Read your favorite manga online, anytime, anywhere.</p>
    <a href="<?= Url::to(['/manga/index']) ?>" class="btn btn-accent btn-lg">Browse Manga</a>
</div>

<?php if (!empty($latest)): ?>
<section class="manga-section">
    <h2>Latest Updates</h2>
    <div class="manga-grid">
        <?php foreach ($latest as $manga): ?>
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
                    <?php if ($manga->title_jp): ?>
                        <span class="manga-title-jp"><?= Html::encode($manga->title_jp) ?></span>
                    <?php endif; ?>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<?php if (!empty($popular)): ?>
<section class="manga-section">
    <h2>Popular</h2>
    <div class="manga-grid">
        <?php foreach ($popular as $manga): ?>
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
</section>
<?php endif; ?>
