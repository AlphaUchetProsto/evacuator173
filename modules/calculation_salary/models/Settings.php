<?php

namespace app\modules\calculation_salary\models;

use yii\base\Model;
use app\modules\calculation_salary\models\App;

class Settings extends Model
{
    public $feeExit;
    public $feePrepaidExpense;
    public $feeCity;
    public $feeIntercity;

    public function attributeLabels()
    {
        return [
            'feeExit' => 'Выход, руб.',
            'feePrepaidExpense' => 'Аванс',
            'feeCity' => 'Город, %',
            'feeIntercity' => 'Межгород, %',
        ];
    }

    public function rules()
    {
        return [
            [['feeExit', 'feePrepaidExpense'], 'number'],
            [['feeExit', 'feePrepaidExpense', 'feeCity', 'feeIntercity'], 'default', 'value' => 0],
        ];
    }

    public static function instance($refresh = false)
    {
        /*$client = App::instance();*/
        //$response = $client->request('entity.add', ['ENTITY' => 'settings', 'NAME' => 'Константы расчета']);
        //$response = $client->request('entity.item.property.add', ['ENTITY' => 'settings', 'PROPERTY' => 'feeExit', 'NAME' => 'Выход, руб.', 'TYPE' => 'S']);
        //$response = $client->request('entity.item.property.add', ['ENTITY' => 'settings', 'PROPERTY' => 'feePrepaidExpense', 'NAME' => 'Аванс', 'TYPE' => 'S']);
        /*$response = $client->request('entity.item.add', [
            'ENTITY' => 'settings',
            'NAME' => 'Параметры',
            'PROPERTY_VALUES' => [
                'feeExit' => 0,
                'feePrepaidExpense' => 0,
            ],
        ]);

        dd($response);*/

        $client = App::instance();
        $model = new static();

        ['result' => [0 => $response]] = $client->request('entity.item.get', ['ENTITY' => 'settings', 'FILTER' => ['ID' => 19]]);

        $model->feeExit = $response['PROPERTY_VALUES']['feeExit'];
        $model->feePrepaidExpense = $response['PROPERTY_VALUES']['feePrepaidExpense'];
        $model->feeCity = $response['PROPERTY_VALUES']['feeCity'];
        $model->feeIntercity = $response['PROPERTY_VALUES']['feeIntercity'];

        $model->validate();

        return $model;
    }

    public function save()
    {
        $client = App::instance();

        return $client->request('entity.item.update', [
            'ENTITY' => 'settings',
            'ID' => 19,
            'PROPERTY_VALUES' => [
                'feeExit' => $this->feeExit,
                'feePrepaidExpense' => $this->feePrepaidExpense,
                'feeCity' => $this->feeCity,
                'feeIntercity' => $this->feeIntercity,
            ],
        ]);
    }
}
