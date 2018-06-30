<?php
/**
 * Try to connecte to FlexiBee by form 
 * Show Connection Status
 * 
 * @author Vítězslav Dvořák <info@vitexsoftware.cz>
 */
require_once '../vendor/autoload.php';

$oPage = new \Ease\TWB\WebPage(_('FlexiBee connection probe'));

$connForm = new FlexiPeeHP\ui\ConnectionForm();
$connForm->fillUp($_REQUEST);


$container = $oPage->addItem(new \Ease\TWB\Container($connForm));

$container->addItem( new \Ease\TWB\Well( new \FlexiPeeHP\ui\StatusInfoBox(null, $_REQUEST)));

$container->addItem( $oPage->getStatusMessagesAsHtml() );

$oPage->draw();
