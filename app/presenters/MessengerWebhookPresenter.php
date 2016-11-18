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
        $this->verifyProcess();

        //Debugger::log($this->httpRequest->getRawBody());
        $request = json_decode($this->httpRequest->getRawBody());

        $this->parse($request);
    }

    private function verifyProcess()
    {
        if (isset($_REQUEST['hub_challenge'])) {
            $challenge = $_REQUEST['hub_challenge'];
            $hub_verify_token = $_REQUEST['hub_verify_token'];

            if ($hub_verify_token === $this->verifyToken) {
                $this->template->response = $challenge;
                exit;
            }
        }
    }

    private function parse($json){
        if(@$json->object != 'page'){
            $this->terminate();
        }

        foreach(@$json->entry as $entry){
            foreach(@$entry->messaging as $message){
                $sender = $message->sender->id;
                $recipient = $message->recipient->id;
                $timestamp = $message->timestamp;
                $seq = $message->message->seq;
                $text = $message->message->text;

                Debugger::log($sender . ': ' . $text);
            }
        }
    }
}

/*
{
    "object":"page",
    "entry":
    [
        {
            "id":"560319450832679",
            "time":1479428716398,
            "messaging":
            [
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
                        "text":"aha"
                    }
                }
            ]
        }
    ]
}
*/