<?php

namespace App\Presenters;

use Nette;

class AccountPresenter extends BasePresenter
{
	public function actionLogout()
	{
		$this->getUser()->logout(true);
		$this->flashMessage('Byl jste úspěšně odhlášen.');
		$this->redirect('Homepage:', ['ga' => 'logout']);
	}
}
