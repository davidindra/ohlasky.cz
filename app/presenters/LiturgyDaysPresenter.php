<?php

namespace App\Presenters;

use App\Model\Entity\LiturgyDay;
use App\Model\Repository\LiturgyDays;
use Nette;
use Nette\Application\UI\Form;
use Nette\Utils\DateTime;
use Tracy\Debugger;

class LiturgyDaysPresenter extends BasePresenter
{
    /** @var LiturgyDays @inject */
    public $liturgyDays;

    public function startup()
    {
        if (!$this->user->isInRole('manager')) {
            $this->flashMessage('Nemáte oprávnění pro přístup k této stránce.');
            $this->redirect('Homepage:');
        }

        parent::startup();
    }

    public function renderDefault($edit = null)
    {
        $this->template->liturgyDays = $this->liturgyDays->getAll();
        $this->template->editId = $edit;
    }

    public function createComponentDayForm()
    {
        $liturgyDay = empty($this->getParameter('edit')) ? null : $this->liturgyDays->getById($this->getParameter('edit'));

        $form = new Form();
        $form->elementPrototype->setAttribute('class', 'ajax');

        $form->addHidden('dayId')
            ->setDefaultValue($this->getParameter('edit'));

        $form->addText('date', 'Datum')
            ->setAttribute('placeholder', 'Datum')
            ->setAttribute('data-value', $liturgyDay ? $liturgyDay->date->format('Y-m-d') : DateTime::from(time() + 24 * 60 * 60)->format('Y-m-d'))
            ->setRequired('Zvolte, prosím, datum.');

        $form->addHidden('date_submit');

        $form->addText('description', 'Popis')
            ->setDefaultValue($liturgyDay ? $liturgyDay->description : null)
            ->setAttribute('placeholder', 'Název dne podle liturgického kalendáře');

        $form->addSubmit('send', 'Uložit');

        $form->onSuccess[] = function (Form $form, $values) {
            if (empty($values['dayId'])) {
                $liturgyDay = new LiturgyDay();

                $liturgyDay->date = DateTime::from($values['date_submit']);
                $liturgyDay->description = $values['description'];
                $this->liturgyDays->create($liturgyDay);
                $this->liturgyDays->flush();
                $this->flashMessage('Den byl vytvořen.');
                $this->redirect('LiturgyDays:');
            } else {
                /** @var LiturgyDay $liturgyDay */
                $liturgyDay = $this->liturgyDays->getById($values['dayId']);
                $liturgyDay->date = DateTime::from($values['date_submit']);
                $liturgyDay->description = $values['description'];
                $this->liturgyDays->flush();
                $this->flashMessage('Den byl upraven.');
                $this->redirect('LiturgyDays:');
            }
        };

        return $form;
    }

    public function handleDelete($dayId)
    {
        $this->liturgyDays->deleteById($dayId);
        $this->liturgyDays->flush();
        $this->flashMessage('Den byl odstraněn.');
        $this->redirect('LiturgyDays:');
    }
}
