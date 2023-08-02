<?php

namespace app\models\bitrix\app;

use App\HTTP\HTTP;
use Yii;
use yii\base\Model;

class Client
{
    public $access_token;
    public $refresh_token;
    public $client_endpoint;

    private $http;
    private $client_id;
    private $client_secret;

    protected $isCreator = true;

    public static function instance($params = [])
    {
        $model = new static();

        $params = collect($params)->mapWithKeys(function ($item, $key){
            return [mb_strtolower($key) => $item];
        });

        if($params->isNotEmpty() && ($params->has('auth_id') || $params->has("access_token")))
        {
            $model->isCreator = false;
            $model->access_token = $params->has('auth_id') ? $params->get('auth_id') : $params->get('access_token');
            $model->refresh_token = $params->has('refresh_id') ? $params->get('refresh_id') : $params->get('refresh_token');
        }

        $model->http = new HTTP();
        $model->http->throttle = 2;
        $model->http->useCookies = false;
        $model->client_endpoint = Yii::$app->params['bitrix']['connection']['restUrl'];
        $model->setConstant();

        return $model;
    }

    private function setConstant()
    {
        $config = static::getConfig();

        $this->client_id = $config['settings']['client_id'];
        $this->client_secret = $config['settings']['client_secret'];

        if($this->isCreator)
        {
            $this->access_token = $config['settings']['access_token'];
            $this->refresh_token = $config['settings']['refresh_token'];
        }
    }

    protected static function getConfigPath()
    {
        return Yii::getAlias('@app') . '/config/test_app_config.php';
    }

    protected static function getConfig()
    {
        return require static::getConfigPath();
    }

    public function request($method, $params = [])
    {
        $url = "{$this->client_endpoint}/{$method}.json";
        $params["auth"] = $this->access_token;

        $response = $this->http->request($url, "POST", $params);

        if(isset($response["error"]) && $response["error"] == "expired_token")
        {
            $this->refreshToken();

            $response = $this->request($method, $params);
        }

        return $response;
    }

    public function refreshToken():void
    {
        $params = [
            "grant_type" => "refresh_token",
            "client_id" => $this->client_id,
            "client_secret" => $this->client_secret,
            "refresh_token" => $this->refresh_token,
        ];

        $response = $this->http->request("https://oauth.bitrix.info/oauth/token/", "POST", $params);

        $this->access_token = $response['access_token'];
        $this->refresh_token = $response['refresh_token'];

        if($this->isCreator)
        {
            $this->updateConfig();
        }
    }

    public function updateConfig()
    {
        $appsConfig = static::getConfig();

        $appsConfig["settings"]["refresh_token"] = $this->refresh_token;
        $appsConfig["settings"]["access_token"] = $this->access_token;

        $appsConfig = var_export($appsConfig, true);

        file_put_contents(static::getConfigPath(), "<?php\n return {$appsConfig};\n");
    }

    public function buildCommand($method, $params = [])
    {
        $command = "{$method}";

        if(!empty($params))
        {
            $command .= "?" . http_build_query($params);
        }

        return $command;
    }

    public function batchRequest($commands, $halt = true)
    {
        $url = "{$this->client_endpoint}/batch";

        $response = $this->http->request($url, "POST", ["cmd" => $commands, "halt" => $halt, 'auth' => $this->access_token]);


        if(isset($response["error"]) && $response["error"] == "expired_token")
        {
            $this->refreshToken();

            $response = $this->batchRequest($commands, $halt);
        }

        return $response;
    }
}