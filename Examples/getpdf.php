<?php
/**
 * Feed Browser pdf of documnet in default english language
 * 
 * @author Vítězslav Dvořák <info@vitexsoftware.cz>
 */

require_once '../vendor/autoload.php';

$oPage = new \Ease\WebPage();
\Ease\Shared::instanced()->loadConfig(dirname(__DIR__).'/tests/client.json');


$embed    = $oPage->getRequestValue('embed');
$id       = $oPage->getRequestValue('id');
$evidence = $oPage->getRequestValue('evidence');
$lang     = $oPage->getRequestValue('lang');


$document = new \FlexiPeeHP\FlexiBeeRO(is_numeric($id) ? intval($id) : $id,
    ['evidence' => $evidence]);

if (!is_null($document->getMyKey())) {
    $documentBody = $document->getInFormat('pdf',null, empty($lang) ? 'en' : $lang );

    if ($embed != 'true') {
        header('Content-Description: File Transfer');
        header('Content-Disposition: attachment; filename='.$document->getEvidence().'_'.$document.'.pdf');
        header('Content-Type: application/octet-stream');
        header('Content-Transfer-Encoding: binary');
    } else {
        header('Content-Type: application/pdf');
    }
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Pragma: public');
    header('Content-Length: '.strlen($documentBody));
    echo $documentBody;
} else {
    die(_('Wrong call'));
}
