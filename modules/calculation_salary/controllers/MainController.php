<?php

namespace app\modules\calculation_salary\controllers;

use app\modules\calculation_salary\models\FilterModel;
use app\modules\calculation_salary\models\Settings;
use yii\web\Controller;

/**
 * Default controller for the `calculation-salary` module
 */
class MainController extends Controller
{
    public $layout  = 'main';

    public function actionIndex()
    {
        $model = new FilterModel();

        if(\Yii::$app->request->isPost && $model->load(\Yii::$app->request->post()) && $model->validate())
        {
            return $this->render('index', ['model' =>  $model, 'report' => $model->filter()]);
        }

        return $this->render('index', ['model' =>  $model]);
    }

    public function actionSettings()
    {
        $model = Settings::instance();

        return $this->render('settings', ['model' => $model]);
    }
}
