<?php

namespace app\controllers;

use app\models\order\OrderForm;
use app\models\student\StudentForm;
use yii\base\BaseObject;
use yii\data\Pagination;
use yii\filters\AccessControl;
use yii\web\Controller;
use app\models\bitrix\Bitrix;
use app\models\bitrix\crm\Contact;

class MainController extends Controller
{
    /*public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['index', 'students', 'orders'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
                'denyCallback' => function($rule, $action) {
                    return $action->controller->redirect('/login');
                },
            ],
        ];
    }*/

    public function actionIndex()
    {
        $bitrix = new Bitrix();
        $response = $bitrix->request('crm.deal.list', ['start' => '-1']);
        dd($response);

        return $this->render('index');
    }
}
