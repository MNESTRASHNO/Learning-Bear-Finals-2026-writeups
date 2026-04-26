<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\SignupForm $model */

$this->title = 'Sign Up';
?>

<div class="auth-page">
    <div class="auth-card">
        <h1><?= Html::encode($this->title) ?></h1>

        <?php $form = ActiveForm::begin([
            'id' => 'signup-form',
            'options' => ['class' => 'auth-form'],
        ]); ?>

        <?= $form->field($model, 'username')->textInput(['placeholder' => 'Username']) ?>
        <?= $form->field($model, 'email')->input('email', ['placeholder' => 'Email']) ?>
        <?= $form->field($model, 'password')->passwordInput(['placeholder' => 'Password']) ?>
        <?= $form->field($model, 'passwordRepeat')->passwordInput(['placeholder' => 'Confirm Password']) ?>

        <div class="form-group">
            <?= Html::submitButton('Create Account', ['class' => 'btn btn-accent btn-block']) ?>
        </div>

        <p class="auth-link">
            Already have an account? <?= Html::a('Login', ['/site/login']) ?>
        </p>

        <?php ActiveForm::end(); ?>
    </div>
</div>
