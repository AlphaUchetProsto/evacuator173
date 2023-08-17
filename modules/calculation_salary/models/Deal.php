<?php

namespace app\modules\calculation_salary\models;

use function Symfony\Component\String\u;

class Deal extends \app\models\bitrix\crm\Deal
{
    public $isFeeCity;
    public $isFeeIntercity;
    public $feeCity;
    public $feeIntercity;
    public $feeDailyAllowance;
    public $feeDifficultLoading;
    public $driverId;
    public $feeEmergencyCommissioner;
    public $totalFee;
    public $carId;

    public function rules()
    {
        $rules = collect(parent::rules());
        $rules->push([['feeCity', 'feeIntercity', 'feeDailyAllowance', 'feeDifficultLoading', 'feeEmergencyCommissioner'], 'default', 'value' => 0]);
        $rules->push([['feeEmergencyCommissioner'], 'filter', 'filter' => function($value){
            return preg_replace('/[^0-9]/', '', $value);
        }]);
        $rules->push([['driverId', 'carId'], 'default', 'value' => 0]);
        $rules->push([['isFeeCity', 'isFeeIntercity'], 'safe']);

        return $rules->toArray();
    }

    public static function mapFields()
    {
        $mapFields = collect(parent::mapFields());
        $mapFields->put('UF_CRM_1691053590', 'isFeeCity');
        $mapFields->put('UF_CRM_1691053555', 'isFeeIntercity');
        $mapFields->put('UF_CRM_1690977744916', 'feeDailyAllowance');
        $mapFields->put('UF_CRM_1642421245187', 'feeDifficultLoading');
        $mapFields->put('UF_CRM_1626443323', 'driverId');
        $mapFields->put('UF_CRM_1627036493126', 'feeEmergencyCommissioner');
        $mapFields->put('UF_CRM_1626442809025', 'carId');

        return $mapFields->toArray();
    }

    public function isCarTransporter($listCar)
    {
        return u($listCar[$this->carId])->containsAny('Автовоз');
    }
}