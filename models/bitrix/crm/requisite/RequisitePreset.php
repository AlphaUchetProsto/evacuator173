<?php

namespace app\models\bitrix\crm\requisite;

use app\models\bitrix\Bitrix;
use app\models\bitrix\traits\Collector;
use yii\base\Model;

class RequisitePreset extends Model
{
    public $id;
    public $entityTypeId;
    public $countryId;
    public $name;
    public $dateCreate;

    use Collector;

    public function rules()
    {
        return [
            [['id', 'entityTypeId', 'countryId'], 'number'],
            [['name', 'dateCreate'], 'string'],
        ];
    }

    public static function mapFields()
    {
        return [
            'ID' => 'id',
            'ENTITY_TYPE_ID' => 'entityTypeId',
            'COUNTRY_ID' => 'countryId',
            'NAME' => 'name',
            'DATE_CREATE' => 'dateCreate',
        ];
    }

    public static function getList()
    {
        $bitrix = new Bitrix;
        $model = new static();

        ['result' => $result] = $bitrix->request('crm.requisite.preset.list');

        return $model::multipleCollect(static::class, $result);
    }
}