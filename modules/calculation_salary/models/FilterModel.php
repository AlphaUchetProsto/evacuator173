<?php

namespace app\modules\calculation_salary\models;

use Tightenco\Collect\Support\Collection;
use yii\base\Model;

class FilterModel extends Model
{
    public $year;
    public $month;
    public $day;

    public function attributeLabels()
    {
        return [
            'year' => 'Год',
            'month' => 'Месяц',
        ];
    }

    public function rules()
    {
        return [
            [['year', 'month'], 'required'],
            [['year', 'month'], 'number'],
            [['day'], 'default', 'value' => 1],
        ];
    }

    public static function listYear()
    {
        $listYear = new Collection();

        for ($i = 2021; $i <= date('Y'); $i++)
        {
            $listYear->push($i);
        }

        return $listYear->sortDesc();
    }

    public function getFilterDay()
    {
        $year = static::listYear()[$this->year];

        return date("Y-m-d", strtotime("{$year}-{$this->month}-{$this->day}"));
    }

    public function filter()
    {
        return SalaryReport::create($this->getFilterDay());
    }

    public function createExcel()
    {
        $reports = SalaryReport::create($this->getFilterDay());

        $filePath = 'src/calculation_salary/reports.csv';

        $text = iconv('utf-8//IGNORE', 'windows-1251//IGNORE', "ФИО водителя;Отработано дней;Штрафы;Аванс, руб.;ЗП с авансом, руб.;К выплате, руб.;\r\n");
        file_put_contents($filePath, $text);

        foreach ($reports as $index => $item)
        {
            $feePrepaidExpense = number_format($item->settings->feePrepaidExpense, 2, ',', ' ');
            $totalSalary = number_format($item->totalSalary, 2, ',', ' ');
            $salary = number_format($item->salary, 2, ',', ' ');

            $text = iconv('utf-8//IGNORE', 'windows-1251//IGNORE', "{$item->driver->lastName} {$item->driver->name} {$item->driver->secondName};{$item->totalWorkedDays};{$item->driver->sumFine};{$feePrepaidExpense};{$totalSalary};{$salary};\r\n");
            file_put_contents($filePath, $text, FILE_APPEND);
        }

        return $filePath;
    }

    public function createTranscriptTable($indexReport)
    {
        $reports = SalaryReport::create($this->getFilterDay());
        $filePath = 'src/calculation_salary/transcript.csv';

        $text = iconv('utf-8//IGNORE', 'windows-1251//IGNORE', "ФИО водителя;{$reports[$indexReport]->driver->lastName}{$reports[$indexReport]->driver->name}{$reports[$indexReport]->driver->secondName};\r\n");
        file_put_contents($filePath, $text);

        $text = iconv('utf-8//IGNORE', 'windows-1251//IGNORE', ";\r\n");
        file_put_contents($filePath, $text, FILE_APPEND);

        $text = iconv('utf-8//IGNORE', 'windows-1251//IGNORE', "Дата завершения;Сумма, руб.;Отработано дней;Сумма за выезд;Сложная погрузка;Меж город, руб;Город, руб;Аварийный комиссар, руб;Итого комиссия, руб.\r\n");
        file_put_contents($filePath, $text, FILE_APPEND);

        foreach ($reports[$indexReport]->deals as $index => $deal)
        {
            $closedDate = date('d.m.Y', strtotime($deal->closedDate));
            $opportunity = number_format($deal->opportunity, 2, ',', ' ');
            $feeDifficultLoading = number_format($deal->feeDifficultLoading, 2, ',', ' ');
            $feeIntercity = number_format($deal->feeIntercity, 2, ',', ' ');
            $feeCity = number_format($deal->feeCity, 2, ',', ' ');
            $feeEmergencyCommissioner = number_format($deal->feeEmergencyCommissioner, 2, ',', ' ');
            $feeDailyAllowance = number_format($deal->feeDailyAllowance, 2, ',', ' ');
            $totalFee = number_format($deal->totalFee, 2, ',', ' ');

            $text = iconv('utf-8//IGNORE', 'windows-1251//IGNORE', "{$closedDate};{$opportunity};;;{$feeDifficultLoading};{$feeIntercity};{$feeCity};{$feeEmergencyCommissioner};{$totalFee}\r\n");
            file_put_contents($filePath, $text, FILE_APPEND);
        }

        $countDeal = count($reports[$indexReport]->deals);

        $totalSum = collect($reports[$indexReport]->deals)->sum(function ($deal){
            return $deal->opportunity;
        });
        $totalSum = number_format($totalSum, 2, ',', ' ');

        $feeDifficultLoading = collect($reports[$indexReport]->deals)->sum(function ($deal){
            return $deal->feeDifficultLoading;
        });
        $feeDifficultLoading = number_format($feeDifficultLoading, 2, ',', ' ');

        $feeIntercity = collect($reports[$indexReport]->deals)->sum(function ($deal){
            return $deal->feeIntercity;
        });
        $feeIntercity = number_format($feeIntercity, 2, ',', ' ');

        $feeCity = collect($reports[$indexReport]->deals)->sum(function ($deal){
            return $deal->feeCity;
        });
        $feeCity = number_format($feeCity, 2, ',', ' ');

        $feeEmergencyCommissioner = collect($reports[$indexReport]->deals)->sum(function ($deal){
            return $deal->feeEmergencyCommissioner;
        });
        $feeEmergencyCommissioner = number_format($feeEmergencyCommissioner, 2, ',', ' ');

        $totalSumDaily = $reports[$indexReport]->totalWorkedDays * $reports[$indexReport]->settings->feeExit;

        $totalFeeDeal = number_format($reports[$indexReport]->totalFeeDeal, 2, ',', ' ');

        $text = iconv('utf-8//IGNORE', 'windows-1251//IGNORE', "{$countDeal};{$totalSum};{$reports[$indexReport]->totalWorkedDays};{$totalSumDaily};{$feeDifficultLoading};{$feeIntercity};{$feeCity};{$feeEmergencyCommissioner};{$totalFeeDeal}\r\n");
        file_put_contents($filePath, $text, FILE_APPEND);

        $text = iconv('utf-8//IGNORE', 'windows-1251//IGNORE', ";;;;;;;Итого к выплате;{$reports[$indexReport]->salary}\r\n");
        file_put_contents($filePath, $text, FILE_APPEND);

        return $filePath;
    }
}
