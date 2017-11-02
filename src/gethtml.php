<?php

namespace Orderer;

include_once '../vendor/autoload.php';

$engine = new FlexiBeeEngine(['config' => '../config.json']);

$oPage = new \Ease\TWB\WebPage('HTML');

$embed    = $oPage->getRequestValue('embed');
$id       = $oPage->getRequestValue('id');
$evidence = $oPage->getRequestValue('evidence');


$document = new \FlexiPeeHP\FlexiBeeRO(is_numeric($id) ? intval($id) : $id,
    ['evidence' => $evidence]);

if (!is_null($document->getMyKey())) {
    $documentBody = $document->getInFormat('html');

    $documentBody = str_replace(['src="/', 'href="/'],
        ['src="'.$document->url.'/', 'href="'.$document->url.'/'], $documentBody);

    if ($embed != 'true') {
        header('Content-Description: File Transfer');
        header('Content-Disposition: attachment; filename='.$document->getEvidence().'_'.$document.'.html');
        header('Content-Type: application/octet-stream');
        header('Content-Transfer-Encoding: binary');
    } else {
        header('Content-Type: text/html');
    }
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Pragma: public');
    header('Content-Length: '.strlen($documentBody));
    echo $documentBody;
} else {
    die(_('Wrong call'));
}
