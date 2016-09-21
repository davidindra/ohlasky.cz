<?php

namespace App\Presenters;

use App\Model\Entity\Church;
use App\Model\Entity\Mass;
use Nette;
use App\Model;
use App\Model\LiturgyCollector;
use App\Model\Repository\Churches;
use App\Model\Repository\Masses;
use Nette\Application\UI\Form;
use Nette\Utils\DateTime;
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

        $this->template->masses = array();
        foreach($this->masses->getByChurch($this->template->church) as $mass){
            if(DateTime::from($mass->datetime) > DateTime::from(time())
                && DateTime::from($mass->datetime) <= DateTime::from(time() + 7*24*60*60)) {

                $this->template->masses[] = $mass;
            }
        }

        $this->template->liturgy = $this->liturgy;
    }

    public function createComponentMassForm(){
        $form = new Form();

        $form->addHidden('churchId');
        $form->addHidden('massId');

        $form->addText('date', 'Datum')
            ->setRequired('Zvolte, prosím, datum mše.');

        $form->addText('time', 'Čas')
            ->setRequired('Zvolte, prosím, čas mše.');

        $form->addCheckbox('highlight', 'slavnost');

        $form->addText('intention', 'Intence');

        $form->addSubmit('send', 'Uložit');

        $form->onSuccess[] = function (Form $form, $values) {
            $church = $this->churches->getById($values['churchId']);
            if(!$this->user->isLoggedIn() || $church->maintainer->username != $this->user->identity->username){
                $this->flashMessage('Nemáte oprávnění upravovat mše tohoto kostela.');
                $this->redirect('this');
            }

            Debugger::barDump($values);
            if(empty($values['massId'])){
                // TODO (handle, adderrors?)
                /*$mass = new Mass();
                $mass->
                $this->masses->create($mass);*/
                $this->flashMessage('Mše byla vytvořena.');
            }else{
                // TODO
                $this->flashMessage('Mše byla upravena.');
            }
        };

        return $form;
    }
}
