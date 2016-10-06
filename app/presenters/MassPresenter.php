<?php

namespace App\Presenters;

use App\Model\LiturgyCollector;
use App\Model\Repository\LiturgyDays;
use App\Model\Repository\Masses;
use Nette;
use Nette\Utils\DateTime;

class MassPresenter extends BasePresenter
{
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

	/** @var LiturgyDays @inject */
	public $liturgyDays;

	public function renderDefault()
	{
		$this->template->masses = array();
		foreach($this->masses->getAll() as $mass){
			if(DateTime::from($mass->datetime) > DateTime::from(time())){
				$this->template->masses[] = $mass;
			}
		}

		$this->template->liturgyDays = $this->liturgyDays;
	}
}
