<?php

namespace app\models\bitrix\crm;

use app\models\bitrix\Bitrix;
use app\models\bitrix\traits\Collector;
use yii\base\Model;
use function Symfony\Component\String\u;

class Product extends Model
{
    public $id;
    public $name;
    public $catalogId;
    public $sectionId;
    public $description;
    public $price;
    public $currencyId;

    use Collector;

    public function rules()
    {
        return [
            [['id', 'catalogId', 'sectionId'], 'number'],
            [['name', 'description', 'currencyId'], 'string'],
            [['price'], 'filter', 'filter' => function($item){
                return u($item)->replace(',', '.')->toString();
            }],
        ];
    }

    public static function mapFields()
    {
        return [
            'ID' => 'id',
            'NAME' => 'name',
            'CATALOG_ID' => 'catalogId',
            'SECTION_ID' => 'sectionId',
            'DESCRIPTION' => 'description',
            'PRICE' => 'price',
            'CURRENCY_ID' => 'currencyId',
        ];
    }

    public static function findById($id)
    {
        $model = new static();
        $bitrix = new Bitrix;

        ['result' => $result] = $bitrix->request('crm.product.get', ['ID' =>  $id]);

        if(!empty($result) && static::collect($model, $result))
        {
            return $model;
        }

        return false;
    }

    public static function getList($filter = [])
    {
        $model = new static();
        $bitrix = new Bitrix;

        ['result' => $result] = $bitrix->request('crm.product.list', [
            'filter' => $filter,
            'start' => -1,
            'select' => collect(static::mapFields())->keys()->toArray(),
        ]);

        return $model::multipleCollect(static::class, $result);
    }

    public function save()
    {
        $bitrix = new Bitrix;

        if(empty($this->id))
        {
            return $bitrix->request('crm.product.add', ['fields' => static::getParamsField($this)]);
        }

        return $bitrix->request('crm.product.update', ['id' => $this->id, 'fields' => static::getParamsField($this)]);
    }

    public function delete()
    {
        $bitrix = new Bitrix;

        return $bitrix->request('crm.product.delete', ['id' => $this->id]);
    }
}
