<?php

namespace app\modules\calculation_salary\models;

use app\models\bitrix\crm\Contact;

class Driver extends Contact
{
    public $totalWorkDay;
    public $totalWorkedDays;

    public function rules()
    {
        $rules = collect(parent::rules());
        $rules->push([['totalWorkDay', 'totalWorkedDays'], 'default', 'value' => 0]);

        return $rules->toArray();
    }

    public static function mapFields()
    {
        $mapFields = collect(parent::mapFields());
        $mapFields->put('UF_CRM_1690978626189', 'totalWorkDay');
        $mapFields->put('UF_CRM_1690978617774', 'totalWorkedDays');

        return $mapFields->toArray();
    }
}
