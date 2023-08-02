<?php

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

$this->title = "Настройки";

$this->params['breadcrumbs'][] = $this->title;

?>

<article class="profile-form-block">
    <header>
        <h4>Константы расчета</h4>
        <p>Укажите значения переменных</p>
    </header>

    <?php $form = ActiveForm::begin([
        'id' => 'settings',
        'fieldConfig' => [
            'enableClientValidation' => false,
            'template' => "{input}{label}",
        ],
    ]) ?>

    <?= $form->field($model, 'feeExit', ['options' => ['class' => 'form-floating mb-3 mt-5']])
        ->textInput(['type' => 'number', 'placeholder' => $model->getAttributeLabel('feeExit')]);
    ?>
    <?= $form->field($model, 'feeCity', ['options' => ['class' => 'form-floating mb-3']])
        ->textInput(['type' => 'number', 'placeholder' => $model->getAttributeLabel('feeCity')]); ?>

    <?= $form->field($model, 'feeIntercity', ['options' => ['class' => 'form-floating mb-3']])
        ->textInput(['type' => 'number', 'placeholder' => $model->getAttributeLabel('feeIntercity')]); ?>

    <?= $form->field($model, 'feeDailyAllowance', ['options' => ['class' => 'form-floating mb-3']])
        ->textInput(['type' => 'number', 'placeholder' => $model->getAttributeLabel('feeDailyAllowance')]); ?>

    <?= $form->field($model, 'feeDifficultLoading', ['options' => ['class' => 'form-floating mb-3']])
        ->textInput(['type' => 'number', 'placeholder' => $model->getAttributeLabel('feeDifficultLoading')]); ?>

    <?= $form->field($model, 'feeUnion', ['options' => ['class' => 'form-floating mb-3']])
        ->textInput(['type' => 'number', 'placeholder' => $model->getAttributeLabel('feeUnion')]); ?>

    <?= $form->field($model, 'feePrepaidExpense', ['options' => ['class' => 'form-floating mb-3']])
        ->textInput(['type' => 'number', 'placeholder' => $model->getAttributeLabel('feePrepaidExpense')]); ?>

    <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>

    <?php $form::end() ?>
</article>
