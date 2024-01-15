<?php

namespace app\modules\calculation_salary\models;

use app\models\bitrix\Bitrix;
use app\modules\calculation_salary\models\Settings;
use Tightenco\Collect\Support\Collection;
use yii\base\Model;
use app\models\TelegramBot;
use yii\helpers\ArrayHelper;

class SalaryReport extends Model
{
    public $driver;
    public $deals;
    public $totalFeeDeal;
    public $totalWorkedDays;
    public $salary;
    public $totalSalary;
    public $settings;
    public $totalSalaryWithoutFine;

    public function rules()
    {
        return [
            [['salary', 'totalFeeDeal', 'totalWorkedDays', 'totalSalary', 'totalSalaryWithoutFine'], 'default', 'value' => 0],
        ];
    }

    public static function create($date)
    {
        $result = new Collection();

        $deals = static::getDeals($date);
        $drivers = static::getDrivers(collect($deals)->keys());

        $additionalData = static::getAdditionalData($date);

        $settings = Settings::instance();

        foreach ($drivers as $driver)
        {
            $model = new static();
            $model->driver = $driver;
            $model->deals = $deals->get($driver->id);
            $model->settings = $settings;
            $model->totalWorkedDays = collect($model->deals)->groupBy(function ($deal){
                return date('Y-m-d', strtotime($deal->closedDate));
            })->count();

            $fine = collect($additionalData['fines'])->filter(function ($item) use($model) {
                return $item['contactId'] == $model->driver->id;
            });

            if($fine->isNotEmpty())
            {
                $model->driver->sumFine = $fine->values()->get(0)['value'];
            }

            $businessDay = collect($additionalData['business_days'])->filter(function ($item) use($model) {
                return $item['contactId'] == $model->driver->id;
            });

            if($businessDay->isNotEmpty())
            {
                $model->driver->businessDays = $businessDay->values()->get(0)['value'];
            }

            $model->validate();
            $model->calculateSalary();

            $result->push($model);
        }

        return $result->toArray();
    }

    public function calculateSalary()
    {
        $bitrix = new Bitrix();
        ['result' => $listCar] = $bitrix->request('crm.deal.fields');

        $listCar = ArrayHelper::map($listCar['UF_CRM_1626442809025']['items'], 'ID', 'VALUE');

        $tempDate = strtotime('now');
        $minusBonus = 0;

        foreach ($this->deals as &$deal)
        {
            $deal->totalFee = $deal->feeDifficultLoading;

            if(!$deal->isCarTransporter($listCar))
            {
                if($deal->isFeeCity == 245)
                {
                    $deal->feeCity = (($deal->opportunity - $deal->feeEmergencyCommissioner - $deal->feeDifficultLoading) / 100) * $this->settings->feeCity;
                    $this->salary += $deal->feeCity;
                    $deal->totalFee += $deal->feeCity;
                }

                if($deal->isFeeIntercity == 241)
                {
                    $deal->feeIntercity = (($deal->opportunity - $deal->feeEmergencyCommissioner - $deal->feeDifficultLoading) / 100) * $this->settings->feeIntercity;
                    $this->salary += $deal->feeIntercity;
                    $deal->totalFee += $deal->feeIntercity;
                }
            }
            else
            {
                $deal->totalFee += (($deal->opportunity - $deal->feeDifficultLoading) / 100) * $this->settings->carTransporter;

                if(strtotime(date('Y-m-d', strtotime($deal->closedDate))) !== $tempDate)
                {
                    $minusBonus += $this->settings->feeExit;
                    $tempDate = strtotime(date('Y-m-d', strtotime($deal->closedDate)));
                }
            }

            $this->totalFeeDeal += $deal->totalFee;
        }

        $this->totalSalary = ((($this->totalWorkedDays + $this->driver->businessDays) * $this->settings->feeExit) + $this->totalFeeDeal);
        $this->totalSalary -= $minusBonus;
        $this->salary = $this->totalSalary - $this->settings->feePrepaidExpense;
        $this->totalSalaryWithoutFine = $this->totalSalary;

        if($this->driver->sumFine > 0)
        {
            $this->totalSalary -= $this->driver->sumFine;
            $this->salary -= $this->driver->sumFine;
        }
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
                    '>=CLOSEDATE' => date('Y-m-d 00:00', strtotime('-1 month', strtotime($date))),
                    '<CLOSEDATE' => $date,
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
            $deals = $deals->sortBy('closedDate');

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
            $response = $bitrix->batchRequest($commandRow->toArray(), false);
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

    public static function getAdditionalData($date)
    {
        $client = App::instance();

        $commands['fines'] = $client->buildCommand('entity.item.get', [
            'ENTITY' => 'fine',
            'FILTER' => [
                'NAME' => \Yii::$app->params['month'][date('n', strtotime($date))] . ' ' . date('Y', strtotime($date)),
            ],
        ]);

        $commands['business_days'] = $client->buildCommand('entity.item.get', [
            'ENTITY' => 'business_days',
            'FILTER' => [
                'NAME' => \Yii::$app->params['month'][date('n', strtotime($date))] . ' ' . date('Y', strtotime($date)),
            ],
        ]);

        ['result' => ['result' => $response]] = $client->batchRequest($commands);

        return collect($response)->transform(function ($items){
            return collect($items)->map(function ($item){
                return $item['PROPERTY_VALUES'];
            })->toArray();
        })->toArray();
    }
}
