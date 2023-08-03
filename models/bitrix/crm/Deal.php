<?php

namespace app\models\bitrix\crm;

use app\models\bitrix\Bitrix;
use app\models\bitrix\traits\Collector;
use Tightenco\Collect\Support\Collection;
use yii\base\Model;
use function Symfony\Component\String\u;

class Deal extends Model
{
    public $id;
    public $title;
    public $contactId;
    public $companyId;
    public $opportunity;
    public $stageId;
    public $categoryId;
    public $countMonthInstallment;
    public $codeWord;
    public $dateCreate;

    use Collector;

    public function rules()
    {
        return [
            [['id', 'contactId', 'companyId', 'opportunity', 'categoryId'], 'number'],
            [['title', 'stageId', 'countMonthInstallment', 'codeWord', 'dateCreate'], 'string'],
            ['categoryId', 'default', 'value' => 0],
        ];
    }

    public static function mapFields()
    {
        return [
            'ID' => 'id',
            'TITLE' => 'title',
            'CONTACT_ID' => 'contactId',
            'COMPANY_ID' => 'companyId',
            'OPPORTUNITY' => 'opportunity',
            'STAGE_ID' => 'stageId',
            'CATEGORY_ID' => 'categoryId',
            'DATE_CREATE' => 'dateCreate',
        ];
    }

    public static function findById($id)
    {
        $model = new static();
        $bitrix = new Bitrix();

        ['result' => $result] = $bitrix->request('crm.deal.list', [
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
}
