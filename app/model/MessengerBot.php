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

    private $apiUrl = 'https://graph.facebook.com/v2.6/me/messages';

    private $accessToken = 'EAAP6B4JPne8BABfkuowWxrGHCkK3tbHa25ZC2JY0nJZBibiI9YSZA6Buki8ZByIlHJ8ObZCacA1gmzNI1W7BWGYe9Vy12inseS25VcVxRWabR4nZBUsFAUAXOa4Tjbzw4NKuknPwCFKLvVxt9nkgpr4vnisPwTZAEuTdO1vVq0cGwZDZD';
    private $verifyToken = '2c7dd4efbf3ad50d6d1e2b038df0c0927810fa62';

    public function __construct(Client $client)
    {
        $this->guzzle = $client;

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
        $this->sendMessage($sender, $text);
    }

    private function sendMessage($recipient, $text){
        $data = [
            'recipient' => ['id' => $recipient],
            'message' => ['text' => $text]
        ];

        $response = $this->guzzle->post($this->apiUrl, ['form_data' => json_encode($data)]);
            //->getBody()
            //->getContents();

        if($response->getStatusCode() != 200){
            throw new MessengerBotException('Failed message sending.');
        }
    }
}

class MessengerBotException extends \Exception
{
}


/*request({
    uri: 'https://graph.facebook.com/v2.6/me/messages',
    qs: { access_token: PAGE_ACCESS_TOKEN },
    method: 'POST',
    json: messageData

  }, function (error, response, body) {
    if (!error && response.statusCode == 200) {
      var recipientId = body.recipient_id;
      var messageId = body.message_id;

      console.log("Successfully sent generic message with id %s to recipient %s",
        messageId, recipientId);
    } else {
      console.error("Unable to send message.");
      console.error(response);
      console.error(error);
    }
  });  */