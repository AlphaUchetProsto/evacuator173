<?php

namespace app\modules\auth\controllers;

use app\models\bitrix\Bitrix;
use app\models\bitrix\crm\requisite\Requisite;
use app\models\bitrix\crm\requisite\RequisitePreset;
use app\modules\auth\models\LoginForm;
use app\modules\auth\models\ResetForm;
use app\modules\auth\models\SignUpForm;
use Yii;
use yii\base\BaseObject;
use yii\filters\AccessControl;
use yii\web\Controller;

class MainController extends Controller
{
    public $layout = 'main';

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['login', 'sign-up', 'reset'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                ],
                'denyCallback' => function($rule, $action) {
                    return $action->controller->redirect('login');
                },
            ],
        ];
    }

    public function actionLogin()
    {
        $model = new LoginForm();

        if(\Yii::$app->request->isPost && $model->load(\Yii::$app->request->post()) && $model->validate())
        {
            $model->login();

            return $this->redirect('/');
        }

        return $this->render('login', ['model'=> $model]);
    }

    public function actionSignUp()
    {
        $presets = RequisitePreset::getList();
        $model = new SignUpForm;

        if(\Yii::$app->request->isPost && $model->load(\Yii::$app->request->post()) && $model->validate())
        {
            if($model->save())
            {
                return $this->redirect('login');
            }
            else
            {
                Yii::$app->session->setFlash('unsuccessful', 'Неправильно указан телеграм ID, или вы не присоединились к нашему чат-боту');
            }
        }

        return $this->render('sign-up', ['model' => $model, 'presets' => $presets]);
    }

    public function actionReset()
    {
        $model = new ResetForm;

        if(\Yii::$app->request->isPost && $model->load(\Yii::$app->request->post()) && $model->validate())
        {
            $model->reset();

            return $this->redirect('login');
        }

        return $this->render('reset', ['model' => $model]);
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->redirect('login');
    }
}
