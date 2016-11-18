<?php

namespace App\Model;

use GuzzleHttp\Client;
use Nette;

class Wit
{
    use Nette\SmartObject;

    /**
     * GuzzleHTTP Client instance
     * @var Client
     */
    private $guzzle;

    private $accessToken = 'HIYLW7CND2TDE6LYURRR7AXXLMQEGZ7W';

    public function __construct(Client $client)
    {
        $this->guzzle = $client;

        //$this->apiUrl = $this->apiUrl . '?access_token=' . $this->accessToken;
    }

    /*private function requestApi($url = 'https://api.wit.ai/message?v=20161118&q=' . 'hello', $data, $post = false){
        $response = $this->guzzle->request($post ? 'post' : 'get', $url, [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->accessToken,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ],
            'json' => $post ? $data : null
        ]);

        if ($response->getStatusCode() != 200) {
            throw new MessengerBotException('Wit API request failed.');
        }

        return $response->getBody()->getContents();
    }*/

    public function apiConverseNew($sender, $text){
        $response = $this->guzzle->request('get', 'https://api.wit.ai/converse?v=20161118&session_id=' . $sender . '&q=' . urlencode($text), [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->accessToken,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ]
        ]);

        if ($response->getStatusCode() != 200) {
            throw new MessengerBotException('Wit API request failed.');
        }

        return json_decode($response->getBody()->getContents());
    }
}

class WitException extends \Exception
{
}
