<?php

use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var string $content */

$theme = null;
if (!Yii::$app->user->isGuest) {
    $loaded = Yii::$app->user->identity->getThemeSettings();
    if ($loaded instanceof \app\models\ThemeSettings) {
        $theme = $loaded;
    }
}
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title ? $this->title . ' — MangaBox' : 'MangaBox') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@400;600;700&family=Noto+Sans+JP:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= Url::to('@web/css/site.css') ?>" rel="stylesheet">
    <?php if ($theme): ?>
    <style>
        :root {
            <?= $theme ?>
        }
    </style>
    <?php endif; ?>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container">
        <a class="navbar-brand" href="<?= Url::home() ?>">
            <span class="brand-icon">&#x1F4D6;</span> MangaBox
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="<?= Url::to(['/manga/index']) ?>">Browse</a>
                </li>
            </ul>
            <ul class="navbar-nav">
                <?php if (Yii::$app->user->isGuest): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= Url::to(['/site/login']) ?>">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn-signup" href="<?= Url::to(['/site/signup']) ?>">Sign Up</a>
                    </li>
                <?php else: ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <?= Html::encode(Yii::$app->user->identity->username) ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end dropdown-menu-dark">
                            <li><a class="dropdown-item" href="<?= Url::to(['/profile/index']) ?>">Profile</a></li>
                            <li><a class="dropdown-item" href="<?= Url::to(['/profile/theme']) ?>">Theme Settings</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <?= Html::a('Logout', ['/site/logout'], [
                                    'class' => 'dropdown-item',
                                    'data-method' => 'post',
                                ]) ?>
                            </li>
                        </ul>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<main class="content-area">
    <div class="container">
        <?php foreach (Yii::$app->session->getAllFlashes() as $type => $message): ?>
            <div class="alert alert-<?= $type === 'error' ? 'danger' : $type ?> alert-dismissible fade show mt-3">
                <?= Html::encode($message) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endforeach; ?>
        <?= $content ?>
    </div>
</main>

<footer class="site-footer">
    <div class="container">
        <p>&copy; <?= date('Y') ?> MangaBox. All rights reserved.</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
