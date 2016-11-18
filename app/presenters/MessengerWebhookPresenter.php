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
            $this->httpRequest->getRawBody());

        if ($bot->checkSubscribe()) {
            print $bot->request->getChallenge();
            exit;
        }
        //$bot->subscribe();

        /** @var MessageReceived[] $messages */
        $messages = $bot->getMessagesReceived();
        foreach ($messages ? $messages : [] as $message) {
            Debugger::log(json_encode($message->messaging));
            //$bot->sendMessage($message->messaging->sender->id, 'text');
        }

        /*$n = array(
            0 => fritak\MessengerPlatform\MessageReceived::__set_state(array('id' => '560319450832679', 'time' => 1479430459825, 'messaging' => array(0 => fritak\MessengerPlatform\Messaging::__set_state(array('recipient' => fritak\MessengerPlatform\Recipient::__set_state(array('data' => array('id' => '560319450832679',),)), 'sender' => fritak\MessengerPlatform\Sender::__set_state(array('data' => array('id' => '1105714516202492',),)), 'timestamp' => 1479430034937, 'message' => fritak\MessengerPlatform\Message::__set_state(array('mid' => 23, 'seq' => NULL, 'text' => 'ahoj', 'attachments' => NULL, 'quick_reply' => NULL,)), 'delivery' => NULL, 'optin' => NULL,)),),)),);*/
    }

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