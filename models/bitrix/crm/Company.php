<?php

namespace app\models\bitrix\crm;

use app\models\bitrix\Bitrix;
use app\models\bitrix\traits\Collector;
use Tightenco\Collect\Support\Collection;
use yii\base\Model;
use function Symfony\Component\String\u;

class Company extends Model
{
    public $id;
    public $title;
    public $phone;
    public $im;

    use Collector;

    public function rules()
    {
        return [
            ['id', 'number'],
            ['title', 'string'],
            [['phone', 'im'], 'default', 'value' => []],
        ];
    }

    public static function mapFields()
    {
        return [
            'ID' => 'id',
            'TITLE' => 'title',
            'PHONE' => 'phone',
            'IM' => 'im',
        ];
    }

    public static function findById($id)
    {
        $model = new static();
        $bitrix = new Bitrix();

        ['result' => $result] = $bitrix->request('crm.company.list', [
            'filter' => ['=ID' => $id],
            'start' => -1,
            'select' => collect(static::mapFields())->keys()->toArray(),
        ]);

        if(!empty($result) && static::collect($model, $result[0]))
        {
            return $model;
        }

        return false;
    }

    public static function findDuplicateByPhone($phone)
    {
        $phones = new Collection();
        $phones->push($phone);
        $phones->push(u($phone)->after('+')->toString());
        $phones->push(u($phone)->after('+')->slice(1)->toString());

        $bitrix = new Bitrix;
        ["result" => $result] = $bitrix->request('crm.duplicate.findbycomm', ['type' => 'PHONE', 'entity_type' => 'COMPANY', 'values' => $phones->toArray()]);

        return $result;
    }

    public static function findByPhone($phone)
    {
        $model = new static();
        $bitrix = new Bitrix();

        ['result' => $result] = $bitrix->request('crm.company.list', [
            'filter' => ['PHONE' => $phone],
            'start' => -1,
            'select' => collect(static::mapFields())->keys()->toArray(),
        ]);

        if(!empty($result) && static::collect($model, $result[0]))
        {
            return $model;
        }

        return false;
    }
}
