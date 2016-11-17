<?php

namespace App\Presenters;

use App\Model\Entity\LiturgyText;
use App\Model\Repository\LiturgyTexts;
use Nette;
use Nette\Application\UI\Form;
use Nette\Utils\DateTime;
use Tracy\Debugger;

class LiturgyTextsPresenter extends BasePresenter
{
	/** @var LiturgyTexts @inject */
	public $liturgyTexts;

	public function startup()
	{
		if(!$this->user->isInRole('manager')){
			$this->flashMessage('Nemáte oprávnění pro přístup k této stránce.');
			$this->redirect('Homepage:');
		}

		parent::startup();
	}

	public function renderDefault()
	{
		$this->template->liturgyTexts = $this->liturgyTexts->getAll();
	}

	public function actionEdit($id = null, $date = null)
	{
		
	}

	public function createComponentTextForm(){
		$liturgyText = empty($this->getParameter('id')) ? null : $this->liturgyTexts->getById($this->getParameter('id'));

		$form = new Form();
		$form->elementPrototype->setAttribute('class', 'ajax');

		$form->addHidden('textId')
			->setDefaultValue($this->getParameter('id'));

		$form->addText('date', 'Datum')
			->setAttribute('placeholder', 'Datum')
			->setAttribute('data-value', $liturgyText ? $liturgyText->date->format('Y-m-d') : DateTime::from(time()+24*60*60)->format('Y-m-d'))
			->setRequired('Zvolte, prosím, datum.');

		$form->addHidden('date_submit');

		$form->addText('heading', 'Název')
			->setDefaultValue($liturgyText ? $liturgyText->heading : null)
			->setAttribute('placeholder', 'Název oddílu liturgických textů (např. Evangelium)')
			->setRequired('Zvolte, prosím, název oddílu.');

		$form->addText('source', 'Zdroj')
			->setDefaultValue($liturgyText ? $liturgyText->source : null)
			->setAttribute('placeholder', 'Zdroj (místo v Bibli)');

		$form->addTextArea('perex', 'Perex')
			->setDefaultValue($liturgyText ? $liturgyText->perex : null)
			->setAttribute('placeholder', 'Úvodník k textu');

		$form->addText('responsum', 'Odpověď (žalmu)')
			->setDefaultValue($liturgyText ? $liturgyText->responsum : null)
			->setAttribute('placeholder', 'Odpověď žalmu');

		$form->addTextArea('content', 'Obsah')
			->setDefaultValue($liturgyText ? $liturgyText->content : null)
			->setAttribute('placeholder', 'Napište obsah oddílu liturgických textů...')
			->setRequired('Vytvořte, prosím, obsah.');

		$form->addSubmit('send', 'Uložit');

		$form->onSuccess[] = function (Form $form, $values) {
			if(empty($values['textId'])){
				$liturgyText = new LiturgyText();

				$liturgyText->date = DateTime::from($values['date_submit']);
				$liturgyText->order = null;
				$liturgyText->heading = $values['heading'];
				$liturgyText->source = $values['source'];
				$liturgyText->perex = $values['perex'];
				$liturgyText->responsum = $values['responsum'];
				$liturgyText->content = $values['content'];

				$this->liturgyTexts->create($liturgyText);

				Debugger::barDump($values);
				$this->flashMessage('Oddíl byl vytvořen.');
				$this->redirect('LiturgyTexts:');
			}else{
				/** @var LiturgyText $liturgyText */
				$liturgyText = $this->liturgyTexts->getById($values['textId']);

				$liturgyText->date = DateTime::from($values['date_submit']);
				$liturgyText->order = null;
				$liturgyText->heading = $values['heading'];
				$liturgyText->source = $values['source'];
				$liturgyText->perex = $values['perex'];
				$liturgyText->responsum = $values['responsum'];
				$liturgyText->content = $values['content'];

				$this->flashMessage('Oddíl byl upraven.');
				$this->redirect('LiturgyTexts:');
			}
		};

		return $form;
	}
	
	public function handleDelete($textId)
	{
		$this->liturgyTexts->deleteById($textId);
		$this->liturgyTexts->flush();
		$this->flashMessage('Oddíl byl odstraněn.');
		$this->redirect('LiturgyTexts:');
	}
}
