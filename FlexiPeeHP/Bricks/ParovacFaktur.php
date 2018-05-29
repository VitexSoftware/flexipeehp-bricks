<?php

namespace FlexiPeeHP\Bricks;

/**
 * Description of ParovacFaktur
 *
 * @author vitex
 */
class ParovacFaktur extends \Ease\Sand
{
    /**
     * Objekt Banky
     * @var \FlexiPeeHP\FakturaVydana
     */
    private $invoicer;

    /**
     * Objekt Banky
     * @var \FlexiPeeHP\Banka
     */
    public $banker;

    /**
     * @var Od kdy začít dohledávat doklady
     */
    public $daysBack = 1;

    /**
     * Invoice Types Cache
     * @var array 
     */
    public $docTypes = [];

    /**
     * Párovač faktur
     */
    public function __construct()
    {
        parent::__construct();
        $this->invoicer = new \FlexiPeeHP\FakturaVydana();
        $this->banker   = new \FlexiPeeHP\Banka();
    }

    /**
     * 
     * @return array 
     */
    public function getDocumentTypes()
    {
        $types                            = [];
        $evbackup                         = $this->invoicer->getEvidence();
        $defbackup                        = $this->invoicer->defaultUrlParams;
        $this->invoicer->setEvidence('typ-faktury-vydane');
        $this->invoicer->defaultUrlParams = ['limit' => 0];
        $typesRaw                         = $this->invoicer->getColumnsFromFlexibee([
            'kod', 'typDoklK']);
        $this->invoicer->setEvidence($evbackup);
        $this->invoicer->defaultUrlParams = $defbackup;
        foreach ($typesRaw as $rawtype) {
            $types[$rawtype['kod']] = $rawtype['typDoklK'];
        }
        return $types;
    }

    /**
     * Start set date
     *
     * @param int $daysBack
     */
    public function setStartDay($daysBack)
    {
        $this->addStatusMessage('Start Date '.date('Y-m-d',
                mktime(0, 0, 0, date("m"), date("d") - $daysBack, date("Y"))));
        $this->daysBack = $daysBack;
    }

    /**
     * Vrací dnešní nespárované příjmy na účtu
     *
     * @return array
     */
    public function getPaymentsToProcess($daysBack = 1)
    {
        $result                                  = [];
        $this->banker->defaultUrlParams['order'] = 'datVyst@A';
        $payments                                = $this->banker->getColumnsFromFlexibee([
            'id',
            'kod',
            'varSym',
            'specSym',
            'sumCelkem',
            'datVyst'],
            ["sparovano eq false AND typPohybuK eq 'typPohybu.prijem' AND storno eq false AND datVyst eq '".\FlexiPeeHP\FlexiBeeRW::timestampToFlexiDate(mktime(0,
                        0, 0, date("m"), date("d") - $daysBack, date("Y")))."' "],
            'id');

        if ($this->banker->lastResponseCode == 200) {
            if (empty($payments)) {
                $result = [];
            } else {
                $result = $payments;
            }
        }
        return $result;
    }

    /**
     * Vrací neuhrazené faktury
     *
     * @return array
     */
    public function getInvoicesToProcess()
    {
        $result                                    = [];
        $this->invoicer->defaultUrlParams['order'] = 'datVyst@A';
        $invoices                                  = $this->invoicer->getColumnsFromFlexibee([
            'id',
            'kod',
            'stavUhrK',
            'zbyvaUhradit',
            'firma',
            'buc',
            'varSym',
            'specSym',
            'sumCelkem',
            'duzpPuv',
            'typDokl',
            'datVyst'],
            ["(stavUhrK is null OR stavUhrK eq 'stavUhr.castUhr') AND storno eq false"],
            'id');

        if ($this->invoicer->lastResponseCode == 200) {
            $result = $invoices;
        }
        return $result;
    }

    /**
     * Párování faktur podle příchozích plateb v bance
     */
    public function invoicesMatchingByBank()
    {
        foreach ($this->getPaymentsToProcess($this->daysBack) as $paymentData) {

            $this->addStatusMessage(sprintf('Processing Payment %s %s vs: %s ss: %s %s',
                    $paymentData['kod'], $paymentData['sumCelkem'],
                    $paymentData['varSym'], $paymentData['specSym'],
                    $this->banker->url.'/c/'.$this->banker->company.'/'.$this->banker->getEvidence().'/'.$paymentData['id']),
                'info');


            $invoices = $this->findInvoices($paymentData);
//  kdyz se vrati jedna faktura:
//     kdyz  je prijata castka mensi nebo rovno tak zlikviduji celou
//     kdyz sedi castka, nebo castecne
//  kdyz se vrati vic faktur  tak kdyz sedi castka uhrazuje se ta nejstarsi
//  jinak se uhrazuje castecne

            if (count($invoices) && count(current($invoices))) {
                $prijatoCelkem = floatval($paymentData['sumCelkem']);
                $payment       = new \FlexiPeeHP\Banka($paymentData);

                foreach ($invoices as $invoiceID => $invoiceData) {
                    $invoice = new \FlexiPeeHP\FakturaVydana((int) $invoiceData['id']);

                    /*
                     *    Standardní faktura (typDokladu.faktura)
                     *    Dobropis/opravný daň. d. (typDokladu.dobropis)
                     *    Zálohová faktura (typDokladu.zalohFaktura)
                     *    Zálohový daňový doklad (typDokladu.zdd)
                     *    Dodací list (typDokladu.dodList)
                     *    Proforma (neúčetní) (typDokladu.proforma)
                     *    Pohyb Kč / Zůstatek Kč (typBanUctu.kc)
                     *    Pohyb měna / Zůstatek měna (typBanUctu.mena)
                     */
                    $docType = $this->getOriginDocumentType($invoiceData['typDokl']);
                    switch ($docType) {
                        case 'typDokladu.faktura':
                            if ($this->settleInvoice($invoice, $payment)) ;
                            break;
                        case 'typDokladu.proforma':
                            if ($this->settleProforma($invoice, $paymentData)) ;
                            break;
                        case 'typDokladu.dobropis':
                            if ($this->settleCreditNote($invoice, $paymentData))
                                    ;
                            break;

                        default:
                            $this->addStatusMessage(
                                sprintf(_('Unsupported document type: %s %s'),
                                    $docType.': '.$invoiceData['typDokl'],
                                    $invoice->getApiURL()
                                ), 'warning');
                            break;
                    }

                    $this->banker->loadFromFlexiBee($paymentData['id']);
                    if ($this->banker->getDataValue('sparovano') == true) {
                        break;
                    }
                }
            } else {
                if (!empty($paymentData['varSym']) || !empty($paymentData['specSym'])) {
                    $this->addStatusMessage(_('Invoice found: - overdue?')
                        , 'warning');
                }
            }
        }
    }

    /**
     * Párování faktur dle nezaplacenych faktur
     */
    public function invoicesMatchingByInvoices()
    {
        foreach ($this->getInvoicesToProcess() as $invoiceData) {
            $payments = $this->findPayments($invoiceData);
            if (!empty($payments) && count(current($payments))) {
                $invoice = new \FlexiPeeHP\FakturaVydana($invoiceData);
                $this->invoicer->setMyKey($invoiceData['id']);
                /*
                 *    Standardní faktura (typDokladu.faktura)
                 *    Dobropis/opravný daň. d. (typDokladu.dobropis)
                 *    Zálohová faktura (typDokladu.zalohFaktura)
                 *    Zálohový daňový doklad (typDokladu.zdd)
                 *    Dodací list (typDokladu.dodList)
                 *    Proforma (neúčetní) (typDokladu.proforma)
                 *    Pohyb Kč / Zůstatek Kč (typBanUctu.kc)
                 *    Pohyb měna / Zůstatek měna (typBanUctu.mena)
                 */

                $docType = $this->getOriginDocumentType($invoiceData['typDokl']);
                foreach ($payments as $paymentData) {
                    $payment = new \FlexiPeeHP\Banka($paymentData);
                    switch ($docType) {
                        case 'typDokladu.faktura':
                            if ($this->settleInvoice($invoice, $payment)) {
                                
                            }
                            break;
                        case 'typDokladu.proforma':
                            $this->settleProforma($invoice, $payments);
                            break;
                        case 'typDokladu.dobropis':
                            $this->settleCreditNote($invoice, $payments);
                            break;
                        default:
                            $this->addStatusMessages(sprintf(_('Unsupported invoice type: %s'),
                                    'warning'),
                                $docType.': '.$invoiceData['typDokl']);
                            break;
                    }
                }
            }
        }
    }

    /**
     * Provede "Zaplacení" vydaného dobropisu
     *
     * @param \FlexiPeeHP\FakturaVydana $invoice
     * @param \FlexiPeeHP\Banka $payment
     *
     * @return int vysledek 0 = chyba, 1 = sparovano
     */
    public function settleCreditNote($invoice, $payment)
    {
        $success       = 0;
        $prijataCastka = (float) $payment->getDataValue('sumCelkem');

        if ($prijataCastka < $invoice->getDataValue('zbyvaUhradit')) { //Castecna uhrada
            $this->addStatusMessages(sprinf(_('Castecna uhrada - DOBROPIS: prijato: %s ma byt zaplaceno %s'),
                    $prijataCastka, $invoice->getDataValue('zbyvaUhradit')),
                'warning');
        }
        if ($prijataCastka > $invoice->getDataValue('zbyvaUhradit')) { //Castecna uhrada
            $this->addStatusMessages(sprinf(_('Přeplatek - DOBROPIS: prijato: %s ma byt zaplaceno %s'),
                    $prijataCastka, $invoice->getDataValue('zbyvaUhradit')),
                'warning');

            $this->banker->dataReset();
            $this->banker->setDataValue('id', $payment['id']);
            $this->banker->setDataValue('stitky', 'PREPLATEK');
            $this->banker->insertToFlexiBee();
        }

        if ($invoice->sparujPlatbu($payment, 'castecnaUhrada')) { //Jak se ma FlexiBee zachovat pri preplatku/nedoplatku
            $success = 1;
            $invoice->addStatusMessage(sprintf(_('Platba %s  %s byla sparovana s dobropisem %s'),
                    (string) $payment, $prijataCastka, (string) $invoice),
                'success');
            //PDF Danoveho dokladu priloz k nemu samemu
            //PDF Danoveho dokladu odesli mailem zakaznikovi y FLEXIBEE( nasledne pouzit tabulku Mail/Gandalf)
        }

        return $success;
    }

    /**
     * Provede "Zaplacení" vydané zalohove faktury
     *
     * @param \FlexiPeeHP\FakturaVydana $zaloha
     * @param array $payment
     * 
     * @return int vysledek 0 = chyba, 1 = sparovano, 2 sparovano a vytvorena faktura, -1 sparovnano ale chyba vytvoreni faktury
     */
    public function settleProforma($zaloha, $payment)
    {
        $success       = 0;
        $prijataCastka = (float) $payment['sumCelkem'];

        $platba = new \FlexiPeeHP\Banka((int) $payment['id']);

        if ($zaloha->sparujPlatbu($platba, 'castecnaUhrada')) {
            $success = 1;
            $zaloha->addStatusMessage(sprintf(_('Platba %s  %s byla sparovana s zalohou %s'),
                    \FlexiPeeHP\FlexiBeeRO::uncode($platba), $prijataCastka,
                    (string) $zaloha), 'success');

            if ($zaloha->getDataValue('zbyvaUhradit') > $prijataCastka) { // Castecna Uhrada
//                //Castecna uhrada
//                //Vytvorit ZDD ve vysi payment
//                $zdd = new \FlexiPeeHP\FakturaVydana(['firma' => $zaloha->getDataValue('firma'),
//                    'zavTxt' => $zaloha->getDataValue('zavTxt').' DOPLNIT!!! ',
//                    'varSym' => $zaloha->getDataValue('varSym'),
//                    'popis' => 'Částečná úhrada '.$zaloha->getDataValue('kod')
//                ]);
//
//                $zdd->setDataValue('typDokl', 'code:ZDD');
////                $zdd->setDataValue('zbyvaUhradit', 0); //Mozna nemusime resit -vymazat
////                $zdd->setDataValue('sumCelkem', $prijataCastka);
//                $zdd->setDataValue('szbDphZakl',
//                    $zaloha->getDataValue('szbDphZakl'));
//                $zdd->setDataValue('bezPolozek', true);
////                $zdd->setDataValue('stavUhrK', '');
//                $zdd->unsetDataValue('polozkyFaktury');
//
//                // ---------- Tady se resi sazby - nahrdit objektem pro praci s castkami --------------//
//                // DPH21
//                if ((float) $zaloha->getDataValue('sumCelkZakl')) {
//                    $sumZklZakl = $prijataCastka / ( 1 + (float) $zaloha->getDataValue('szbDphZakl')
//                        / 100 );
//
////                    $zdd->setDataValue('sumZklZakl', round($sumZklZakl, 2));
////                    $zdd->setDataValue('sumDphZakl',
////                        round($prijataCastka - $sumZklZakl, 2));
//                    $zdd->setDataValue('sumCelkZakl', round($prijataCastka, 2));
//                    // DPH00
//                } else {
//                    if ((float) $zaloha->getDataValue('sumOsv')) {
////                        $zdd->setDataValue('sumOsv', round($prijataCastka),
////                            2);
//                    }
//                }
//                $result = $zdd->insertToFlexiBee();
//
//                $zdd->loadFromFlexiBee();
//                $zaloha->debug = true;
//                $zdd->debug    = true;
//
//
//                $targt      = $platba->apiURL.'/vytvor-zdd.json';
//                $zauctovani = '01-02';
//                $value      = $zaloha->getDataValue('kod').'^^^'.$zauctovani;
//                $sender     = new \FlexiPeeHP\FlexiBeeRW();
//                $sender->setPostFields(['zalohaACleneni' => $value]);
//                $result     = $sender->performRequest($targt, 'POST', 'json');
//
//                $result = $zdd->odpocetZDD($zaloha,
//                    ['castkaMen' => $prijataCastka]);
//                if (isset($result['success']) && ($result['success'] == 'true')) {
//                    $success = 2;
//                    $zaloha->addStatusMessage(sprintf(_('Faktura #%s byla sparovana se ZDD'),
//                            $kod), 'success');
//                } else {
//                    $success = -1;
//                    $zaloha->addStatusMessage(sprintf(_('Faktura #%s nebyla sparovana se ZDD'),
//                            $kod), 'error');
//                }
                $zaloha->addStatusMessage(sprintf(_('Částečná úhrada %s'),
                        self::apiUrlToLink($zaloha->apiURL)), 'warning');

                $zaloha->addStatusMessage(sprintf(_('Vytvoř ZDD: %s'),
                        self::apiUrlToLink($platba->apiURL.'/vytvor-zdd')),
                    'debug');
            } else {

                if ($prijataCastka > $zaloha->getDataValue('zbyvaUhradit')) { // Preplatek
                    $zaloha->addStatusMessage(sprintf(_('Přeplatek %s'),
                            self::apiUrlToLink($platba->apiURL)), 'warning');
                }

                //Plna uhrada
                //$toCopy['sumCelkem'] = $payment->getDataValue('sumCelkem');
                //Dopsat pro vsechny mozne sazby dane - vytvorit objekt

                $faktura2 = $this->invoiceCopy($zaloha,
                    ['duzpUcto' => $platba->getDataValue('datVyst'), 'datVyst' => $platba->getDataValue('datVyst')]);
                $id       = (int) $faktura2->getLastInsertedId();
                $faktura2->loadFromFlexiBee($id);
                $kod      = $faktura2->getDataValue('kod');
                $faktura2->dataReset();
                $faktura2->setDataValue('id', 'code:'.$kod);
                $faktura2->setDataValue('typDokl', 'code:FAKTURA');

                $result = $faktura2->odpocetZalohy($zaloha);
                if (isset($result['success']) && ($result['success'] == 'true')) {
                    $success = 2;
                    $zaloha->addStatusMessage(sprintf(_('Faktura #%s byla sparovana'),
                            $kod), 'success');
                } else {
                    $success = -1;
                    $zaloha->addStatusMessage(sprintf(_('Faktura #%s nebyla sparovana'),
                            $kod), 'error');
                }
            }

            //PDF Danoveho dokladu priloz k nemu samemu
            //PDF Danoveho dokladu odesli mailem zakaznikovi y FLEXIBEE( nasledne pouzit tabulku Mail/Gandalf)
        }
        return $success;
    }

    /**
     * Provede "Zaplacení" vydané faktury
     *
     * @param \FlexiPeeHP\FakturaVydana $invoice Invoice to settle
     * @param \FlexiPeeHP\Banka $payment Payment to settle by
     *
     * @return int vysledek 0 = chyba, 1 = sparovano
     */
    public function settleInvoice($invoice, $payment)
    {
        $success       = 0;
        $zbytek        = 'ne';
        $prijataCastka = (float) $payment->getDataValue('sumCelkem');
        $zbyvaUhradit  = $invoice->getDataValue('zbyvaUhradit');

        if ($prijataCastka < $zbyvaUhradit) { //Castecna uhrada
            $this->addStatusMessage(sprintf(_('Castecna uhrada - FAKTURA: prijato: %s ma byt zaplaceno %s'),
                    $prijataCastka, $zbyvaUhradit), 'warning');
            $zbytek = 'castecnaUhrada';
        }
        if ($prijataCastka > $zbyvaUhradit) { //Castecna uhrada
            $this->addStatusMessage(sprintf(_('Přeplatek - FAKTURA: prijato: %s ma byt zaplaceno %s'),
                    $prijataCastka, $invoice->getDataValue('zbyvaUhradit')),
                'warning');

            $this->banker->dataReset();
            $this->banker->setDataValue('id', $payment->getDataValue('id'));
            $this->banker->setDataValue('stitky', 'PREPLATEK');
            $this->banker->insertToFlexiBee();
            $zbytek = 'ignorovat';
        }

        if ($invoice->sparujPlatbu($payment, $zbytek)) { //Jak se ma FlexiBee zachovat pri preplatku/nedoplatku
            $success = 1;
            $invoice->insertToFlexiBee(['id' => (string) $invoice, 'stavMailK' => 'stavMail.odeslat']);
            $invoice->addStatusMessage(sprintf(_('Platba %s  %s byla sparovana s fakturou %s'),
                    \FlexiPeeHP\FlexiBeeRO::uncode($payment->getRecordIdent()),
                    $prijataCastka,
                    \FlexiPeeHP\FlexiBeeRO::uncode($invoice->getRecordIdent())),
                'success');
        }

        return $success;
    }

    /**
     * Provizorní zkopírování faktury
     *
     * @link https://www.flexibee.eu/podpora/Tickets/Ticket/View/28848 Chyba při Provádění akcí přes REST API JSON
     * @param \FlexiPeeHP\FakturaVydana $invoice
     * @param array                     $extraValues Extra hodnoty pro kopii faktury
     *
     * @return \FlexiPeeHP\FakturaVydana
     */
    function invoiceCopy($invoice, $extraValues = [])
    {
        $invoice2 = new \FlexiPeeHP\FakturaVydana(array_merge($invoice->getData(),
                $extraValues));
//        $invoice2->debug = true;
        $invoice2->setDataValue('typDokl', 'code:FAKTURA');
        $invoice2->unsetDataValue('id');
        $invoice2->unsetDataValue('kod');
        if ($invoice2->getDataValue('stavUhrK') != 'stavUhr.uhrazenoRucne') {
            $invoice2->unsetDataValue('stavUhrK');
        }
        $polozky = $invoice2->getDataValue('polozkyFaktury');
        if (count($polozky)) {
            foreach ($polozky as $pid => $polozka) {
                unset($polozky[$pid]['id']);
                unset($polozky[$pid]['datUcto']);
                unset($polozky[$pid]['doklFak']);
                unset($polozky[$pid]['doklFak@showAs']);
                unset($polozky[$pid]['doklFak@ref']);
                $polozky[$pid]['ucetni'] = true;
            }
        }
        $invoice2->setDataValue('polozkyFaktury', $polozky);

        $invoice2->unsetDataValue('external-ids');
//              $invoice2->unsetDataValue('duzpUcto');

        if (isset($extraValues['datVyst'])) {
            $today = $extraValues['datVyst'];
        } else {
            $today = date('Y-m-d');
        }
        $invoice2->setDataValue('duzpPuv', $today);
        $invoice2->setDataValue('duzpUcto', $today);
        $invoice2->setDataValue('datUcto', $today);
        $invoice2->setDataValue('stavMailK', 'stavMail.odeslat');
        $invoice2->insertToFlexiBee();
        if ($invoice2->lastResponseCode == 201) {
            $invoice->addStatusMessage(sprintf(_('Faktura %s byla vytvořena z dokladu %s'),
                    self::apiUrlToLink($invoice2->apiURL),
                    self::apiUrlToLink($invoice->apiURL)), 'success');
        }
        return $invoice2;
    }

    function hotfixDeductionOfAdvances()
    {
        
    }

    /**
     * Najde vydané faktury
     *
     * @param array $paymentData
     * @return array
     */
    public function findInvoices($paymentData)
    {
        $invoices  = [];
        $vInvoices = [];
        $sInvoices = [];
//        $bInvoices = [];


        if (!empty($paymentData['varSym'])) {
            $vInvoices = $this->findInvoice(['varSym' => $paymentData['varSym']]);
        }

        if (!empty($paymentData['specSym'])) {
            $sInvoices = $this->findInvoice(['specSym' => $paymentData['specSym']]);
        }

//      DOPSAT
//      parovani podle cisla uctu
//        if ($paymentData['buc']) {
//            $bInvoices = $this->findInvoice(['buc' => $paymentData['buc']]);
//            foreach ($bInvoices as $invoiceID => $invoice) {
//                if (!array_key_exists($invoiceID, $invoices)) {
//                    $invoices[$invoiceID] = $invoice;
//                }
//            }
//        }
//

        if (count($vInvoices)) {
            foreach ($vInvoices as $invoiceID => $invoice) {
                if (!array_key_exists($invoiceID, $invoices)) {
                    $invoices[$invoiceID] = $invoice;
                }
            }
        }
        if (count($sInvoices)) {
            foreach ($sInvoices as $invoiceID => $invoice) {
                if (!array_key_exists($invoiceID, $invoices)) {
                    $invoices[$invoiceID] = $invoice;
                }
            }
        }
//        foreach ($bInvoices as $invoiceID => $invoice) {
//            if (!array_key_exists($invoiceID, $invoices)) {
//                $invoices[$invoiceID] = $invoice;
//            }
//        }

        if (empty($paymentData['varSym']) && empty($paymentData['specSym'])) {
            $this->banker->dataReset();
            $this->banker->setDataValue('id', $paymentData['id']);
            $this->banker->setDataValue('stitky', 'NEIDENTIFIKOVANO');
            $this->addStatusMessage(_('Neidentifikovaná platba').': '.self::apiUrlToLink($this->banker->apiURL),
                'warning');
            $this->banker->insertToFlexiBee();
        } elseif (count($invoices) == 0) {
            $this->banker->dataReset();
            $this->banker->setDataValue('id', $paymentData['id']);
            $this->banker->setDataValue('stitky', 'CHYBIFAKTURA');
            $this->addStatusMessage(_('Platba bez faktury').': '.self::apiUrlToLink($this->banker->apiURL),
                'warning');
            $this->banker->insertToFlexiBee();
        }

        return $invoices;
    }

    /**
     * Najde příchozí platby
     *
     * @param array $invoiceData
     * @return array
     */
    public function findPayments($invoiceData)
    {
        $pays  = [];
        $sPays = [];
        $bPays = [];

        if (array_key_exists('varSym', $invoiceData) && !empty($invoiceData['varSym'])) {
            $sPays = $this->findPayment(['varSym' => $invoiceData['varSym']]);
            if (is_array($sPays)) {
                $pays = $sPays;
            }
        }

        if (array_key_exists('specSym', $invoiceData) && !empty($invoiceData['specSym'])) {
            $sPays = $this->findPayment(['specSym' => $invoiceData['specSym']]);
            if (is_array($bPays)) {
                $pays = $bPays;
            }
        }

        if (array_key_exists('buc', $invoiceData) && !empty($invoiceData['buc'])) {
            $bPays = $this->findPayment(['buc' => $invoiceData['buc']]);
            if ($bPays) {
                foreach ($bPays as $payID => $payment) {
                    if (!array_key_exists($payID, $pays)) {
                        $pays[$payID] = $payment;
                    }
                }
            }
        }

        return $pays;
    }

    /**
     * Vrací neuhrazene faktury odpovídající zadaným parametrům
     *
     * @param array $what
     * @return array
     */
    public function findInvoice($what)
    {
        $result                                    = null;
        $this->invoicer->defaultUrlParams['order'] = 'datVyst@A';
        $payments                                  = $this->invoicer->getColumnsFromFlexibee([
            'id',
            'varSym',
            'specSym',
            'buc',
            'kod',
            'typDokl',
            'sumCelkem',
            'stitky',
            'datVyst'],
            ["(".\FlexiPeeHP\FlexiBeeRO::flexiUrl($what, 'or').") AND (stavUhrK is null OR stavUhrK eq 'stavUhr.castUhr') AND storno eq false"],
            'id');
        if ($this->invoicer->lastResponseCode == 200) {
            $result = $payments;
        }

        return $result;
    }

    /**
     * Vrací nesparovane platby odpovídající zadaným parametrům
     *
     * @param array $what
     * @return array
     */
    public function findPayment($what)
    {
        $result                                  = null;
        $this->banker->defaultUrlParams['order'] = 'datVyst@A';
        $payments                                = $this->banker->getColumnsFromFlexibee([
            'id',
            'varSym',
            'specSym',
            'buc',
            'sumCelkem',
            'stitky',
            'datVyst'],
            ["(".\FlexiPeeHP\FlexiBeeRO::flexiUrl($what, 'or').") AND sparovano eq 'false'"],
            'id');
        if ($this->banker->lastResponseCode == 200) {
            $result = $payments;
        }

        return $result;
    }

    /**
     * Najde nejlepší platbu pro danou fakturu
     *
     * @param array $payments pole příchozích plateb
     * @param \FlexiPeeHP\FakturaVydana $invoice  faktura ke spárování
     * 
     * @return \FlexiPeeHP\Banka Bankovní pohyb
     */
    public function findBestPayment($payments, $invoice)
    {
        $value = $invoice->getDataValue('sumCelkem');
        foreach ($payments as $paymentID => $payment) {
            if ($payment['sumCelkem'] == $value) {
                return new \FlexiPeeHP\Banka(\FlexiPeeHP\FlexiBeeRO::code($payments[$paymentID]['kod']));
            }
        }

        $symbol = $invoice->getDataValue('specSym');

        $this->addStatusMessage(sprintf(_('Platba pro fakturu %s nebyla dohledána'),
                self::apiUrlToLink($invoice->apiURL)), 'warning');

        return null;
    }

    /**
     * Change url to html link
     *
     * @param string $apiURL
     * 
     * @return string
     */
    public static function apiUrlToLink($apiURL)
    {
        return str_replace('.json?limit=0', '',
            preg_replace("#(^|[\n ])([\w]+?://[\w\#$%&~/.\-;:=,?@\[\]+]*)#is",
                "\\1<a href=\"\\2\" target=\"_blank\" rel=\"nofollow\">\\2</a>",
                $apiURL));
    }

    /**
     * Return Document original type
     * 
     * @param string $typDokl
     * 
     * @return string typDokladu.faktura|typDokladu.dobropis|
     *                typDokladu.zalohFaktura|typDokladu.zdd|
     *                typDokladu.dodList|typDokladu.proforma|
     *                typBanUctu.kc|typBanUctu.mena
     */
    public function getOriginDocumentType($typDokl)
    {
        if (empty($this->docTypes)) {
            $this->docTypes = $this->getDocumentTypes();
        }
        $documentType = \FlexiPeeHP\FlexiBeeRO::uncode($typDokl);
        return array_key_exists($documentType, $this->docTypes) ? $this->docTypes[$documentType]
                : 'typDokladu.neznamy';
    }
}
