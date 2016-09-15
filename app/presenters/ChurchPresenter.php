<?php

namespace App\Presenters;

use Nette;
use App\Model;


class ChurchPresenter extends BasePresenter
{
    public function renderDefault()
    {
        $this->redirect('Church:list');
    }

    public function renderList(){

    }

    public function renderView($church){
        $this->template->church = $church;
    }
}
