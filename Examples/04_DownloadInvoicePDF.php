#!/usr/bin/php -f
<?php
/**
 * FlexiPeeHP - Example how download PDF (of Invoice)
 *
 * @author     Vítězslav Dvořák <info@vitexsofware.cz>
 * @copyright  (G) 2017 Vitex Software
 */

namespace Example\FlexiPeeHP;

include_once './config.php';
include_once '../vendor/autoload.php';

echo "Please enter invoice ID ( one of https://demo.flexibee.eu/c/demo/faktura-vydana.json?detail=custom;id&limit=0 ) to copy:";
$input     = fopen("php://stdin", "r");
$invoiceID = trim(fgets($input));
fclose($input);
if (is_numeric($invoiceID)) {
    $invoiceID = intval($invoiceID);
}

/*
 * FlexiPeeHP Classes accept this form of initial identifier:
 *
 * (int) 2588
 * (string) ext:ESHOP:oi1978
 * (array) ['varSym'=>'20080015']
 */

$invoice = new \FlexiPeeHP\FakturaVydana($invoiceID);
$pdfUrl  = $invoice->getEvidenceURL().'/'.urlencode($invoice).'.pdf';
$saveTo  = $invoice->getEvidence().'-'.$invoice->getDataValue('id').'.pdf';

file_put_contents($saveTo, file_get_contents($pdfUrl));
