<?php

use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var app\models\Manga $manga */
/** @var app\models\Comment[] $comments */
/** @var app\models\Rating|null $userRating */
/** @var float|null $avgRating */
/** @var int $ratingCount */

$this->title = $manga->title;
?>

<div class="manga-detail">
    <div class="manga-detail-header">
        <div class="manga-detail-cover">
            <?php if ($manga->cover_image): ?>
                <img src="<?= Html::encode($manga->cover_image) ?>" alt="<?= Html::encode($manga->title) ?>">
            <?php else: ?>
                <div class="cover-placeholder cover-placeholder-lg"><?= Html::encode(mb_substr($manga->title, 0, 1)) ?></div>
            <?php endif; ?>
        </div>
        <div class="manga-detail-info">
            <h1><?= Html::encode($manga->title) ?></h1>
            <?php if ($manga->title_jp): ?>
                <p class="manga-title-jp"><?= Html::encode($manga->title_jp) ?></p>
            <?php endif; ?>

            <div class="rating-display">
                <?php if ($avgRating): ?>
                    <span class="rating-score"><?= number_format($avgRating, 1) ?></span>
                    <span class="rating-max">/ 10</span>
                    <span class="rating-count">(<?= $ratingCount ?> <?= $ratingCount === 1 ? 'rating' : 'ratings' ?>)</span>
                <?php else: ?>
                    <span class="rating-count">No ratings yet</span>
                <?php endif; ?>
            </div>

            <div class="manga-meta">
                <span class="meta-item"><strong>Author:</strong> <?= Html::encode($manga->author) ?></span>
                <span class="meta-item"><strong>Status:</strong> <?= Html::encode(ucfirst($manga->status)) ?></span>
            </div>
            <?php if ($manga->genres): ?>
                <div class="genre-tags">
                    <?php foreach ($manga->getGenreList() as $genre): ?>
                        <span class="genre-tag"><?= Html::encode(trim($genre)) ?></span>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            <div class="manga-description">
                <p><?= Html::encode($manga->description) ?></p>
            </div>

            <?php if (!Yii::$app->user->isGuest): ?>
                <div class="rating-form">
                    <form method="post" class="inline-form">
                        <input type="hidden" name="<?= Yii::$app->request->csrfParam ?>" value="<?= Yii::$app->request->csrfToken ?>">
                        <input type="hidden" name="action" value="rate">
                        <label class="form-label">Your rating:</label>
                        <select name="score" class="form-control form-control-sm rating-select">
                            <option value="">--</option>
                            <?php for ($i = 1; $i <= 10; $i++): ?>
                                <option value="<?= $i ?>" <?= $userRating && $userRating->score == $i ? 'selected' : '' ?>><?= $i ?></option>
                            <?php endfor; ?>
                        </select>
                        <button type="submit" class="btn btn-accent btn-sm">Rate</button>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="chapter-list">
        <h2>Chapters</h2>
        <?php if ($manga->chapters): ?>
            <div class="chapter-table">
                <?php foreach ($manga->chapters as $chapter): ?>
                    <a href="<?= Url::to(['/chapter/read', 'mangaId' => $manga->id, 'id' => $chapter->id]) ?>" class="chapter-row">
                        <span class="chapter-number">Chapter <?= Html::encode($chapter->chapter_number) ?></span>
                        <span class="chapter-title"><?= Html::encode($chapter->title ?: '') ?></span>
                        <span class="chapter-date"><?= Yii::$app->formatter->asRelativeTime($chapter->created_at) ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="empty-state">No chapters available yet.</p>
        <?php endif; ?>
    </div>

    <div class="comments-section" id="comments">
        <h2>Comments (<?= count($comments) ?>)</h2>

        <?php if (!Yii::$app->user->isGuest): ?>
            <div class="comment-form">
                <form method="post">
                    <input type="hidden" name="<?= Yii::$app->request->csrfParam ?>" value="<?= Yii::$app->request->csrfToken ?>">
                    <input type="hidden" name="action" value="comment">
                    <textarea name="body" class="form-control" rows="3" placeholder="Leave a comment..." required></textarea>
                    <button type="submit" class="btn btn-accent btn-sm mt-2">Post Comment</button>
                </form>
            </div>
        <?php else: ?>
            <p class="comment-login-hint"><?= Html::a('Login', ['/site/login']) ?> to leave a comment.</p>
        <?php endif; ?>

        <div class="comment-list">
            <?php foreach ($comments as $comment): ?>
                <div class="comment-item">
                    <div class="comment-header">
                        <span class="comment-author"><?= Html::encode($comment->user->username ?? 'Unknown') ?></span>
                        <span class="comment-date"><?= Yii::$app->formatter->asRelativeTime($comment->created_at) ?></span>
                    </div>
                    <div class="comment-body"><?= Html::encode($comment->body) ?></div>
                </div>
            <?php endforeach; ?>
            <?php if (empty($comments)): ?>
                <p class="empty-state">No comments yet. Be the first!</p>
            <?php endif; ?>
        </div>
    </div>
</div>
