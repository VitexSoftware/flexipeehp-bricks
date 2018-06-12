<?php
/**
 * List 20 Invoices
 * 
 * @author Vítězslav Dvořák <info@vitexsoftware.cz>
 */

require_once '../vendor/autoload.php';

$oPage = new \Ease\TWB\WebPage();
\Ease\Shared::instanced()->loadConfig(dirname(__DIR__).'/tests/client.json');


$container  = $oPage->addItem(new \Ease\TWB\Container());
$container->addItem(new \Ease\Html\H1Tag('20 invoices'));

$invoiceRow = new \Ease\TWB\Row();
$invoiceRow->addColumn(2, 'FlexiBee Code');
$invoiceRow->addColumn(2, 'Remote Number');
$invoiceRow->addColumn(2, 'Amount');
$invoiceRow->addColumn(2, 'Company');

$container->addItem($invoiceRow);


$invoice = new \FlexiPeeHP\FakturaVydana();

$invoices = $invoice->getColumnsFromFlexibee(['id', 'kod', 'cisDosle', 'sumCelkem',
    'nazFirmy'], ['storno' => false,'limit'=>20]);

if (!empty($invoices)) {
    $listing = new \Ease\Html\UlTag();

    foreach ($invoices as $invoiceData) {
        $invoiceRow = new \Ease\TWB\Row();
        $invoiceRow->addColumn(2, $invoiceData['kod']);
        $invoiceRow->addColumn(2, $invoiceData['cisDosle']);
        $invoiceRow->addColumn(2, $invoiceData['sumCelkem']);
        $invoiceRow->addColumn(2, $invoiceData['nazFirmy']);
        $container->addItem(new \Ease\Html\ATag('embed.php?id='.$invoiceData['id'].'&evidence='.$invoice->getEvidence(),
                $invoiceRow));
    }
} else {
    $oPage->addItem(new \Ease\Html\H1Tag('No Invoices'));
}

$oPage->draw();
