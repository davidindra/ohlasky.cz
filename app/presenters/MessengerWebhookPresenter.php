<?php

namespace App\Presenters;

use fritak\MessengerPlatform;
use fritak\MessengerPlatform\MessageReceived;
use Nette;
use Tracy\Debugger;

class MessengerWebhookPresenter extends BasePresenter
{
    /** @inject @var Nette\Http\Request */
    public $httpRequest;

    private $accessToken = 'EAAP6B4JPne8BABfkuowWxrGHCkK3tbHa25ZC2JY0nJZBibiI9YSZA6Buki8ZByIlHJ8ObZCacA1gmzNI1W7BWGYe9Vy12inseS25VcVxRWabR4nZBUsFAUAXOa4Tjbzw4NKuknPwCFKLvVxt9nkgpr4vnisPwTZAEuTdO1vVq0cGwZDZD';
    private $verifyToken = '2c7dd4efbf3ad50d6d1e2b038df0c0927810fa62';

    private function verifyProcess()
    {
        $hub_verify_token = null;

        if (isset($_REQUEST['hub_challenge'])) {
            $challenge = $_REQUEST['hub_challenge'];
            $hub_verify_token = $_REQUEST['hub_verify_token'];
        }
        if ($hub_verify_token === $this->verifyToken) {
            $this->template->response = $challenge;
        }
    }

    public function renderDefault()
    {
        //Debugger::log($this->httpRequest->getRawBody());
        $request = json_decode($this->httpRequest->getRawBody(), true);

        $bot = new MessengerPlatform(
            [
                'accessToken' => $this->accessToken,
                'webhookToken' => $this->verifyToken,
                'facebookApiUrl' => 'https://graph.facebook.com/v2.6/me/' //2.6 is minimum
            ],
            $request);

        if($bot->checkSubscribe())
        {
            print $bot->request->getChallenge();
            exit;
        }
        //$bot->subscribe();

        /** @var MessageReceived[] $messages */
        $messages = $bot->getMessagesReceived();
        foreach($messages ? $messages : [] as $message){
            $bot->sendMessage($message->messaging['sender']['id'], $message->messaging['message']['text']);
        }
    }
}

/*{
    "object":"page",
    "entry": [
        {
            "id":"560319450832679",
            "time":1479428716398,
            "messaging": [
                {
                    "sender": {
                        "id":"1105714516202492"
                    },
                    "recipient": {
                        "id":"560319450832679"
                    },
                    "timestamp":1479428716373,
                    "message": {
                        "mid":"mid.1479428716373:a9fbdb1d34",
                        "seq":12,
                        "text":"aha"}}]}]}
*/