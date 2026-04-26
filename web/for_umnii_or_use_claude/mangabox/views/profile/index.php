<?php

use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var app\models\User $user */

$this->title = 'Profile';
?>

<div class="profile-page">
    <div class="profile-card">
        <div class="profile-header">
            <div class="profile-avatar">
                <?= Html::encode(mb_strtoupper(mb_substr($user->username, 0, 2))) ?>
            </div>
            <div class="profile-info">
                <h1><?= Html::encode($user->username) ?></h1>
                <span class="profile-email"><?= Html::encode($user->email) ?></span>
                <span class="profile-joined">Joined <?= Yii::$app->formatter->asRelativeTime($user->created_at) ?></span>
            </div>
        </div>

        <div class="profile-actions">
            <a href="<?= Url::to(['/profile/theme']) ?>" class="btn btn-outline">Theme Settings</a>
        </div>
    </div>
</div>
