<?php

namespace App\Presenters;

use Nette;
use Tracy\Debugger;

class MessengerWebhookPresenter extends BasePresenter
{
    private $accessToken = 'EAAP6B4JPne8BABfkuowWxrGHCkK3tbHa25ZC2JY0nJZBibiI9YSZA6Buki8ZByIlHJ8ObZCacA1gmzNI1W7BWGYe9Vy12inseS25VcVxRWabR4nZBUsFAUAXOa4Tjbzw4NKuknPwCFKLvVxt9nkgpr4vnisPwTZAEuTdO1vVq0cGwZDZD';
    private $verifyToken = '2c7dd4efbf3ad50d6d1e2b038df0c0927810fa62';

    private function verifyProcess(){
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
        Debugger::log(var_export($this->request->getPost(), true));
    }
}
