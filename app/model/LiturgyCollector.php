<?php
namespace App\Model;

use Nette;
use GuzzleHttp\Client;
use Nette\Caching\Cache;
use Tester\Environment;

/**
 * Class LiturgyCollector cares about fetching liturgy texts & day names from Katolik.cz
 * @package App\Model
 */
class LiturgyCollector extends Nette\Object
{
    /**
     * GuzzleHTTP Client instance
     * @var Client
     */
    private $guzzle;

    /**
     * Nette Cache mechanism
     * @var Cache
     */
    private $cache;

    /**
     * LiturgyCollector constructor.
     * @param Client $client GuzzleHTTP client instance
     * @param Cache $cache Nette Cache
     */
    public function __construct(Client $client, Cache $cache = null)
    {
        $this->guzzle = $client;

        if(!$cache) {
            $storage = new Nette\Caching\Storages\FileStorage(__DIR__ . '/../../temp');
            $this->cache = new Cache($storage, 'Liturgy');
        }else{
            $this->cache = $cache;
        }
    }

    /**
     * Gets liturgical name of the day
     * @param Nette\Utils\DateTime $dateTime
     * @return LiturgyDayInfo
     */
    public function getDayInfo(Nette\Utils\DateTime $dateTime)
    {
        return $this->cache->load('dayInfo-' . $dateTime->format('Y-m-d'), function() use ($dateTime){
            $document = $this->guzzle->get(
                'http://www.katolik.cz/kalendar/kalendar.asp'
                . '?d=' . $dateTime->format('d')
                . '&m=' . $dateTime->format('m')
                . '&r=' . $dateTime->format('Y'))
                ->getBody()
                ->getContents();

            //$document = mb_convert_encoding($document, 'utf-8', 'cp1252');
            $document = iconv('cp1250', 'utf-8', $document);

            /** @var \QueryPath\DOMQuery $dom */
            $dom = \QueryPath::withHTML5($document);

            $dayInfo = new LiturgyDayInfo();
            $dayInfo->dateTime = $dateTime;
            $dayInfo->dayCivil = mb_strtolower(trim($dom->find('body div.wrapper div.middle div.normal table tr:nth-child(1) td:nth-child(2) table tr:nth-child(1) td')->text()));
            $dayInfo->dayEcclesiastical = trim($dom->find('body div.wrapper div.middle div.normal table tr:nth-child(1) td:nth-child(2) table tr:nth-child(2) td')->text());
            $dayInfo->feastCivil = trim($dom->find('table.kalendar tr:nth-child(1) td:nth-child(2) table tr:nth-child(3) td div')->text());
            $dayInfo->feastEcclesiastical = trim($dom->find('table.kalendar tr:nth-child(1) td:nth-child(2) table tr:nth-child(4) td div:nth-of-type(1)')->text());
            $dayInfo->colorLiturgic = mb_strtolower(trim($dom->find('table.kalendar tr:nth-child(1) td:nth-child(2) table tr:nth-child(5) td font')->text()));
            $dayInfo->cycleLiturgic = mb_strtoupper(substr(trim($dom->find('table.kalendar tr:nth-child(2) td:nth-child(2) table tr:nth-child(1) td')->text()), -2, 1));

            return $dayInfo;
        });
    }

    private function getTexts(Nette\Utils\DateTime $dateTime)
    {
        $document = $this->guzzle->get(
            'http://www.katolik.cz/kalendar/vypis_lit.asp'
            . '?d=' . $dateTime->format('d')
            . '&m=' . $dateTime->format('m')
            . '&r=' . $dateTime->format('Y'))
            ->getBody()
            ->getContents();
        $document = iconv('cp1250', 'utf-8', $document);
        $dom = \QueryPath::withHTML5($document);
    }
}

class LiturgyParsingException extends \Exception
{
}

class LiturgyDayInfo extends Nette\Object
{
    public $dateTime;
    public $feastCivil;
    public $feastEcclesiastical;
    public $dayCivil;
    public $dayEcclesiastical;
    public $cycleLiturgic;
    public $colorLiturgic;
}