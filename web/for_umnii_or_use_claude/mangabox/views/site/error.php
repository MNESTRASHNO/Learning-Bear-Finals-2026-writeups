<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var string $name */
/** @var string $message */
/** @var Exception $exception */

$this->title = $name;
?>

<div class="auth-page">
    <div class="auth-card" style="text-align: center;">
        <h1><?= Html::encode($this->title) ?></h1>
        <p style="color: var(--mg-muted); margin: 1.5rem 0;"><?= nl2br(Html::encode($message)) ?></p>
        <a href="<?= Yii::$app->homeUrl ?>" class="btn btn-accent">Back to Home</a>
    </div>
</div>
