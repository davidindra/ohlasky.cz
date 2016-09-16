<?php

namespace App\Presenters;

use App\Church;
use Nette;
use App\Model;
use App\Article;
use Tracy\Debugger;

class ChurchPresenter extends BasePresenter
{
    /**
     * @inject
     * @var \Kdyby\Doctrine\EntityManager
     */
    public $em;

    public function renderDefault()
    {
        $this->redirect('Church:list');
    }

    public function renderList(){
        $dao = $this->em->getRepository(Church::class);
        $this->template->churches = $dao->findAll();
    }

    public function renderView($church){
        $this->template->church = $church;
    }
}
