<?php

namespace App\Presenters;

use Nette;
use App\Forms;

class AccountPresenter extends BasePresenter
{
	/** @var Forms\SignInFormFactory @inject */
	public $signInFactory;

	/**
	 * Sign-in form factory.
	 * @return Nette\Application\UI\Form
	 */
	protected function createComponentSignInForm()
	{
		return $this->signInFactory->create(function () {
			$this->flashMessage('Byl jste úspěšně přihlášen!');
			$this->redirect('ChurchList:', ['ga' => 'login']);
		});
	}

	public function renderLogin(){

	}

	public function actionLogout()
	{
		$this->getUser()->logout(true);
		$this->flashMessage('Byl jste úspěšně odhlášen.');
		$this->redirect('Homepage:', ['ga' => 'logout']);
	}
}
