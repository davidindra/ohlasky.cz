<?php

namespace App\Presenters;

use Nette;
use App\Model;
use App\Model\Repository\Churches;
use App\Model\Repository\Masses;
use App\Model\Entity\Church;
use App\Model\Entity\Mass;
use App\Model\LiturgyCollector;
use Nette\Utils\DateTime;
use Tracy\Debugger;

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

    /**
     * @inject
     * @var LiturgyCollector
     */
    public $liturgyCollector;

    public function startup()
    {
        if(!$this->user->isInRole('admin')){
            $this->flashMessage('Nemáte oprávnění pro přístup k této stránce.');
            $this->redirect('Homepage:', ['backlink' => $this->storeRequest()]);
        }

        parent::startup();
    }

    public function renderDefault(){
        Debugger::timer();
        $this->template->liturgyToday = $this->liturgyCollector->getDayInfo(DateTime::from(time()));
        Debugger::barDump(Debugger::timer());
    }

    public function handleCleanCache(){
        $path = __DIR__ . '/../../temp/cache';
        $this->deleteDirectory($path);
        mkdir($path);

        $this->flashMessage('Cache úspěšně vyprázdněna.');
        $this->redirect('this');
    }

    public function handleAddUser($username = 'knez', $password = 'knez', $role = 'maintainer', $name = 'P. Pokusný Kněz'){
        $this->um->add($username, $password, 'pokusny@knez.cz', $role, $name);
        $this->flashMessage('Uživatel ' . $username . '/' . $password . ' přidán.');
        $this->redirect('this');
    }

    public function handleAddDummyData(){
        $user = @array_pop($this->users->getAll());
        if(!$user){
            $this->flashMessage('Nemáme uživatele, kterému by mohla být data přiřazena.');
            $this->redirect('this');
        }

        $church = new Church();
        $church->abbreviation = 'pokusnykostel';
        $church->name = 'Kostel sv. Pokusu v Prostějově';
        $church->nameHighlighted = 'Kostel <b>sv. Pokusu</b> v Prostějově';
        $church->maintainer = $user;
        $this->churches->create($church);
        $this->flashMessage('Testovací kostel vytvořen.');

        $church = @array_pop($this->churches->getAll());
        for($i = 0; $i <= 14; $i++) {
            $mass = new Mass();
            $mass->datetime = DateTime::from(time())->add(\DateInterval::createFromDateString($i . 'day'));
            $mass->celebration = $i % 7 == 6 ? 'Testovací slavnost, významná' : null;
            $mass->highlighted = $i % 7 == 6 ? true : false;
            $mass->intention = 'za rodinu Pokusných a Testovacích' . $i . ' a za duše v očistci';
            $mass->church = $church;
            $mass->officiant = $user;

            $this->masses->create($mass);
        }
        $this->flashMessage('Testovací mše vytvořeny.');

        $this->redirect('this');
    }
}
