<?php

namespace App\Presenters;

use Nette;
use Nette\Utils\DateTime;
use App\Model\LiturgyCollector;
use App\Model\Repository\Announcements;
use App\Model\Repository\Churches;
use App\Model\Repository\Masses;

class ExportPresenter extends BasePresenter
{
    /**
     * @inject
     * @var Churches
     */
    public $churches;

    /**
     * @inject
     * @var Masses
     */
    public $masses;

    /**
     * @inject
     * @var Announcements
     */
    public $announcements;

    /**
     * @inject
     * @var LiturgyCollector
     */
    public $liturgy;

    public function renderDefault($type, $churches, $period, $announcements, $zoom, $massSpacing, $print = false)
    {
        if (empty($type) || empty($churches) || empty($period)) {
            $this->error('Formulář byl vyplněn nesprávně.', 500);
        }

        $this->setLayout('printLayout');
        $churches = explode('a', $churches);

        if ($type == 'banns') {
            $this->setView('banns');

            $this->prepareBanns($type, $churches, $period, $announcements, $zoom, $massSpacing, $print);
        } elseif ($type == 'sundaynews') {
            $this->setView('sundaynews');

            $this->prepareBanns($type, $churches, $period, $announcements, $zoom, $massSpacing, $print);
            $this->prepareSundayNews($type, $churches, $period, $announcements, $zoom, $massSpacing, $print);
        } else {
            $this->error('Neznámý typ požadovaného exportu.');
        }
    }

    private function prepareBanns($type, $churches, $period, $announcements, $zoom, $massSpacing, $print)
    {
        $churchList = $this->churches->getByIds($churches);

        $this->template->churches = $churchList;

        $thisStart = DateTime::from(strtotime(date('o-\\WW')) - 24 * 60 * 60);
        $thisEnd = DateTime::from(strtotime(date('o-\\WW')) + 7 * 24 * 60 * 60 - 1);
        $nextStart = DateTime::from(strtotime(date('o-\\WW', time() + 7 * 24 * 60 * 60)) - 24 * 60 * 60);
        $nextEnd = DateTime::from(strtotime(date('o-\\WW', time() + 7 * 24 * 60 * 60)) + 7 * 24 * 60 * 60 - 1);

        $massList = $this->masses->getByChurches($churchList);
        foreach ($massList as $key => $mass) {
            if ($period == 'this') {
                if ((DateTime::from($mass->datetime) < $thisStart) ||
                    (DateTime::from($mass->datetime) > $thisEnd)
                ) {
                    unset($massList[$key]);
                }
            } else {
                if ((DateTime::from($mass->datetime) < $nextStart) ||
                    (DateTime::from($mass->datetime) > $nextEnd)
                ) {
                    unset($massList[$key]);
                }
            }
        }
        $this->template->masses = $massList;

        $this->template->announcements = $this->announcements->getByChurches($churchList);

        if ($period == 'this') {
            $this->template->weekStart = $thisStart;
            $this->template->weekEnd = $thisEnd;
        } else {
            $this->template->weekStart = $nextStart;
            $this->template->weekEnd = $nextEnd;
        }

        $this->template->announcementsOption = $announcements;

        $this->template->zoom = $zoom;
        $this->template->massSpacing = $massSpacing;

        $this->template->print = $print;

        $this->template->liturgy = $this->liturgy;
    }

    private function prepareSundayNews($type, $churches, $period, $announcements, $zoom, $massSpacing, $print)
    {
        $data = [];

        $data[] = [
            'heading' => 'Vstupní modlitba',
            'source' => null,
            'perex' => null,
            'responsum' => null,
            'content' => 'Všemohoucí Bože, náš nebeský Otče, ty ve své štědrosti dáváš prosícím více, než si zasluhují a žádají; smiluj se nad námi, zbav nás všeho, co tíží naše svědomí, a daruj nám i to, oč se ani neodvažujeme prosit. Skrze tvého Syna…'
        ];

        $data[] = [
            'heading' => '1. čtení',
            'source' => 'Hab 1,2-3; 2,2-4',
            'perex' => 'Habakuk vystupuje před rokem 597 př. Kr., kdy je Judsko v deset let trvajícím ohrožení Babyloňany. První část knihy se skládá z Habakukových otázek Bohu a Božích odpovědí. Poslední věta naší perikopy je zcela zásadní argument v textech Nového zákona (Řím 1,17; Gal 3,11; Žid 10,38). Hebrejské slovo „emuná“ znamená jak „věrnost“, tak také „víru“.',
            'responsum' => null,
            'content' => 'Jak dlouho již volám o pomoc, Hospodine, – ty však neslyšíš; křičím k tobě: „Násilí!“ – ty však nepomáháš. Proč mi dáváš hledět na bezpráví? Můžeš se dívat na soužení? Zpustošení a násilí je přede mnou, povstávají hádky, rozmáhá se svár. Tu mi Hospodin odpověděl: „Napiš vidění, vyryj ho zřetelně na desky, aby ho mohl každý snadno přečíst. Na určený čas totiž ještě čeká vidění, spěje však k naplnění a nezklame. I když ještě prodlévá, počkej na ně, neboť jistě se splní, nedá se zdržet. Hle, zahynul ten, kdo nebyl upřímný v duši, spravedlivý však bude žít pro svou věrnost.“'
        ];

        $data[] = [
            'heading' => 'Žalm',
            'source' => '95',
            'perex' => 'Nezatvrdit srdce je zásadní podmínka, aby člověk viděl velké Boží skutky. A jedině tak může chválit Boha.',
            'responsum' => 'Kéž byste dnes uposlechli jeho hlasu! Nezatvrzujte svá srdce!',
            'content' => 'Pojďme, jásejme Hospodinu, – oslavujme skálu své spásy, – předstupme před něho s chvalozpěvy – a písněmi mu zajásejme! Pojďme, padněme, klaňme se, – poklekněme před svým tvůrcem, Hospodinem! – Neboť on je náš Bůh – a my jsme lid, který pase, stádce vedené jeho rukou. Kéž byste dnes uposlechli jeho hlasu: – „Nezatvrzujte svá srdce jako v Meribě, – jako tehdy v Masse na poušti, – kde mě dráždili vaši otcové, – zkoušeli mě, ač viděli mé činy.“'
        ];

        $data[] = [
            'heading' => '2. čtení',
            'source' => '2 Tim 1,6-8.13-14',
            'perex' => 'Pokračujeme v četbě pastorálních listů. Timoteus byl spolupracovníkem sv. Pavla a později biskupem v Efesu. V textu narazíme na poznámky k tehdejší praxi církve, jako bylo vzkládání rukou v rámci biskupského svěcení či chápání víry jako svěřeného pokladu, tedy čehosi tradovaného - předávaného.',
            'responsum' => 'Kéž byste dnes uposlechli jeho hlasu! Nezatvrzujte svá srdce!',
            'content' => 'Milovaný! Vybízím tě: zase oživ plamen Božího daru, který ti byl dán vzkládáním mých rukou. Vždyť Bůh nám nedal ducha bojácnosti, ale ducha síly, lásky A rozvážnosti! Proto se nestyď veřejně vyznávat našeho Pána ani se nestyď za mě, že nosím kvůli němu pouta. Naopak: Bůh ti dej sílu, abys nesl jako já obtíže spojené s hlásáním evangelia. Jako vzoru zdravé nauky se drž toho, cos ode mě slyšel, a měj přitom víru a lásku v Kristu Ježíši. Ten drahocenný, tobě svěřený poklad opatruj skrze Ducha svatého, který v nás bydlí.'
        ];

        $data[] = [
            'heading' => 'Zpěv před evangeliem',
            'content' => 'Aleluja. Slovo Páně trvá navěky; totiž slovo evangelia, které vám bylo zvěstováno. Aleluja'
        ];

        $data[] = [
            'heading' => 'Evangelium',
            'source' => 'Lk 17,5-10',
            'perex' => 'V předchozích textech nedělních evangelií se mluvilo o bohatství (Lk 16). Nejde jen o to „být chudý“, ale k dosažení Božího království je třeba odpouštět (17,3-4), věřit (17,5-6), ale také mít pravdivé povědomí, kým jsme my a kým je Bůh (17,7-10).',
            'responsum' => null,
            'content' => 'Apoštolové prosili Pána: „Dej nám více víry!“ Pán řekl: „Kdybyste měli víru jako hořčičné zrnko a řekli této moruši: ‘Vyrvi se i s kořeny a přesaď se do moře!’, poslechla by vás. Když někdo z vás má služebníka a ten orá nebo pase, řekne mu snad, až se vrátí z pole: ‘Hned pojď a sedni si ke stolu’? Spíše mu přece řekne: ‘Připrav mi večeři, přepásej se a obsluhuj mě, dokud se nenajím a nenapiji. Potom můžeš jíst a pít ty.’ Děkuje snad potom tomu služebníkovi, že udělal, co mu bylo přikázáno? Tak i vy, až uděláte všechno, co vám bylo přikázáno, řekněte: ‘Jsme jenom služebníci. Udělali jsme, co jsme byli povinni udělat.’“'
        ];

        $this->template->sundayLiturgy = $data;
    }
}
