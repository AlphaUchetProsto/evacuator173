<?php

namespace app\models\bitrix;

use App\HTTP\HTTP;
use Yii;

class Bitrix
{
    protected $client;
    protected $rest_url;

    private $last_response;

    public function __construct($config = [])
    {
        $this->rest_url = Yii::$app->params['bitrix']['webhook_url'];

        $client = new HTTP();
        $client->throttle = 2;

        $this->client = $client;
    }

    public function request($method, $params = [])
    {
        $url = "{$this->rest_url}{$method}";

        $this->last_response = $this->client->request($url, "POST", $params);

        return $this->last_response;
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
        $url = "{$this->rest_url}batch";

        $this->last_response = $this->client->request($url, "POST", ["cmd" => $commands, "halt" => $halt]);

        return $this->last_response;
    }

    public function getLastResponse()
    {
        return $this->last_response;
    }
}
