<?php

namespace app\models;

use app\models\school\School;
use Yii;
use yii\base\BaseObject;

class User extends BaseObject implements \yii\web\IdentityInterface
{
    public $id;
    public $title;
    public $password;
    public $authKey;
    public $accessToken;

    public static function findIdentity($id)
    {
        return null;
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return null;
    }

    public static function findByPhone($phone)
    {
        return null;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getAuthKey()
    {
        return $this->authKey;
    }

    public function validateAuthKey($authKey)
    {
        return $this->authKey === $authKey;
    }

    public function validatePassword($password)
    {
        return Yii::$app->getSecurity()->validatePassword($password, $this->password);
    }
}
