<?php

namespace App\Presenters;

use Nette;
use App\Model;
use App\Forms;


/**
 * Base presenter for all application presenters.
 */
abstract class BasePresenter extends Nette\Application\UI\Presenter
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

    protected function beforeRender()
    {
        $this->template->addFilter('czdate', function ($input, $format = 'l j. F G:i') {
            $en = [
                "January", "February", "March", "April", "May", "June",
                "July", "August", "September", "October", "November", "December",
                "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"
            ];
            $cz = [
                "ledna", "února", "března", "dubna", "května", "června",
                "července", "srpna", "září", "října", "listopadu", "prosince",
                "pondělí", "úterý", "středa", "čtvrtek", "pátek", "sobota", "neděle"
            ];
            return str_replace(
                $en, $cz,
                date(
                    $format,
                    Nette\Utils\DateTime::from($input)->getTimestamp()
                )
            );
        });
    }
}
