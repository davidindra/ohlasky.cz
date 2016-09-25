<?php

namespace App\Presenters;

use App\Model\Repository\Announcements;
use Nette;
use Nette\Application\UI\Form;
use Nette\Utils\DateTime;
use Tracy\Debugger;
use App\Model\LiturgyCollector;
use App\Model\Entity\Church;
use App\Model\Repository\Churches;
use App\Model\Repository\Masses;

class PrintPresenter extends SecuredPresenter
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
            $churchList[$church->getId()] = $church->name;
        }
        $form->addMultiSelect('churches', 'Výběr kostelů', $churchList)
            ->setOmitted('')
            ->setRequired('Zvolte, prosím, alespoň jeden kostel.');

        $form->addRadioList('period', 'Období:', ['this' => 'Tento týden', 'next' => 'Následující týden'])
            ->setRequired('Zvolte, prosím, období.')
            ->setDefaultValue('next');

        $form->addCheckbox('breakAnnouncements', 'Ohlášky až na druhou stranu:')
            ->setDefaultValue(false);

        $form->addSubmit('send', 'Vytisknout');

        $form->onSuccess[] = function (Form $form, $values) {
            if (!$this->user->isLoggedIn()) {
                $this->flashMessage('Nemáte oprávnění tisknout ohlášky.');
                $this->redirect('this');
            }

            $this->redirect(
                'Print:export',
                [
                    $values['type'],
                    implode('a', $values['churches']),
                    $values['period'],
                    $values['breakAnnouncements'],
                    true
                ]
            );
        };

        return $form;
    }

    public function renderExport($type, $churches, $period, $breakAnnouncements, $print = false)
    {
        if (empty($type) || empty($churches) || empty($period)) {
            $this->error('Formulář byl vyplněn nesprávně.', 500);
        }

        $this->setLayout(__DIR__ . '/templates/Print/@printLayout.latte');
        $churches = explode('a', $churches);

        if ($type == 'banns') {
            $this->setView('banns');

            $churchList = $this->churches->getByIds($churches);

            $this->template->churches = $churchList;

            $thisStart = DateTime::from(strtotime(date('o-\\WW')) - 24 * 60 * 60);
            $thisEnd = DateTime::from(strtotime(date('o-\\WW')) + 7 * 24 * 60 * 60 - 1);
            $nextStart = DateTime::from(strtotime(date('o-\\WW', time() + 7*24*60*60)) - 24 * 60 * 60);
            $nextEnd = DateTime::from(strtotime(date('o-\\WW', time() + 7*24*60*60)) + 7 * 24 * 60 * 60 - 1);

            $massList = $this->masses->getByChurches($churchList);
            foreach ($massList as $key => $mass) {
                if ($period == 'this') {
                    if ((DateTime::from($mass->datetime) < $thisStart) ||
                        (DateTime::from($mass->datetime) > $thisEnd)
                    ) {
                        unset($massList[$key]);
                    }
                } else {
                    if ((DateTime::from($mass->datetime) < $nextStart) ||
                        (DateTime::from($mass->datetime) > $nextEnd)
                    ) {
                        unset($massList[$key]);
                    }
                }
            }
            $this->template->masses = $massList;

            $this->template->announcements = $this->announcements->getByChurches($churchList);

            if ($period == 'this') {
                $this->template->weekStart = $thisStart;
                $this->template->weekEnd = $thisEnd;
            } else {
                $this->template->weekStart = $nextStart;
                $this->template->weekEnd = $nextEnd;
            }

            $this->template->breakAnnouncements = $breakAnnouncements;

            $this->template->print = $print;

            $this->template->liturgy = $this->liturgy;
        } else {
            $this->error('Tisk nedělníků prozatím není podporován.');
        }
    }
}
