<?php

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

$this->title = "Регистрация";

?>


<?php if( Yii::$app->session->hasFlash('unsuccessful') ): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?php echo Yii::$app->session->getFlash('unsuccessful'); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Закрыть"></button>
    </div>
<?php endif;?>

<div class="wrapper-form shadow-sm rounded mb-4">
    <h4 class="mb-4 text-center text-uppercase">Регистрация</h4>
    <div class="alert alert-warning" role="alert">
        Перед регистрации обязательно подпишитесь на наш чат-бот и нажмите кнопку /start, иначе мы не сможем отправить вам логин и пароль.
        <a href="https://t.me/VashOtdelBot" target="_blank">Подписаться</a>
    </div>
    <?php $form = ActiveForm::begin([
        'id' => 'sign-up-form',
        'fieldConfig' => [
            'enableClientValidation' => false,
            'template' => "{input}{label}{error}",
        ],
    ]) ?>

    <?= $form->field($model, 'typeCompany', ['options' => ['class' => 'form-floating mb-3 type-company-select']])->dropDownList(\yii\helpers\ArrayHelper::map($presets, 'id', 'name'), ['prompt' => 'Выберите тип']); ?>
    <?= $form->field($model, 'titleCompany', ['options' => ['class' => 'form-floating mb-3']])->textInput(); ?>
    <?= $form->field($model, 'inn', ['options' => ['class' => 'form-floating mb-3 inn']])->textInput(); ?>
    <?= $form->field($model, 'ogrn', ['options' => ['class' => 'form-floating mb-3 hide-block ogrn']])->textInput(); ?>
    <?= $form->field($model, 'ogrnIp', ['options' => ['class' => 'form-floating mb-3 hide-block ogrnIp']])->textInput(); ?>
    <?= $form->field($model, 'phone', ['options' => ['class' => 'form-floating mb-3']])->widget(\yii\widgets\MaskedInput::class, [
        'mask' => '+7 (999) 999 99 99',
    ]); ?>
    <div class="alert alert-warning" role="alert">
        Для того, чтобы узнать ваш телеграм ID перейдите в специализированного бота и напиши /start, введите ваш логин в формате @nickname и скопируйте значение.
        <a href="https://t.me/get_any_telegram_id_bot" target="_blank">Открыть</a>
    </div>
    <?= $form->field($model, 'telegramLogin', ['options' => ['class' => 'form-floating mb-3']])->textInput(); ?>
    <?= Html::submitButton('Зарегистрироваться', ['class' => 'btn btn-primary w-100']) ?>
    <?php $form::end() ?>
</div>

<div class="auth-toolbar text-center">
    <?= Html::a('Есть уже аккаунт?', 'login'); ?>
</div>
