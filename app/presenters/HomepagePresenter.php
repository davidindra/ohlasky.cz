<?php

namespace App\Presenters;

use Nette;
use App\Model;

class HomepagePresenter extends BasePresenter
{
	public function renderDefault()
	{

	}

	public function actionSignOut()
	{
		$this->getUser()->logout(true);
		$this->flashMessage('Byl jste úspěšně odhlášen.');
		$this->redirect('Homepage:');
	}
}
