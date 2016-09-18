<?php

namespace App\Presenters;

use Nette;
use App\Model;
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
        $this->um->add('davidindra', 'f26.QiIL', 'mail@davidindra.cz', 'admin', 'David Indra');
        //$this->um->add('davidindra', 'heslo123', 'mail@davidindra.cz', 'admin', 'David Indra');
        //$this->getUser()->login('davidindra', 'heslo123');
        //$this->getUser()->logout(true);
        /*$church = new Model\Entity\Church();
        $church->abbreviation = 'povyseni';
        $church->name = 'Kostel Povýšení sv. Kříže v Prostějově';
        $church->nameHighlighted = 'Kostel <b>Povýšení sv. Kříže</b> v Prostějově';
        $church->maintainer = $this->users->getByUsername('davidindra');
        $this->churches->create($church);*/
    }

    public function renderView($church){
        $this->template->church = $this->churches->getByAbbreviation($church);
        if(!$this->template->church){
            $this->error();
        }

        $this->template->masses = $this->masses->getByChurch($this->template->church);
    }
}
