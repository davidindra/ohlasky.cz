<?php

namespace App\Presenters;

use App\Model\Repository\LiturgyDays;
use App\Model\Repository\LiturgyTexts;
use Nette;
use Nette\Utils\DateTime;
use App\Model\LiturgyCollector;
use App\Model\Repository\Announcements;
use App\Model\Repository\Churches;
use App\Model\Repository\Masses;

class ExportPresenter extends BasePresenter
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

    /**
     * @inject
     * @var LiturgyDays
     */
    public $liturgyDays;

    /** @var LiturgyTexts @inject */
    public $liturgyTexts;

    public function renderDefault($type, $churches, $period, $announcements, $zoom, $massSpacing, $print = false)
    {
        if (empty($type) || empty($churches) || empty($period)) {
            $this->error('Formulář byl vyplněn nesprávně.', 500);
        }

        $this->setLayout('printLayout');
        $churches = explode('a', $churches);

        if ($type == 'banns') {
            $this->setView('banns');

            $this->prepareBanns($type, $churches, $period, $announcements, $zoom, $massSpacing, $print);
        } elseif ($type == 'sundaynews') {
            $this->setView('sundaynews');

            $this->prepareBanns($type, $churches, $period, $announcements, $zoom, $massSpacing, $print);
            $this->prepareSundayNews($type, $churches, $period, $announcements, $zoom, $massSpacing, $print);
        } else {
            $this->error('Neznámý typ požadovaného exportu.');
        }
    }

    private function prepareBanns($type, $churches, $period, $announcements, $zoom, $massSpacing, $print)
    {
        $churchList = $this->churches->getByIds($churches);

        $this->template->churches = $churchList;

        $thisStart = DateTime::from(strtotime(date('o-\\WW')) - 24 * 60 * 60);
        $thisEnd = DateTime::from(strtotime(date('o-\\WW')) + 7 * 24 * 60 * 60 - 1);
        $nextStart = DateTime::from(strtotime(date('o-\\WW', time() + 7 * 24 * 60 * 60)) - 24 * 60 * 60);
        $nextEnd = DateTime::from(strtotime(date('o-\\WW', time() + 7 * 24 * 60 * 60)) + 7 * 24 * 60 * 60 - 1);

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

        $this->template->announcementsOption = $announcements;

        $this->template->zoom = $zoom;
        $this->template->massSpacing = $massSpacing;

        $this->template->print = $print;

        $this->template->liturgyDays = $this->liturgyDays;
    }

    private function prepareSundayNews($type, $churches, $period, $announcements, $zoom, $massSpacing, $print)
    {
        if($period == 'this') {
            $this->template->sundayLiturgy = $this->liturgyTexts->getByDate(DateTime::from(strtotime(date('o-\\WW')) - 24 * 60 * 60));
        }else{
            $this->template->sundayLiturgy = $this->liturgyTexts->getByDate(DateTime::from(strtotime(date('o-\\WW', time() + 7 * 24 * 60 * 60)) - 24 * 60 * 60));
        }
    }
}
