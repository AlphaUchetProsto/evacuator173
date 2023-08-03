<?php

namespace app\modules\calculation_salary\models;

use app\models\bitrix\Bitrix;
use app\modules\calculation_salary\models\Settings;
use Tightenco\Collect\Support\Collection;
use yii\base\Model;
use app\models\TelegramBot;

class SalaryReport extends Model
{
    public $driver;
    public $deals;
    public $totalFeeDeal;
    public $totalWorkedDays;
    public $salary;
    public $totalSalary;
    public $settings;

    public function rules()
    {
        return [
            [['salary', 'totalFeeDeal', 'totalWorkedDays', 'totalSalary'], 'default', 'value' => 0],
        ];
    }

    public static function create($date)
    {
        $result = new Collection();

        $deals = static::getDeals($date);
        $drivers = static::getDrivers(collect($deals)->keys());
        $settings = Settings::instance();

        foreach ($drivers as $driver)
        {
            $model = new static();
            $model->driver = $driver;
            $model->deals = $deals->get($driver->id);
            $model->settings = $settings;
            $model->totalWorkedDays = collect($model->deals)->groupBy(function ($deal){
                return date('Y-m-d', strtotime($deal->dateCreate));
            })->count();

            $model->validate();
            $model->calculateSalary();

            $result->push($model);
        }

        return $result->toArray();
    }

    public function calculateSalary()
    {
        foreach ($this->deals as &$deal)
        {
            $deal->totalFee = $deal->feeDifficultLoading + $deal->feeDailyAllowance;

            if($deal->isFeeCity == 245)
            {
                $deal->feeCity = (($deal->opportunity - $deal->feeEmergencyCommissioner) / 100) * $this->settings->feeCity;
                $this->salary += $deal->feeCity;
                $deal->totalFee += $deal->feeCity;
            }

            if($deal->isFeeIntercity == 241)
            {
                $deal->feeIntercity = (($deal->opportunity - $deal->feeEmergencyCommissioner) / 100) * $this->settings->feeIntercity;
                $this->salary += $deal->feeIntercity;
                $deal->totalFee += $deal->feeIntercity;
            }

            $this->totalFeeDeal += $deal->totalFee;
        }

        $this->totalSalary = (($this->totalWorkedDays * $this->settings->feeExit) + $this->totalFeeDeal);
        $this->salary = $this->totalSalary - $this->settings->feePrepaidExpense;
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

    public static function sendReport()
    {
        $bot = new TelegramBot('6407080374:AAEe00FQ7kYzzEhPv3C_pfiG0RDJt_5f0xM');
        $reports = static::create(date('Y-9-01'));

        //$result = static::create(date('Y-m-01'));

        foreach ($reports as $report)
        {
            if(!empty($report->driver->telegramId))
            {
                $message = "Твоя зарплата - {$report->salary} руб.";
                $bot->sendMessage($report->driver->telegramId, $message);
            }
        }
    }

    public function createExcel()
    {
        $bot = new TelegramBot('6407080374:AAEe00FQ7kYzzEhPv3C_pfiG0RDJt_5f0xM');
        $reports = static::create(date('Y-9-01'));

        //$result = static::create(date('Y-m-01'));

        foreach ($reports as $report)
        {
            if(!empty($report->driver->telegramId))
            {
                $message = "Твоя зарплата - {$report->salary} руб.";
                $bot->sendMessage($report->driver->telegramId, $message);
            }
        }
    }
}
