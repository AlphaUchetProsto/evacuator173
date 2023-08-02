<?php

namespace app\models\bitrix\crm\requisite;

use app\models\bitrix\Bitrix;
use app\models\bitrix\traits\Collector;
use yii\base\Model;

class BankRequisite extends Model
{
    public $id;
    public $entityId;
    public $title;
    public $bik;
    public $accNum;
    public $corAccNum;

    use Collector;

    public function rules()
    {
        return [
            [['id', 'entityId'], 'number'],
            [['title', 'bik', 'accNum', 'corAccNum'], 'string'],
            [['title'], 'required'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'title' => 'Название реквизита',
            'bik' => 'Бик',
            'accNum' => 'Расчетный счет',
            'corAccNum' => 'Кор.счет',
        ];
    }

    public static function mapFields()
    {
        return [
            'ID' => 'id',
            'NAME' => 'title',
            'RQ_BIK' => 'bik',
            'RQ_ACC_NUM' => 'accNum',
            'RQ_COR_ACC_NUM' => 'corAccNum',
            'ENTITY_ID' => 'entityId',
        ];
    }

    public function save()
    {
        $bitrix = new Bitrix;

        if(empty($this->id))
        {
            return $bitrix->request('crm.requisite.bankdetail.add', ['fields' => static::getParamsField($this)]);
        }

        return $bitrix->request('crm.requisite.bankdetail.update', ['ID' => $this->id, 'fields' => static::getParamsField($this)]);
    }
}
