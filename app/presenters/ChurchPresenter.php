<?php

namespace App\Presenters;

use Nette;
use App\Model;
use App\Model\Repository\Churches;
<<<<<<< HEAD
use Tracy\Debugger;
=======
>>>>>>> 84d61976f38c8034110fd9060e2f448e1762bb34

class ChurchPresenter extends BasePresenter
{
    /**
     * @inject
     * @var Churches
     */
    public $churches;
<<<<<<< HEAD

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
=======
>>>>>>> 84d61976f38c8034110fd9060e2f448e1762bb34

    public function renderDefault()
    {
        $this->redirect('Church:list');
    }

    public function renderList(){
        $this->template->churches = $this->churches->getAll();
<<<<<<< HEAD
        //$this->um->add('davidindra', 'heslo123', 'mail@davidindra.cz', 'admin', 'David Indra');
        //$this->getUser()->login('davidindra', 'heslo123');
        //$this->getUser()->logout(true);
        /*$church = new Model\Entity\Church();
        $church->linkAbbr = 'povyseni';
        $church->fullName = 'Kostel Povýšení sv. Kříže v Prostějově';
        $church->maintainer = $this->users->getByUsername('davidindra');
        $this->churches->create($church);*/
=======
>>>>>>> 84d61976f38c8034110fd9060e2f448e1762bb34
    }

    public function renderView($church){
        $this->template->church = $church;
    }
}
