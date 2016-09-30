<?php

namespace App\Presenters;

use Nette;
use Nette\Application\UI\Form;
use App\Model\Repository\Churches;

class PrintPresenter extends SecuredPresenter
{
    /**
     * @inject
     * @var Churches
     */
    public $churches;

    public function renderDefault()
    {
        /** @var Church[] $churchList */
        $churchList = $this->churches->getAll();

        $this->template->churches = array();
        foreach ($churchList as $church) {
            $this->template->churches[$church->getId()] = $church->name;
        }
    }

    public function createComponentPrintForm()
    {
        $form = new Form();

        $form->addRadioList('type', 'Chci tisknout:', ['banns' => 'Ohlášky', 'sundaynews' => 'Nedělník'])
            ->setRequired('Zvolte, prosím, typ tisku.')
            ->setDefaultValue('banns');

        $churchList = ['' => Nette\Utils\Html::el('option')->value('')->setHtml('Vyberte prosím kostely')->disabled(TRUE)];
        foreach ($this->churches->getAll() as $church) {
            $churchList[$church->getId()] = $church->name . ' (' . $church->maintainer->fullName . ')';
        }
        $form->addMultiSelect('churches', 'Výběr kostelů', $churchList)
            ->setOmitted('')
            ->setRequired('Zvolte, prosím, alespoň jeden kostel.');

        $form->addRadioList('period', 'Období:', ['this' => 'Tento týden', 'next' => 'Následující týden'])
            ->setRequired('Zvolte, prosím, období.')
            ->setDefaultValue('next');

        $form->addRadioList('announcements', 'Ohlášky:', ['yes' => 'ano', 'break' => 'ano, na další stranu', 'no' => 'ne'])
            ->setRequired('Zvolte, prosím, typ tisku ohlášek.')
            ->setDefaultValue('yes');

        $form->addText('zoom', 'Měřítko:')
            ->setType('range')
            ->setAttribute('min', '0.4')
            ->setAttribute('max', '1.6')
            ->setAttribute('step', '0.05')
            ->setDefaultValue(1);

        $form->addText('massSpacing', 'Řádkování bohoslužeb:')
            ->setType('range')
            ->setAttribute('min', '0.1')
            ->setAttribute('max', '0.9')
            ->setAttribute('step', '0.1')
            ->setDefaultValue(0.5);

        $form->addSubmit('send', 'Vytisknout');

        $form->onSuccess[] = function (Form $form, $values) {
            if (!$this->user->isLoggedIn()) {
                $this->flashMessage('Nemáte oprávnění tisknout ohlášky.');
                $this->redirect('this');
            }

            $this->redirect(
                'Export:',
                [
                    $values['type'],
                    implode('a', $values['churches']),
                    $values['period'],
                    $values['announcements'],
                    $values['zoom'],
                    $values['massSpacing'],
                    true
                ]
            );
        };

        return $form;
    }
}
