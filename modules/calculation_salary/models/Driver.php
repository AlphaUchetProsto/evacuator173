<?php

namespace app\modules\calculation_salary\models;

use app\models\bitrix\crm\Contact;

class Driver extends Contact
{
    public $telegramId;

    public function rules()
    {
        $rules = collect(parent::rules());
        $rules->push([['telegramId'], 'safe']);

        return $rules->toArray();
    }

    public static function mapFields()
    {
        $mapFields = collect(parent::mapFields());
        $mapFields->put('UF_CRM_1691049416362', 'telegramId');

        return $mapFields->toArray();
    }
}
