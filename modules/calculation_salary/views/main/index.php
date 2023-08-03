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
        <?php if(!empty($report)) : ?>
        <div class="col-auto d-flex align-items-end">
        <?= Html::button('Скачать', ['class' => 'btn btn-primary download-report']) ?>
        </div>
        <?php endif; ?>
    </div>
    <?php $form::end(); ?>
</div>

<div class="wrapper-block mt-3">
    <table class="default-table">
        <thead>
        <tr>
            <th class="text-center" style="width: 20px;"></th>
            <th class="text-center column-small">#</th>
            <th>ФИО водителя</th>
            <th class="text-center">Отработано дней</th>
            <th class="text-center">Аванс, руб.</th>
            <th class="text-center">ЗП с авансом, руб.</th>
            <th class="text-center">К выплате, руб.</th>
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
                    <td><a href="javascript::void()" onclick="BX24.openPath('/crm/contact/details/<?= $item->driver->id ?>/');"><?= "{$item->driver->lastName} {$item->driver->name} {$item->driver->secondName}" ?></a></td>
                    <td class="text-center"><?= $item->totalWorkedDays ?></td>
                    <td class="text-center"><?= number_format($item->settings->feePrepaidExpense, 2, ',', ' ') ?></td>
                    <td class="text-center"><?= number_format($item->totalSalary, 2, ',', ' ') ?></td>
                    <td class="text-center"><?= number_format($item->salary, 2, ',', ' ') ?></td>
                </tr>
                <tr class="details data-<?= $item->driver->id ?> hide-block">
                    <td colspan="7">
                        <div style="margin-left: 50px;">
                            <h4 class="mt-4 mb-4">Расшифровка сделок</h4>

                            <table class="default-table">
                                <thead>
                                <tr>
                                    <th class="text-center column-small">#</th>
                                    <th style="width: 30%;">Название сделки</th>
                                    <th class="text-center">Сумма, руб.</th>
                                    <th class="text-center">Сложная погрузка</th>
                                    <th class="text-center">Меж город, руб</th>
                                    <th class="text-center">Город, руб</th>
                                    <th class="text-center">Аварийный комиссар, руб</th>
                                    <th class="text-center">Суточные, руб.</th>
                                    <th class="text-center">Итого комиссия, руб.</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($item->deals as $index => $deal) : ?>
                                    <tr>
                                        <td class="text-center column-small"><span class="bg-blue"><?= $index + 1 ?><span></td>
                                        <td>
                                            <a href="javascript::void()" onclick="BX24.openPath('/crm/deal/details/<?= $deal->id ?>/');"><?= $deal->title ?></a>
                                        </td>
                                        <td class="text-center"><?= $deal->opportunity ?></td>
                                        <td class="text-center"><?= number_format($deal->feeDifficultLoading, 2, ',', ' ') ?></td>
                                        <td class="text-center"><?= number_format($deal->feeIntercity, 2, ',', ' ') ?></td>
                                        <td class="text-center"><?= number_format($deal->feeCity, 2, ',', ' ') ?></td>
                                        <td class="text-center"><?= number_format($deal->feeEmergencyCommissioner, 2, ',', ' ') ?></td>
                                        <td class="text-center"><?= number_format($deal->feeDailyAllowance, 2, ',', ' ') ?></td>
                                        <td class="text-center"><?= number_format($deal->totalFee, 2, ',', ' ') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                <tr>
                                    <td colspan="8" style="text-align: right; font-weight: bold;">Итого, руб: </td>
                                    <td class="text-center" style="font-weight: bold;"><?= number_format($item->totalFeeDeal, 2, ',', ' ') ?></td>
                                </tr>
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