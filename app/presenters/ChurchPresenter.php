<?php

namespace App\Presenters;

use Nette;
use App\Model;
use App\Model\Repository\Churches;

class ChurchPresenter extends BasePresenter
{
    /**
     * @inject
     * @var Churches
     */
    public $churches;

    public function renderDefault()
    {
        $this->redirect('Church:list');
    }

    public function renderList(){
        $this->template->churches = $this->churches->getAll();
    }

    public function renderView($church){
        $this->template->church = $church;
    }
}
