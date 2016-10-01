<?php

namespace App\Presenters;

use App\Model\Repository\Churches;
use Nette;

class ChurchListPresenter extends BasePresenter
{
	/**
	 * @inject
	 * @var Churches
	 */
	public $churches;

	public function renderDefault()
	{
		$this->template->churches = $this->churches->getAll();
	}
}
