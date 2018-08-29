<?php
/**
 * Convert Documents
 * 
 * @author Vítězslav Dvořák <info@vitexsoftware.cz>
 */

require_once '../vendor/autoload.php';

function unc($code)
{
    return \FlexiPeeHP\FlexiBeeRO::uncode($code);
}

/**
 * Prepare testing payment
 * 
 * @param array $initialData
 * 
 * @return \FlexiPeeHP\Banka
 */
function makePayment($initialData = [], $dayBack = 1)
{
    $yesterday = new \DateTime();
    $yesterday->modify('-'.$dayBack.' day');

    $testCode = 'PAY_'.\Ease\Sand::randomString();

    $payment = new \FlexiPeeHP\Banka($initialData);

    $payment->takeData(array_merge([
        'kod' => $testCode,
        'banka' => 'code:HLAVNI',
        'typPohybuK' => 'typPohybu.prijem',
        'popis' => 'FlexiPeeHP bricks Test bank record',
        'varSym' => \Ease\Sand::randomNumber(1111, 9999),
        'specSym' => \Ease\Sand::randomNumber(111, 999),
        'bezPolozek' => true,
        'datVyst' => \FlexiPeeHP\FlexiBeeRO::dateToFlexiDate($yesterday),
        'typDokl' => \FlexiPeeHP\FlexiBeeRO::code('STANDARD')
            ], $initialData));
    if ($payment->sync()) {
        $payment->addStatusMessage($payment->getApiURL().' '.unc($payment->getDataValue('typPohybuK')).' '.unc($payment->getRecordIdent()).' '.unc($payment->getDataValue('sumCelkem')).' '.unc($payment->getDataValue('mena')),
            'success');
    } else {
        $payment->addStatusMessage(json_encode($payment->getData()), 'debug');
    }
    return $payment;
}


\Ease\Shared::instanced()->loadConfig(dirname(__DIR__).'/tests/client.json');

$prijem = makePayment();
$zdd = new FlexiPeeHP\FakturaVydana(['typDokl' => \FlexiPeeHP\FlexiBeeRO::code('ZDD')]);

$engine = new FlexiPeeHP\Bricks\Convertor($prijem,$zdd);
$zdd = $engine->conversion();

print_r($zdd->getData());
    