<?php

namespace app\modules\calculation_salary\models;

class Deal extends \app\models\bitrix\crm\Deal
{
    public $feeCity;
    public $feeIntercity;
    public $feeDailyAllowance;
    public $feeDifficultLoading;
    public $driverId;

    public function rules()
    {
        $rules = collect(parent::rules());
        $rules->push([['feeCity', 'feeIntercity', 'feeDailyAllowance', 'feeDifficultLoading'], 'default', 'value' => 0]);
        $rules->push(['driverId', 'default', 'value' => 0]);

        return $rules->toArray();
    }

    public static function mapFields()
    {
        $mapFields = collect(parent::mapFields());
        $mapFields->put('UF_CRM_1690977720652', 'feeCity');
        $mapFields->put('UF_CRM_1690977700448', 'feeIntercity');
        $mapFields->put('UF_CRM_1690977744916', 'feeDailyAllowance');
        $mapFields->put('UF_CRM_1642421245187', 'feeDifficultLoading');
        $mapFields->put('UF_CRM_1626443323', 'driverId');

        return $mapFields->toArray();
    }
}