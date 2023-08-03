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

        $text = iconv('utf-8//IGNORE', 'windows-1251//IGNORE', "ФИО водителя;Отработано дней;Аванс, руб.;ЗП с авансом, руб.;К выплате, руб.;\r\n");
        file_put_contents($filePath, $text);

        foreach ($reports as $index => $item)
        {
            $feePrepaidExpense = number_format($item->settings->feePrepaidExpense, 2, ',', ' ');
            $totalSalary = number_format($item->totalSalary, 2, ',', ' ');
            $salary = number_format($item->salary, 2, ',', ' ');

            $text = iconv('utf-8//IGNORE', 'windows-1251//IGNORE', "{$item->driver->lastName} {$item->driver->name} {$item->driver->secondName};{$item->totalWorkedDays};{$feePrepaidExpense};{$totalSalary};{$salary};\r\n");
            file_put_contents($filePath, $text, FILE_APPEND);
        }

        return $filePath;
    }
}
