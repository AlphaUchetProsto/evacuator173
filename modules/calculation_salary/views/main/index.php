<?php

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

$this->title = "Отчет по зарплате";

$this->params['breadcrumbs'][] = $this->title;

?>

<div class="grey-wrapper">
    <?php $form = ActiveForm::begin([
        'id' => 'filter-form',
        'method' => 'POST',
        'fieldConfig' => [
            'template' => "{label}{input}",
        ],
    ]) ?>
    <div class="row">
        <div class="col-auto">
            <?= $form->field($model, 'year', ['options' => ['class' => 'mb-0']])
                ->dropDownList($model::listYear(), ['prompt' => 'Выбрать год']);
            ?>
        </div>
        <div class="col-auto">
            <?= $form->field($model, 'month', ['options' => ['class' => 'mb-0']])
                ->dropDownList(Yii::$app->params['month'], ['prompt' => 'Выбрать месяц']);
            ?>
        </div>
        <div class="col-auto d-flex align-items-end">
            <?= Html::submitButton('Посчитать', ['class' => 'btn btn-primary']) ?>
        </div>
    </div>
    <?php $form::end(); ?>
</div>

<div class="wrapper-block mt-3">
    <table class="default-table">
        <thead>
        <tr>
            <th class="text-center" style="width: 20px;"></th>
            <th class="text-center column-small"></th>
            <th></th>
            <th colspan="2" class="text-center">Рабочие дни</th>
            <th class="text-center">Оклад</th>
            <th class="text-center">К оплате</th>
        </tr>
        <tr>
            <th class="text-center" style="width: 20px;"></th>
            <th class="text-center column-small">#</th>
            <th>ФИО водителя</th>
            <th class="text-center">Отработано</th>
            <th class="text-center">Всего</th>
            <th class="text-center">Руб.</th>
            <th class="text-center">Руб.</th>
        </tr>
        </thead>
        <tbody>
        <?php if(!empty($report)) : ?>
            <?php foreach ($report as $key => $item) :?>
                <tr>
                    <td class="text-center" style="width: 20px;">
                        <span class="open-details" aria-label="<?= $item->driver->id ?>"><i class="fas fa-chevron-up up-details"></i></span>
                    </td>
                    <td class="text-center"><span class="bg-blue"><?= $key + 1 ?><span></td>
                    <td><?= "{$item->driver->lastName} {$item->driver->name} {$item->driver->secondName}" ?></td>
                    <td class="text-center"><?= $item->driver->totalWorkedDays ?></td>
                    <td class="text-center"><?= $item->driver->totalWorkDay ?></td>
                    <td class="text-center">100</td>
                    <td class="text-center">200</td>
                </tr>
                <tr class="details data-<?= $item->driver->id ?> hide-block">
                    <td colspan="7">
                        <div style="margin-left: 70px;">
                            <h4 class="mt-4 mb-4">Расшифровка сделок</h4>

                            <table class="default-table">
                                <thead>
                                <tr>
                                    <th class="text-center column-small">#</th>
                                    <th>Название сделки</th>
                                    <th class="text-center">Сложная погрузка</th>
                                    <th class="text-center">Меж город, %</th>
                                    <th class="text-center">Город, %</th>
                                    <th class="text-center">Суточные, руб.</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($item->deals as $index => $deal) : ?>
                                    <tr>
                                        <td class="text-center column-small"><span class="bg-blue"><?= $index + 1 ?><span></td>
                                        <td><?= $deal->title ?></td>
                                        <td class="text-center"><?= $deal->feeDifficultLoading ?></td>
                                        <td class="text-center"><?= $deal->feeIntercity ?></td>
                                        <td class="text-center"><?= $deal->feeCity ?></td>
                                        <td class="text-center"><?= $deal->feeDailyAllowance ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td class="text-center" colspan="7">Нет данных</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>