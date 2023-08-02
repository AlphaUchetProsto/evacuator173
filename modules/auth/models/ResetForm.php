<?php

namespace app\modules\auth\models;

use app\models\bitrix\Bitrix;
use app\models\school\School;
use app\models\TelegramBot;
use Tightenco\Collect\Support\Collection;
use Yii;
use yii\base\Model;

class ResetForm extends Model
{
    public $phone;

    public function rules()
    {
        return [
            ['phone', 'required'],
            [['phone'], 'filter', 'filter' => function($item){
                return preg_replace('/[^0-9+]/', '', $item);
            }],
            ['phone', 'validationPhone'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'phone' => 'Телефон',
        ];
    }

    public function validationPhone($attribute)
    {
        if(empty(School::findDuplicateByPhone($this->$attribute)))
        {
            $this->addError($attribute, 'Пользователь с таким номером не зарегистрирован.');
        }
    }

    public function reset()
    {
        $commands = new Collection();
        $bitrix = new Bitrix;
        $password = \Yii::$app->security->generateRandomString(5);

        $uniqId = uniqid();

        $company = School::findByPhone($this->phone);
        $company->password = \Yii::$app->security->generatePasswordHash($password);
        $company->tokenReferral = md5("{$password}:{$uniqId}");
        $company->referralLinkInstallment = Yii::$app->request->hostInfo . "/forms/order/installment?token={$company->tokenReferral}";
        $company->referralLinkAcquiring = Yii::$app->request->hostInfo . "/forms/order/acquiring?token={$company->tokenReferral}";

        $commands->put('company_update', $bitrix->buildCommand('crm.company.update', ['ID' => $company->id, 'fields' => $company::getParamsField($company)]));
        $commands->put('start_bizproc', $bitrix->buildCommand('bizproc.workflow.start', [
            'TEMPLATE_ID' => 19,
            'DOCUMENT_ID' => ['crm', 'CCrmDocumentCompany', $company->id],
            'PARAMETERS' => [
                'password' => $password,
            ],
        ]));


        $message = "Пароль успешно сброшен!\n\n";
        $message .= "Логин: <code>{$this->phone}</code>\n";
        $message .= "Пароль: <code>{$password}</code>\n\n";


        $message .= "<a href='https://lk.vashotdel.ru/login'>Войти</a>";

        $tgBot = TelegramBot::vashOtdel();
        $tgBot->sendMessage($company->telegramId, $message);

        return $bitrix->batchRequest($commands->toArray());
    }
}