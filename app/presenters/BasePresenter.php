<?php

namespace App\Presenters;

use Nette;
use App\Model;
use App\Forms;
use Tracy\Debugger;


/**
 * Base presenter for all application presenters.
 */
abstract class BasePresenter extends Nette\Application\UI\Presenter
{
    /** @persistent */
    public $backlink = '';

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
            $this->restoreRequest($this->backlink);
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

        $this->template->addFilter('dump', function ($input) {
            $html = new Nette\Utils\Html();
            return $html->setHtml(Debugger::dump($input, true));
        });

        $this->template->addFilter('markdown', function($input) {
            $md = \Parsedown::instance();
            $html = new Nette\Utils\Html();
            return $html->setHtml($md->line($this->template->getLatte()->invokeFilter('breaklines', [$input])));
        });
    }

    public function deleteDirectory($dir) {
        $files = array_diff(scandir($dir), array('.','..'));
        foreach ($files as $file) {
            (is_dir("$dir/$file")) ? $this->deleteDirectory("$dir/$file") : unlink("$dir/$file");
        }
        return rmdir($dir);
    }
}
