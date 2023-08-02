<?php

namespace app\models\bitrix\crm\requisite;

use app\models\bitrix\Bitrix;
use app\models\bitrix\traits\Collector;
use yii\base\Model;

class Requisite extends Model
{
    public $id;
    public $entityTypeId;
    public $entityId;
    public $presetId;
    public $dateCreate;
    public $inn;
    public $ogrn;
    public $name;
    public $directorName;
    public $ogrnIp;

    use Collector;

    public function rules()
    {
        return [
            [['id', 'entityTypeId', 'entityId', 'presetId'], 'number'],
            [['dateCreate', 'inn', 'ogrn', 'name', 'directorName', 'ogrnIp'], 'string'],
        ];
    }

    public static function mapFields()
    {
        return [
            'ID' => 'id',
            'ENTITY_TYPE_ID' => 'entityTypeId',
            'ENTITY_ID' => 'entityId',
            'PRESET_ID' => 'presetId',
            'DATE_CREATE' => 'dateCreate',
            'RQ_INN' => 'inn',
            'RQ_OGRN' => 'ogrn',
            'RQ_OGRNIP' => 'ogrnIp',
            'NAME' => 'name',
            'RQ_DIRECTOR' => 'directorName',
        ];
    }

    public static function getList()
    {
        $bitrix = new Bitrix;
        $model = new static();

        ['result' => $result] = $bitrix->request('crm.requisite.list');

        return $model::multipleCollect(static::class, $result);
    }

    public static function findByCompanyId($companyId)
    {
        $bitrix = new Bitrix;
        $model = new static();

        ['result' => $result] = $bitrix->request('crm.requisite.list', ['filter' => ['ENTITY_TYPE_ID' => 4, 'ENTITY_ID' => $companyId]]);

        if(!empty($result) && $model::collect($model, $result[0]))
        {
            return $model;
        }

        return false;
    }

    public function save()
    {
        $bitrix = new Bitrix;

        return $bitrix->request('crm.requisite.update', ['id' => $this->id, 'fields' => static::getParamsField($this)]);
    }
}