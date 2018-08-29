<?php

namespace FlexiPeeHP;

/**
 * Address Book Record Editor form usage example
 * 
 * @author Vítězslav Dvořák <info@vitexsoftware.cz>
 */
require_once '../vendor/autoload.php';
\Ease\Shared::instanced()->loadConfig(dirname(__DIR__).'/tests/client.json',
    true);

$oPage = new \Ease\TWB\WebPage(_('Address editor'));

$addressId = $oPage->getRequestValue('id');

if (empty($addressId)) {
    $form = new \Ease\TWB\Form('idform');
    $form->addInput(new \Ease\Html\InputTextTag('id'),
        _('FlexiBee address identifier'));
} else {
    $adresser = new Adresar(is_numeric($addressId) ? (int) $addressId : $addressId);
    if ($oPage->isPosted()) {
        if ($adresser->sync($_POST)) {
            $oPage->addItem(new \Ease\TWB\Label('success', _('Address saved')));
        } else {
            $oPage->addItem(new \Ease\TWB\Label('error',
                _('Address save failed')));
        }
    }

    $form = new ui\AdresarForm($adresser);
}


$oPage->addItem(new \Ease\TWB\Container($form));


$oPage->draw();
