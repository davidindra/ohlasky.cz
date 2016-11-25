<?php

namespace App\Model;

use GuzzleHttp\Client;
use Nette;
use Tracy\Debugger;

class MessengerBot
{
    use Nette\SmartObject;

    /**
     * GuzzleHTTP Client instance
     * @var Client
     */
    private $guzzle;

    /**
     * Wit model instance
     * @var Wit
     */
    private $wit;

    private $apiUrl = 'https://graph.facebook.com/v2.6/me/messages';

    private $accessToken = 'EAAP6B4JPne8BABfkuowWxrGHCkK3tbHa25ZC2JY0nJZBibiI9YSZA6Buki8ZByIlHJ8ObZCacA1gmzNI1W7BWGYe9Vy12inseS25VcVxRWabR4nZBUsFAUAXOa4Tjbzw4NKuknPwCFKLvVxt9nkgpr4vnisPwTZAEuTdO1vVq0cGwZDZD';
    private $verifyToken = '2c7dd4efbf3ad50d6d1e2b038df0c0927810fa62';

    private $pageFib = 560319450832679;

    public function __construct(Client $client, Wit $wit)
    {
        $this->guzzle = $client;
        $this->wit = $wit;

        $this->apiUrl = $this->apiUrl . '?access_token=' . $this->accessToken;

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

    public function parse($raw)
    {
        Debugger::log('DBGR: ' . $raw);

        $json = json_decode($raw);

        if (@$json->object != 'page') {
            exit;
        }

        foreach (@$json->entry as $entry) {
            foreach (@$entry->messaging as $message) {
                $sender = $message->sender->id;
                $recipient = $message->recipient->id;
                $timestamp = $message->timestamp;
                $seq = $message->message->seq;
                $text = trim($message->message->text);

                if ($recipient == $this->pageFib) { // it's for our page, not from us manually to the client
                    $this->requestApi([
                        'recipient' => ['id' => $sender],
                        'sender_action' => 'mark_seen'
                    ]);

                    $this->receivedMessage($sender, $text);
                }
            }
        }

        return '';
    }

    private function requestApi($data)
    {
        Debugger::log('DBGS: ' . json_encode($data));

        $response = $this->guzzle->post($this->apiUrl, ['json' => $data]);

        if ($response->getStatusCode() != 200) {
            throw new MessengerBotException('Messenger SendAPI request failed.');
        }

        return $response->getBody()->getContents();
    }

    private function receivedMessage($sender, $text)
    {
        Debugger::log('RECV: ' . $sender . ': ' . $text);

        $this->requestApi([
            'recipient' => ['id' => $sender],
            'sender_action' => 'typing_on'
        ]);

        if ($text != '') { // attachments not supported
            $context = null;
            $continue = true;
            while($continue) {
                $wit = $this->wit->converse($sender, ($context ? null : $text), $context);
                Debugger::log('witlog: ' . $wit);
                switch ($wit->type) {
                    case 'msg':
                        $this->sendMessage($sender, $wit->msg);
                        break;
                    case 'merge':
                        $this->sendMessage($sender, 'Máme mergovat, nastavuji dummy context.');
                        $context = json_encode(['dummy' => 'context']);
                        break;
                    case 'action':
                        $this->sendMessage($sender, 'Máme provést action ' . $wit->action . '.');
                        break;
                    case 'stop':
                        $continue = false;
                        break;
                    default:
                        $this->sendMessage($sender, Debugger::dump($wit, true));

                        //throw new MessengerBotException('Inappropiate Wit response type!');
                        $continue = false;
                }
            }
        }else{
            $this->sendMessage($sender, 'Omlouvám se, zatím tvojí zprávě nerozumím.');
        }

        $this->requestApi([
            'recipient' => ['id' => $sender],
            'sender_action' => 'typing_off'
        ]);
    }

    private function sendMessage($recipient, $text)
    {
        if (!is_numeric($recipient) || trim($text) == '') {
            throw new MessengerBotException('Invalid recipient ID or missing text.');
        }

        $this->requestApi([
            'recipient' => ['id' => $recipient],
            'message' => ['text' => $text]
        ]);

        Debugger::log('SENT: ' . $recipient . ': ' . $text);
    }
}

class MessengerBotException extends \Exception
{
}
