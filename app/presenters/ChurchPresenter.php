<?php

namespace App\Presenters;

use Nette;
use App\Model;
use App\Model\LiturgyCollector;
use App\Model\Repository\Churches;
use App\Model\Repository\Masses;

class ChurchPresenter extends BasePresenter
{
    /**
     * @inject
     * @var Churches
     */
    public $churches;

    /**
     * @inject
     * @var Masses
     */
    public $masses;

    /**
     * @inject
     * @var LiturgyCollector
     */
    public $liturgy;

    public function renderDefault()
    {
        $this->redirect('Church:list');
    }

    public function renderList(){
        $this->template->churches = $this->churches->getAll();
    }

    public function renderView($church){
        $this->template->church = $this->churches->getByAbbreviation($church);
        if(!$this->template->church){
            $this->error();
        }

        $this->template->masses = $this->masses->getByChurch($this->template->church);

        $this->template->liturgy = $this->liturgy;
    }
}
