<?php

namespace App\Presenters;

use Nette;
use App\Model\MessengerBot;

class MessengerWebhookPresenter extends BasePresenter
{
    /** @inject @var Nette\Http\Request */
    public $httpRequest;

    /** @inject @var MessengerBot */
    public $bot;

    public function renderDefault()
    {
        $this->template->response = $this->bot->parse($this->httpRequest->getRawBody());
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