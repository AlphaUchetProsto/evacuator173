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
            [['day'], 'default', 'value' => 18],
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
}
