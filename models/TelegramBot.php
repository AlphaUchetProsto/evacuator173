<?php

namespace app\models;

use GuzzleHttp\Client;
use Tightenco\Collect\Support\Collection;

class TelegramBot
{
    private static $base_uri = "https://api.telegram.org/bot";
    public static Client $client;

    public function __construct(string $token)
    {
        static::$client = new Client([
            "base_uri" => static::$base_uri . $token . "/"
        ]);
    }

    public function sendMessage($chatID = null, $text, $isHTML = true)
    {
        if(is_null($chatID))
        {
            return false;
        }

        $params = ["chat_id" => $chatID, "text" => $text, 'disable_web_page_preview' => true];

        if($isHTML) $params["parse_mode"] = "HTML";

        $response = static::$client->request("POST", "sendMessage", [
            "form_params" => $params,
        ]);

        return collect(json_decode($response->getBody()->getContents(), true));
    }

    public function sendDocument($chatID, $fileUrl, $caption, $isHTML = true)
    {
        $params = ["chat_id" => $chatID, "document" => $fileUrl, "caption" => $caption,];

        if($isHTML) $params["parse_mode"] = "HTML";

        $response = static::$client->request("POST", "sendDocument", [
            "form_params" => $params,
        ]);

        return collect(json_decode($response->getBody()->getContents(), true));
    }

    public function sendDocumentFromPath($chatID, $filePath, $caption, $isHTML = true)
    {
        $response = static::$client->request("POST", "sendDocument", [
            "multipart" => [
                [
                    'name' => 'chat_id',
                    'contents' => $chatID,
                ],
                [
                    'name' => 'document',
                    'contents' => fopen($filePath, 'r'),
                ],
                [
                    'name' => 'caption',
                    'contents' => $caption,
                ],
                [
                    'name' => 'parse_mode',
                    'contents' => 'HTML',
                ],
            ],
        ]);

        return collect(json_decode($response->getBody()->getContents(), true));
    }

    public function getChat($chatID)
    {
        $response = static::$client->request("POST", "getChat", [
            "form_params" => ["chat_id" => $chatID],
        ]);

        return collect(json_decode($response->getBody()->getContents(), true));
    }

    public function getWebhook()
    {
        $response = static::$client->request("POST", "getWebhookInfo");

        return collect(json_decode($response->getBody()->getContents(), true));
    }

    public function deleteWebhook($drop_pending_updates = true)
    {
        $response = static::$client->request("POST", "deleteWebhook", ["form_params" => ["drop_pending_updates" => true]]);

        return collect(json_decode($response->getBody()->getContents(), true));
    }

    public function setWebhook($url, $events = [])
    {
        $response = static::$client->request("POST", "setWebhook", [
            "form_params" => [
                "url" => $url,
                "allowed_updates" => collect($events)->toJson(),
            ],
        ]);

        return collect(json_decode($response->getBody()->getContents(), true));
    }

    public function getChatMember($chatID, $user_id)
    {
        try
        {
            $response = static::$client->request("POST", "getChatMember", [
                "form_params" => ["chat_id" => $chatID, "user_id" => $user_id],
            ]);
        }
        catch (\Exception $exception){
            return false;
        }

        return collect(json_decode($response->getBody()->getContents(), true));
    }
}