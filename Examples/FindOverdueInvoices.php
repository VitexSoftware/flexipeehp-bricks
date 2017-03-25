#!/usr/bin/php -f
<?php
/**
 * FlexiPeeHP - Example how to find overdue invoices
 *
 * @author     Vítězslav Dvořák <info@vitexsofware.cz>
 * @copyright  (G) 2017 Vitex Software
 */

namespace Example\FlexiPeeHP;

include_once './config.php';
include_once '../vendor/autoload.php';

function getOverdueInvoices()
{
    $invoicer = new \FlexiPeeHP\FakturaVydana();

    $result                              = null;
    $invoicer->defaultUrlParams['order']     = 'datVyst@A';
    $invoicer->defaultUrlParams['relations'] = 'adresar,kontakt';
    $invoices                                = $invoicer->getColumnsFromFlexibee([
        'id',
        'kod',
        'stavUhrK',
        'firma',
        'buc',
        'varSym',
        'specSym',
        'sumCelkem',
        'duzpPuv',
        'datVyst'],
        "(stavUhrK is null OR stavUhrK eq 'stavUhr.castUhr') AND storno eq false",
        'id');

    if ($invoicer->lastResponseCode == 200) {
        $result = $invoices;
    }
    return $result;
}

$firmer = new \FlexiPeeHP\Adresar();

foreach (getOverdueInvoices() as $invoice) {
    $kontakt = $firmer->getColumnsFromFlexibee(['nazev', 'email'],
        ['id' => $invoice['firma']]);
    $firmer->addStatusMessage(implode(',', $kontakt[0]), 'success');
    print_r($invoice);
}
