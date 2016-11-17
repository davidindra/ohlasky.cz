<?php

namespace App\Presenters;

use App\Model\Entity\Announcement;
use App\Model\Entity\Church;
use App\Model\Entity\Mass;
use App\Model\Repository\Announcements;
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
     * @var Announcements
     */
    public $announcements;

    /**
     * @inject
     * @var LiturgyCollector
     */
    public $liturgy;

    /** @var  Model\Repository\LiturgyDays @inject */
    public $liturgyDays;

    public function renderDefault($church, $edit = null, $editAnnouncement = null, $vice = false){
        $this->template->edit = $edit;
        $this->template->editAnnouncement = $editAnnouncement;
        $this->template->vice = $vice;

        $this->template->church = $this->churches->getByAbbreviation($church);
        if(!$this->template->church){
            $this->error();
        }

        $future = $this->user->isLoggedIn() || $vice;

        $this->template->masses = array();
        foreach($this->masses->getByChurch($this->template->church) as $mass){
            if(DateTime::from($mass->datetime) > DateTime::from(strtotime(date('o-\\WW')))
                //&& (DateTime::from($mass->datetime) <= DateTime::from(time() + 7*24*60*60) || $future)) {
                && (DateTime::from($mass->datetime) <= DateTime::from(strtotime(date('o-\\WW', time() + 7*24*60*60))) || $future)) {

                $this->template->masses[] = $mass;
            }
        }

        $this->template->announcements = $this->announcements->getByChurch($this->template->church);

        $this->template->liturgyDays = $this->liturgyDays;
    }

    public function createComponentMassForm(){
        $mass = empty($this->getParameter('edit')) ? null : $this->masses->getById($this->getParameter('edit'));

        $form = new Form();
        $form->elementPrototype->setAttribute('class', 'ajax');

        $form->addHidden('churchId');
        $form->addHidden('massId')
            ->setDefaultValue($this->getParameter('edit'));

        $form->addText('date', 'Datum')
            ->setAttribute('placeholder', 'Datum')
            ->setAttribute('data-value', $mass ? $mass->datetime->format('Y-m-d') : DateTime::from(time()+24*60*60)->format('Y-m-d'))
            ->setRequired('Zvolte, prosím, datum mše.');

        $form->addHidden('date_submit');

        $form->addText('time', 'Čas')
            ->setAttribute('placeholder', 'Čas')
            ->setDefaultValue($mass ? $mass->datetime->format('G:i') : '9:00')
            ->setRequired('Zvolte, prosím, čas mše.');

        /*$form->addCheckbox('highlight', 'slavnost')
            ->setDefaultValue($mass ? $mass->highlighted : false);*/

        $form->addText('liturgy', 'Liturgická oslava')
            ->setDefaultValue($mass ? $mass->celebration : null)
            ->setAttribute('placeholder', 'Liturgická oslava; doplněno automaticky');

        $form->addText('intention', 'Intence')
            ->setDefaultValue($mass ? $mass->intention : null)
            ->setAttribute('placeholder', 'Intence - pokud není, ponechte pole prázdné');

        $form->addSubmit('send', 'Uložit');

        $form->onSuccess[] = function (Form $form, $values) {
            $church = $this->churches->getById($values['churchId']);
            if(!$this->user->isLoggedIn() || ($church->maintainer->username != $this->user->identity->username && !$this->user->isInRole('manager'))){
                $this->flashMessage('Nemáte oprávnění upravovat mše tohoto kostela.');
                $this->redirect('Church:', [$this->getParameter('church')]);
            }

            if(empty($values['massId'])){
                $mass = new Mass();
                $mass->church = $church;

                $mass->datetime = DateTime::from($values['date_submit'] . ' ' . $values['time']);
                //$mass->highlighted = $values['highlight'];
                $mass->highlighted = $values['liturgy'] ? true : false;
                $mass->celebration = $values['liturgy'] ? $values['liturgy'] : null;
                $mass->intention = $values['intention'];
                $this->masses->create($mass);
                $this->masses->flush();
                $this->flashMessage('Mše byla vytvořena.');
                $this->redirect('Church:', [$this->getParameter('church')]);
            }else{
                /** @var Mass $mass */
                $mass = $this->masses->getById($values['massId']);
                $mass->datetime = DateTime::from($values['date_submit'] . ' ' . $values['time']);
                //$mass->highlighted = $values['highlight'];
                $mass->highlighted = $values['liturgy'] ? true : false;
                $mass->celebration = $values['liturgy'] ? $values['liturgy'] : null;
                $mass->intention = $values['intention'];
                $this->masses->flush();
                $this->flashMessage('Mše byla upravena.');
                $this->redirect('Church:', [$this->getParameter('church')]);
            }
        };

        return $form;
    }

    public function handleDeleteMass($massId){
        $mass = $this->masses->getById($massId);
        if(!$this->user->isLoggedIn() || ($mass->church->maintainer->username != $this->user->identity->username && !$this->user->isInRole('manager'))) {
            $this->flashMessage('Nemáte oprávnění mazat mše tohoto kostela.');
            $this->redirect('Church:', [$this->getParameter('church')]);
        }
        $this->masses->deleteById($massId);
        $this->masses->flush();
        $this->flashMessage('Mše byla odstraněna.');
        $this->redirect('Church:', [$this->getParameter('church')]);
    }

    public function createComponentAnnouncementForm(){
        $announcement = empty($this->getParameter('editAnnouncement')) ? null : $this->announcements->getById($this->getParameter('editAnnouncement'));

        $form = new Form();
        $form->elementPrototype->setAttribute('class', 'ajax');

        $form->addHidden('churchId')
            ->setDefaultValue($this->getParameter('church'));
        $form->addHidden('announcementId')
            ->setDefaultValue($this->getParameter('editAnnouncement'));

        $form->addTextArea('announcement', 'Ohláška')
            ->setDefaultValue($announcement ? $announcement->content : null)
            ->setAttribute('placeholder', 'Napište ohlášku');

        $form->addSubmit('send', 'Uložit');

        $form->onSuccess[] = function (Form $form, $values) {
            $church = $this->churches->getById($values['churchId']);
            if(!$this->user->isLoggedIn() || ($church->maintainer->username != $this->user->identity->username && !$this->user->isInRole('manager'))){
                $this->flashMessage('Nemáte oprávnění upravovat ohlášky tohoto kostela.');
                $this->redirect('Church:', [$this->getParameter('church')]);
            }

            if(empty($values['announcementId'])){
                $announcement = new Announcement();
                $announcement->church = $church;
                $announcement->lastEdit = DateTime::from(time());
                $announcement->content = $values['announcement'];
                $this->announcements->create($announcement);
                $this->announcements->flush();
                $this->flashMessage('Ohláška byla vytvořena.');
                $this->redirect('Church:', [$this->getParameter('church')]);
            }else{
                /** @var Announcement $announcement */
                $announcement = $this->announcements->getById($values['announcementId']);
                $announcement->lastEdit = DateTime::from(time());
                $announcement->content = $values['announcement'];
                $this->announcements->flush();
                $this->flashMessage('Ohláška byla upravena.');
                $this->redirect('Church:', [$this->getParameter('church')]);
            }
        };

        return $form;
    }

    public function handleDeleteAnnouncement($announcementId){
        $announcement = $this->announcements->getById($announcementId);
        if(!$this->user->isLoggedIn() || ($announcement->church->maintainer->username != $this->user->identity->username && !$this->user->isInRole('manager'))){
            $this->flashMessage('Nemáte oprávnění mazat ohlášky tohoto kostela.');
            $this->redirect('Church:', [$this->getParameter('church')]);
        }
        $this->announcements->deleteById($announcementId);
        $this->announcements->flush();
        $this->flashMessage('Ohláška byla odstraněna.');
        $this->redirect('Church:', [$this->getParameter('church')]);
    }
}
