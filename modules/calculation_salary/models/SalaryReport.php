<?php

namespace app\modules\calculation_salary\models;

use app\models\bitrix\Bitrix;
use Tightenco\Collect\Support\Collection;
use yii\base\Model;

class SalaryReport extends Model
{
    public $driver;
    public $deals;
    public $salary;

    public static function create($date)
    {
        $result = new Collection();

        $deals = static::getDeals($date);
        $drivers = static::getDrivers(collect($deals)->keys());

        foreach ($drivers as $driver)
        {
            $model = new static();
            $model->driver = $driver;
            $model->deals = $deals->get($driver->id);

            $result->push($model);
        }

        return $result->toArray();
    }

    public static function getDeals($date)
    {
        $bitrix = new Bitrix();

        $dealId = 0;
        $finish = false;
        $deals = new Collection();

        while (!$finish)
        {
            ['result' => $response] = $bitrix->request('crm.deal.list', [
                'order' => ['ID' => 'ASC'],
                'filter' => [
                    '=STAGE_ID' => 'WON',
                    '>ID' => $dealId,
                    '>=DATE_CREATE' => date('Y-m-d', strtotime('-1 month', strtotime($date))),
                    '<=DATE_CREATE' => $date,
                ],
                'select' => collect(Deal::mapFields())->keys()->toArray(),
                'start' => '-1'
            ]);

            if(count($response) > 0)
            {
                $response = Deal::multipleCollect(new Deal(), $response);

                $deals = $deals->merge($response);
                $dealId = $response[count($response) - 1]->id;
            }
            else
            {
                $finish = true;
            }
        }

        if($deals->isNotEmpty())
        {
            $deals = $deals->groupBy(function ($deal){
                return $deal->driverId;
            });
        }

        return $deals;
    }

    public static function getDrivers($contactsIds)
    {
        $bitrix = new Bitrix();
        $drivers = new Collection();

        $commandRows = collect($contactsIds)->map(function ($id) use($bitrix){
            return $bitrix->buildCommand('crm.contact.get', ['ID' => $id]);
        })->chunk(50);

        foreach ($commandRows as $commandRow)
        {
            $response = $bitrix->batchRequest($commandRow->toArray());
            $response = Driver::multipleCollect(new Driver(), $response['result']['result']);

            $drivers = $drivers->merge($response);
        }

        return $drivers;
    }
}
