<?php

namespace app\modules\calculation_salary\models;

use app\models\bitrix\crm\Contact;

class Driver extends Contact
{
    public $telegramId;
    public $sumFine;
    public $businessDays;

    public function rules()
    {
        $rules = collect(parent::rules());
        $rules->push([['telegramId', 'sumFine', 'businessDays'], 'safe']);
        $rules->push([['sumFine', 'businessDays'], 'default', 'value' => 0]);

        return $rules->toArray();
    }

    public static function mapFields()
    {
        $mapFields = collect(parent::mapFields());
        $mapFields->put('UF_CRM_1691049416362', 'telegramId');
        //$mapFields->put('UF_CRM_1691405841467', 'sumFine');
        //$mapFields->put('UF_CRM_1693232836445', 'businessDays');

        return $mapFields->toArray();
    }
}
