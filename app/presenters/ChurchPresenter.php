<?php

namespace App\Presenters;

use Nette;
use App\Model;
use App\Model\Repository\Churches;
use Tracy\Debugger;

class ChurchPresenter extends BasePresenter
{
    /**
     * @inject
     * @var Churches
     */
    public $churches;

    /**
     * @inject
     * @var Model\Repository\Users
     */
    public $users;

    /**
     * @inject
     * @var Model\UserManager
     */
    public $um;

    public function renderDefault()
    {
        $this->redirect('Church:list');
    }

    public function renderList(){
        $this->template->churches = $this->churches->getAll();
        //$this->um->add('davidindra', 'heslo123', 'mail@davidindra.cz', 'admin', 'David Indra');
        //$this->getUser()->login('davidindra', 'heslo123');
        //$this->getUser()->logout(true);
        /*$church = new Model\Entity\Church();
        $church->linkAbbr = 'povyseni';
        $church->fullName = 'Kostel Povýšení sv. Kříže v Prostějově';
        $church->maintainer = $this->users->getByUsername('davidindra');
        $this->churches->create($church);*/
    }

    public function renderView($church){
        $this->template->church = $church;
    }
}
