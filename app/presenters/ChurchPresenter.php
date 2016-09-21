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

    public function renderView($church, $edit = null){
        $this->template->edit = $edit;

        $this->template->church = $this->churches->getByAbbreviation($church);
        if(!$this->template->church){
            $this->error();
        }

        $future = $this->user->isLoggedIn();

        $this->template->masses = array();
        foreach($this->masses->getByChurch($this->template->church) as $mass){
            if(DateTime::from($mass->datetime) > DateTime::from(time())
                && (DateTime::from($mass->datetime) <= DateTime::from(time() + 7*24*60*60) || $future)) {

                $this->template->masses[] = $mass;
            }
        }

        $this->template->liturgy = $this->liturgy;
    }

    public function createComponentMassForm(){
        $mass = empty($this->getParameter('edit')) ? null : $this->masses->getById($this->getParameter('edit'));

        $form = new Form();

        $form->addHidden('churchId')
            ->setDefaultValue($this->getParameter('church'));
        $form->addHidden('massId')
            ->setDefaultValue($this->getParameter('edit'));

        $form->addText('date', 'Datum')
            ->setAttribute('placeholder', 'Datum')
            ->setAttribute('data-value', $mass ? $mass->datetime->format('Y-m-d') : DateTime::from(time()+24*60*60)->format('Y-m-d'))
            ->setRequired('Zvolte, prosím, datum mše.');

        $form->addHidden('date_submit');

        $form->addText('time', 'Čas')
            ->setAttribute('placeholder', 'Čas')
            ->setDefaultValue($mass ? $mass->datetime->format('H:i') : '09:00')
            ->setRequired('Zvolte, prosím, čas mše.');

        $form->addCheckbox('highlight', 'slavnost')
            ->setDefaultValue($mass ? $mass->highlighted : false);

        $form->addText('intention', 'Intence')
            ->setDefaultValue($mass ? $mass->intention : null)
            ->setAttribute('placeholder', 'Intence');

        $form->addSubmit('send', 'Uložit');

        $form->onSuccess[] = function (Form $form, $values) {
            $church = $this->churches->getById($values['churchId']);
            if(!$this->user->isLoggedIn() || $church->maintainer->username != $this->user->identity->username){
                $this->flashMessage('Nemáte oprávnění upravovat mše tohoto kostela.');
                $this->redirect('this');
            }

            Debugger::barDump($values);
            if(empty($values['massId'])){
                $mass = new Mass();
                $mass->church = $church;
                $mass->officiant = $church->maintainer;

                $mass->datetime = DateTime::from($values['date'] . ' ' . $values['time']);
                $mass->highlighted = $values['highlight'];
                $mass->intention = $values['intention'];
                $this->masses->create($mass);
                $this->flashMessage('Mše byla vytvořena.');
                $this->redirect('this');
            }else{
                /** @var Mass $mass */
                $mass = $this->masses->getById($values['massId']);
                $mass->datetime = DateTime::from($values['date'] . ' ' . $values['time']);
                $mass->highlighted = $values['highlight'];
                $mass->intention = $values['intention'];
                $this->flashMessage('Mše byla upravena.');
                $this->redirect('Church:view', [$this->getParameter('church')]);
            }
        };

        return $form;
    }

    public function handleDeleteMass($massId){
        $this->masses->deleteById($massId);
        $this->flashMessage('Mše byla odstraněna.');
        $this->redirect('Church:view', [$this->getParameter('church')]);
    }
}
