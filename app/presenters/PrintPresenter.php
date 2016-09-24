<?php

namespace App\Presenters;

use Nette;
use App\Model\Entity\Church;
use App\Model\Repository\Churches;

class PrintPresenter extends BasePresenter
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
        foreach($churchList as $church){
            $this->template->churches[$church->getId()] = $church->name;
        }
    }

    public function renderPrint(){
        $this->setLayout(__DIR__ . '/templates/Print/@printLayout.latte');

        $this->template->setFile(__DIR__ . '/templates/Print/banns.latte');
    }
}
