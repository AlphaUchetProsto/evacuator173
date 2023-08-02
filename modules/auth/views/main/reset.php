<?php

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

$this->title = "Сброс пароля";

?>

<div class="wrapper-form shadow-sm rounded mb-4">
    <h4 class="mb-4 text-center text-uppercase">Сброс пароля</h4>
    <?php $form = ActiveForm::begin([
        'id' => 'login-form',
        'fieldConfig' => [
            'enableClientValidation' => false,
            'template' => "{input}{label}",
        ],
    ]) ?>
    <?= $form->field($model, 'phone', ['options' => ['class' => 'form-floating mb-3']])->widget(\yii\widgets\MaskedInput::class, [
        'mask' => '+7 (999) 999 99 99',
    ]); ?>
    <?= Html::submitButton('Сбросить', ['class' => 'btn btn-primary w-100']) ?>
    <?php $form::end() ?>
</div>

<div class="auth-toolbar text-center">
    <?= Html::a('Есть уже аккаунт?', 'login'); ?> | <?= Html::a('Регистрация', 'sign-up'); ?>
</div>
