<?php

namespace App\Model;

use Nette;
use Tracy\Debugger;

class MessengerBot
{
    use Nette\SmartObject;

    private $accessToken = 'EAAP6B4JPne8BABfkuowWxrGHCkK3tbHa25ZC2JY0nJZBibiI9YSZA6Buki8ZByIlHJ8ObZCacA1gmzNI1W7BWGYe9Vy12inseS25VcVxRWabR4nZBUsFAUAXOa4Tjbzw4NKuknPwCFKLvVxt9nkgpr4vnisPwTZAEuTdO1vVq0cGwZDZD';
    private $verifyToken = '2c7dd4efbf3ad50d6d1e2b038df0c0927810fa62';

    public function __construct()
    {
        $this->verifyProcess();
    }

    private function verifyProcess()
    {
        if (isset($_REQUEST['hub_challenge'])) {
            $challenge = $_REQUEST['hub_challenge'];
            $hub_verify_token = $_REQUEST['hub_verify_token'];

            if ($hub_verify_token === $this->verifyToken) {
                print $challenge;
                exit;
            }
        }
    }

    public function parse($raw){
        $json = json_decode($raw);

        if(@$json->object != 'page'){
            exit;
        }

        foreach(@$json->entry as $entry){
            foreach(@$entry->messaging as $message){
                $sender = $message->sender->id;
                $recipient = $message->recipient->id;
                $timestamp = $message->timestamp;
                $seq = $message->message->seq;
                $text = $message->message->text;

                $this->receivedMessage($sender, $text);
            }
        }

        return '';
    }

    private function receivedMessage($sender, $text){
        Debugger::log($sender . ': ' . $text);
    }
}

class MessengerBotException extends \Exception
{
}
