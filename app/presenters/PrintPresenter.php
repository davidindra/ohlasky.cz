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

        $churchList = array();
        foreach ($this->churches->getAll() as $church) {
            $churchList[$church->getId()] = $church->name;
        }
        $form->addMultiSelect('churches', 'Výběr kostelů', $churchList)
            ->setRequired('Zvolte, prosím, alespoň jeden kostel.');

        $form->addRadioList('period', 'Období:', ['this' => 'Tento týden', 'next' => 'Následující týden'])
            ->setRequired('Zvolte, prosím, období.')
            ->setDefaultValue('next');

        $form->addSubmit('send', 'Vytisknout');

        $form->onSuccess[] = function (Form $form, $values) {
            if (!$this->user->isLoggedIn()) {
                $this->flashMessage('Nemáte oprávnění tisknout ohlášky.');
                $this->redirect('this');
            }

            $this->redirect(
                'Print:print',
                [
                    $values['type'],
                    implode(',', $values['churches']),
                    $values['period']
                ]
            );
        };

        return $form;
    }

    public function renderPrint($type, $churches, $period)
    {
        if(empty($type) || empty($churches) || empty($period)){
            $this->error('Formulář byl vyplněn nesprávně.', 500);
        }

        $this->setLayout(__DIR__ . '/templates/Print/@printLayout.latte');
        $churches = explode(',', $churches);
        Debugger::barDump([$type, $churches, $period]);

        if ($type == 'banns') {
            $this->setView('banns');

            $churchList = $this->churches->getByIds($churches);

            $this->template->churches = $churchList;

            $massList = $this->masses->getByChurches($churchList);
            foreach ($massList as $key => $mass) {
                if ($period == 'this') {
                    if ((DateTime::from($mass->datetime) < DateTime::from(strtotime('this week', time()))) ||
                        (DateTime::from($mass->datetime) > DateTime::from(strtotime('sunday this week', time() + 24 * 60 * 60 - 1)))
                    ) {
                        unset($massList[$key]);
                    }
                } else {
                    if ((DateTime::from($mass->datetime) < DateTime::from(strtotime('this week', time() + 7 * 24 * 60 * 60))) ||
                        (DateTime::from($mass->datetime) > DateTime::from(strtotime('sunday this week', time() + 7 * 24 * 60 * 60 + 24 * 60 * 60 - 1)))
                    ) {
                        unset($massList[$key]);
                    }
                }
            }
            $this->template->masses = $massList;

            $this->template->announcements = $this->announcements->getByChurches($churchList);

            if($period == 'this'){
                $this->template->weekStart = strtotime('this week', time());
                $this->template->weekEnd = strtotime('sunday this week', time() + 24 * 60 * 60 - 1);
            }else{
                $this->template->weekStart = strtotime('this week', time() + 7 * 24 * 60 * 60);
                $this->template->weekEnd = strtotime('sunday this week', time() + 7 * 24 * 60 * 60 + 24 * 60 * 60 - 1);
            }

            $this->template->liturgy = $this->liturgy;
        } else {
            $this->error('Tisk nedělníků prozatím není podporován.');
        }
    }
}