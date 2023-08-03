<?php

namespace app\modules\calculation_salary\models;

use app\models\bitrix\app\Client;

class App extends Client
{
    protected static function getConfigPath()
    {
        return \Yii::getAlias('@modules') . '/calculation_salary/config/config.php';
    }
}