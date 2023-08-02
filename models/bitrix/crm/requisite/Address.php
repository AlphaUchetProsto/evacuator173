<?php

namespace app\models\bitrix\crm\requisite;

use app\models\bitrix\Bitrix;
use app\models\bitrix\traits\Collector;
use yii\base\Model;

class Address extends Model
{
    public $typeId;
    public $entityTypeId;
    public $entityId;
    public $address1;
    public $address2;
    public $city;
    public $postalCode;
    public $region;
    public $province;
    public $country;
    public $countryCode;

    use Collector;

    public function rules()
    {
        return [
            [['typeId', 'entityTypeId', 'entityId'], 'number'],
            [['address1', 'address2', 'city', 'postalCode', 'region', 'province', 'country', 'countryCode'], 'string'],
            [['entityTypeId'], 'default', 'value' => 8],
        ];
    }

    public function attributeLabels()
    {
        return [
            'address1' => 'Адрес',
        ];
    }


    public static function mapFields()
    {
        return [
            'TYPE_ID' => 'typeId',
            'ENTITY_TYPE_ID' => 'entityTypeId',
            'ENTITY_ID' => 'entityId',
            'ADDRESS_1' => 'address1',
            'ADDRESS_2' => 'address2',
            'CITY' => 'city',
            'POSTAL_CODE' => 'postalCode',
            'REGION' => 'region',
            'PROVINCE' => 'country',
            'COUNTRY' => 'name',
            'COUNTRY_CODE' => 'countryCode',
        ];
    }

    public function save()
    {
        $bitrix = new Bitrix;

        $result = $bitrix->request('crm.address.update', ['fields' => static::getParamsField($this)]);

        if(collect($result)->has('error'))
        {
            $result = $bitrix->request('crm.address.add', ['fields' => static::getParamsField($this)]);
        }

        return $result;
    }
}