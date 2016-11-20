<?php

namespace App\Presenters;

use Nette;
use App\Model;
use Tracy\Debugger;


/**
 * Base presenter for all application presenters.
 */
abstract class BasePresenter extends Nette\Application\UI\Presenter
{
    /** @inject
     *  @var Nette\Http\Request */
    public $httpRequest;

    protected function beforeRender()
    {
        $this->template->url = $this->httpRequest->getUrl();

        $this->template->ga = $this->getParameter('ga');

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

        $this->template->addFilter('gmaps', function($input) {
            $html = new Nette\Utils\Html();
            return $html->setHtml('https://www.google.cz/maps/search/' . $this->template->getLatte()->invokeFilter('escapeurl', [$input]) . '?hl=cs&source=opensearch');
        });

        if($this->isAjax()) {
            $this->redrawControl('title');
            $this->redrawControl('menu');
            $this->redrawControl('flashes');
            $this->redrawControl('container');
        }
    }

    public function deleteDirectory($dir) {
        $files = array_diff(scandir($dir), array('.','..'));
        foreach ($files as $file) {
            (is_dir("$dir/$file")) ? $this->deleteDirectory("$dir/$file") : unlink("$dir/$file");
        }
        return rmdir($dir);
    }
}
