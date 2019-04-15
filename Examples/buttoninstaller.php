<?php
/**
 * FlexiBee Custom Button Installer 
 * 
 * @author Vítězslav Dvořák <info@vitexsoftware.cz>
 */

namespace FlexiPeeHP\Bricks;

require_once dirname(__DIR__).'/vendor/autoload.php';

$oPage = new \Ease\TWB\WebPage(_('FlexiBee Custom Button Installer'));

$loginForm = new \FlexiPeeHP\ui\ConnectionForm('install.php');
$loginForm->addInput(new \Ease\ui\TWBSwitch('browser',
        isset($_REQUEST) && array_key_exists('browser', $_REQUEST), 'automatic',
        ['onText' => _('FlexiBee WebView'), 'offText' => _('System Browser')]),
    _('Open results in FlexiBee WebView or in System default browser'));
$loginForm->fillUp($_REQUEST);

$baseUrl = dirname(\Ease\Page::phpSelf()).'/index.php?authSessionId=${authSessionId}&companyUrl=${companyUrl}';


if ($oPage->isPosted()) {
    $browser = isset($_REQUEST) && array_key_exists('browser', $_REQUEST) ? 'automatic'
            : 'desktop';

    $buttoner = new \FlexiPeeHP\FlexiBeeRW(null,
        array_merge($_REQUEST, ['evidence' => 'custom-button']));


    /* Modify Here: */
    $buttoner->insertToFlexiBee(['id' => 'code:BUTTON EXAMPLE CODE', 'url' => $baseUrl.'&custom=parameters..',
        'title' => _('Example Custom Action'), 'description' => _('Custom Button Description'),
        'location' => 'list', 'evidence' => 'faktura-vydana', 'browser' => $browser]);


    if ($buttoner->lastResponseCode == 201) {
        $oPage->addStatusMessage(_('Custom Button Established'), 'success');

        define('FLEXIBEE_COMPANY', $buttoner->getCompany());
    }
} else {
    $oPage->addStatusMessage(_('My App URL').': '.$baseUrl);
}



$setupRow = new \Ease\TWB\Row();
$setupRow->addColumn(6, $loginForm);
$setupRow->addColumn(6, $oPage->getStatusMessagesAsHtml());

$oPage->addItem(new \Ease\TWB\Container($setupRow));


echo $oPage;


