<?php

namespace App\Presenters;

use Nette;
use App\Model;

class SecuredPresenter extends BasePresenter
{
    public function startup()
    {
        if(!$this->user->isLoggedIn()){
            $this->flashMessage('Pro přístup k této stránce se musíte přihlásit.');
            $this->redirect('Homepage:', ['backlink' => $this->storeRequest()]);
        }

        parent::startup();
    }
}