<?php

namespace app\modules\calculation_salary\models;

use yii\base\Model;

class Settings extends Model
{
    public $feeExit;
    public $feeCity;
    public $feeIntercity;
    public $feeDailyAllowance;
    public $feeDifficultLoading;
    public $feeUnion;
    public $feePrepaidExpense;

    public function attributeLabels()
    {
        return [
            'feeExit' => 'Выход, руб.',
            'feeCity' => 'Город,%',
            'feeIntercity' => 'Меж город, %',
            'feeDailyAllowance' => 'Суточные, руб.',
            'feeDifficultLoading' => 'Сложная погрузка, руб.',
            'feeUnion' => 'Профсоюз, руб.',
            'feePrepaidExpense' => 'Аванс',
        ];
    }

    public function rules()
    {
        return [
            [['feeExit', 'feeCity', 'feeIntercity', 'feeDailyAllowance', 'feeDifficultLoading', 'feeUnion', 'feePrepaidExpense'], 'number']
        ];
    }
}
