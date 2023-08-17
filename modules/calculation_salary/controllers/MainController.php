<?php

namespace app\modules\calculation_salary\controllers;

use app\models\bitrix\Bitrix;
use app\modules\calculation_salary\models\FilterModel;
use app\modules\calculation_salary\models\Settings;
use yii\base\BaseObject;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use app\modules\calculation_salary\models\App;
use app\modules\calculation_salary\models\SalaryReport;
use yii\web\Response;

class MainController extends Controller
{
    public $layout  = 'main';
    public $enableCsrfValidation = false;
    
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

        if(\Yii::$app->request->isPost && $model->load(\Yii::$app->request->post()) && $model->validate())
        {
            $model->save();
        }

        return $this->render('settings', ['model' => $model]);
    }

    public function actionNotificationSalary()
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;

        SalaryReport::sendReport();

        return 200;
    }

    public function actionCreateExcel()
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;

        $model = new FilterModel();

        if(\Yii::$app->request->isPost && $model->load(\Yii::$app->request->post()) && $model->validate())
        {
            return [
                'result' => $model->createExcel()
            ];
        }

        return [
            'error' => 'Не удалось создать файл',
        ];
    }

    public function actionCreateTranscript()
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;

        $model = new FilterModel();

        if(\Yii::$app->request->isPost && $model->load(\Yii::$app->request->post()) && $model->validate())
        {
            return [
                'result' => $model->createTranscriptTable(\Yii::$app->request->get('indexReport'))
            ];
        }

        return [
            'error' => 'Не удалось создать файл',
        ];
    }

    public function actionInstall()
    {
        $client = App::instance(\Yii::$app->request->post());
        $client->updateConfig();

        return $this->render('install');
    }
}
