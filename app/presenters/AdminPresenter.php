<?php

namespace App\Presenters;

use Nette;
use App\Model;
use App\Model\Repository\Churches;
use App\Model\Repository\Masses;

class AdminPresenter extends BasePresenter
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

    public function startup()
    {
        if(!$this->user->isInRole('admin')){
            $this->flashMessage('Nemáte oprávnění pro přístup k této stránce.');
            $this->redirect('Homepage:', ['backlink' => $this->storeRequest()]);
        }

        parent::startup();
    }

    public function renderDefault()
    {
        //$this->template->churches = $this->churches->getAll();
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

    public function handleCleanCache(){
        $path = __DIR__ . '/../../temp/cache';
        $this->deleteDirectory($path);
        mkdir($path);

        $this->flashMessage('Cache úspěšně vyprázdněna.');
        $this->redirect('this');
    }

    public function handleAddUser($username = 'cap', $password = 'capcap', $role = 'maintainer', $name = 'P. Pavel Čáp'){
        $this->um->add($username, $password, 'dummy@mail.cz', $role, $name);
        $this->flashMessage('Uživatel ' . $username . '/' . $password . ' přidán.');
        $this->redirect('this');
    }
}
