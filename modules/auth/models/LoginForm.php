<?php

namespace app\modules\auth\models;

use app\models\User;
use Yii;
use yii\base\Model;

class LoginForm extends Model
{
    public $phone;
    public $password;
    public $rememberMe = true;

    private $_user = false;

    public function rules()
    {
        return [
            [['phone', 'password'], 'required'],
            ['phone', 'filter', 'filter' => function($item){
                return preg_replace('/[^0-9+]/', '', $item);
            }],
            ['rememberMe', 'boolean'],
            ['password', 'validatePassword'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'phone' => 'Телефон',
            'password' => 'Пароль',
            'rememberMe' => 'Запомнить меня',
        ];
    }

    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();

            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, 'Логин или пароль неправильный!');
            }
        }
    }

    public function login()
    {
        if ($this->validate())
        {
            return Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600*24*30 : 0);
        }

        return false;
    }

    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = User::findByPhone($this->phone);
        }

        return $this->_user;
    }
}
