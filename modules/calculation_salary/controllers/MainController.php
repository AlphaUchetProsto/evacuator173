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

    public function actionUpdateFine()
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;

        $postData = \Yii::$app->request->post();

        $client = App::instance();

        ['result' => $items] = $client->request('entity.item.get', [
            'ENTITY' => 'fine',
            'FILTER' => [
                'NAME' => $postData['date'],
                'PROPERTY_contactId' => $postData['contactId'],
            ],
        ]);

        if(empty($items))
        {
            $response = $client->request('entity.item.add', [
                'ENTITY' => 'fine',
                'NAME' => $postData['date'],
                'PROPERTY_VALUES' => $postData,
            ]);
        }
        else
        {
            $response = $client->request('entity.item.update', [
                'ENTITY' => 'fine',
                'ID' => $items[0]['ID'],
                'PROPERTY_VALUES' => $postData,
            ]);
        }

        return [
            'result' => 'Данные успешно обновлены!',
        ];
    }

    public function actionUpdateBussinesDay()
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;

        $postData = \Yii::$app->request->post();

        $client = App::instance();

        ['result' => $items] = $client->request('entity.item.get', [
            'ENTITY' => 'business_days',
            'FILTER' => [
                'NAME' => $postData['date'],
                'PROPERTY_contactId' => $postData['contactId'],
            ],
        ]);

        if(empty($items))
        {
            $response = $client->request('entity.item.add', [
                'ENTITY' => 'business_days',
                'NAME' => $postData['date'],
                'PROPERTY_VALUES' => $postData,
            ]);
        }
        else
        {
            $response = $client->request('entity.item.update', [
                'ENTITY' => 'business_days',
                'ID' => $items[0]['ID'],
                'PROPERTY_VALUES' => $postData,
            ]);
        }

        return [
            'result' => 'Данные успешно обновлены!',
        ];
    }

    public function actionTest()
    {
        $model = new FilterModel();
        $model->month = 12;
        $model->year = 2;
        $model->validate();
        
        $result = $model->filter();

        dd($result);
    }

//    public function actionGetFine()
//    {
//        \Yii::$app->response->format = Response::FORMAT_JSON;
//
//        $postData = ['contactId' => 5, 'date' => '082023'];
//
//        $client = App::instance();
//
//        ['result' => $items] = $client->request('entity.item.get', [
//            'ENTITY' => 'fine',
//            'FILTER' => [
//                'NAME' => $postData['date'],
//                'PROPERTY_contactId' => $postData['contactId'],
//            ],
//        ]);
//
//        return [
//            'result' => !empty($items) ? $items[0]['PROPERTY_VALUES']['value'] : 0,
//        ];
//    }
}
