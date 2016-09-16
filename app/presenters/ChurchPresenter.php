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
        $dao = $this->em->getRepository(Church::getClassName());
        $this->template->churches = Debugger::dump($dao->findAll(), true);
    }

    public function renderView($church){
        $this->template->church = $church;
    }
}
