<?php

namespace app\modules\calculation_salary\models;

use Tightenco\Collect\Support\Collection;
use yii\base\Model;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

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
        $filePath = 'src/calculation_salary/transcript.csv';

        $reports = SalaryReport::create($this->getFilterDay());

        $feePrepaidExpense = number_format($reports[$indexReport]->settings->feePrepaidExpense, 2, ',', ' ');
        $totalSalary = number_format($reports[$indexReport]->totalSalaryWithoutFine, 2, ',', ' ');

        $spreadsheet = new Spreadsheet();
        $activeWorksheet = $spreadsheet->getActiveSheet();
        $activeWorksheet->setCellValue('A1', 'ФИО водителя');
        $activeWorksheet->setCellValue('B1', "{$reports[$indexReport]->driver->lastName} {$reports[$indexReport]->driver->name} {$reports[$indexReport]->driver->secondName}");
        $activeWorksheet->setCellValue('C1', "Зарплата Всего");
        $activeWorksheet->setCellValue('D1', $totalSalary);
        $activeWorksheet->setCellValue('E1', 'Аванс');
        $activeWorksheet->setCellValue('F1', $feePrepaidExpense);

        $tableHeader = [
            ['Дата завершения','Сумма, руб.', 'Отработано дней', 'Дней в командировке', 'Сумма за выход', 'Сложная погрузка', 'Меж город, руб', 'Город, руб', 'Аварийный комиссар, руб', 'Удержания, руб', 'Итого сумма, руб.']
        ];

        $spreadsheet->getActiveSheet()->fromArray($tableHeader, NULL, 'A3');

        $currentRow = 4;

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

            $body = [[$closedDate, $opportunity, '', '', '', $feeDifficultLoading, $feeIntercity,$feeCity, $feeEmergencyCommissioner, '', $totalFee]];
            $spreadsheet->getActiveSheet()->fromArray($body, NULL, "A{$currentRow}");

            $currentRow++;
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

        $totalSumDaily = ($reports[$indexReport]->totalWorkedDays + $reports[$indexReport]->driver->businessDays) * $reports[$indexReport]->settings->feeExit;

        $totalFeeDeal = number_format($reports[$indexReport]->totalFeeDeal, 2, ',', ' ');

        $footer = [
            [$countDeal, $totalSum, $reports[$indexReport]->totalWorkedDays, $reports[$indexReport]->driver->businessDays, $totalSumDaily, $feeDifficultLoading, $feeIntercity, $feeCity, $feeEmergencyCommissioner, $reports[$indexReport]->driver->sumFine, $totalFeeDeal],
            ['']
        ];

        $spreadsheet->getActiveSheet()->fromArray($footer, NULL, "A{$currentRow}");

        $currentRow += 2;

        $activeWorksheet->setCellValue("J{$currentRow}", 'Итого к выплате');
        $activeWorksheet->setCellValue("K{$currentRow}", $reports[$indexReport]->salary);

        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ];

        $spreadsheet->getActiveSheet()->getStyle("A1:K{$currentRow}")->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);

        $writer = new Xlsx($spreadsheet);
        $writer->save($filePath);

        return $filePath;
    }
}
