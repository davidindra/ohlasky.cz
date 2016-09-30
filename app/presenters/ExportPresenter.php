<?php

namespace App\Presenters;

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

	public function renderDefault($type, $churches, $period, $announcements, $zoom = 1, $massSpacing = 0.5, $print = false)
	{
		if (empty($type) || empty($churches) || empty($period)) {
			$this->error('Formulář byl vyplněn nesprávně.', 500);
		}

		$this->setLayout('printLayout');
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

			$this->template->announcementsOption = $announcements;

			$this->template->zoom = $zoom;
			$this->template->massSpacing = $massSpacing;

			$this->template->print = $print;

			$this->template->liturgy = $this->liturgy;
		} else {
			$this->error('Tisk nedělníků prozatím není podporován.');
		}
	}
}
