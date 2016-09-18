<?php

namespace App\Presenters;

use Nette;
use App\Model;
use App\Forms;

class HomepagePresenter extends BasePresenter
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
			$this->redirect('Homepage:');
		});
	}

	public function renderDefault()
	{

	}
}
