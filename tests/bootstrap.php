<?php
/**
 * FlexiPeeHP-Bricks - Unit Test bootstrap
 *
 * @author Vítězslav Dvořák <info@vitexsoftware.cz>
 * @copyright (c) 2018, Vítězslav Dvořák
 */
if (file_exists('../vendor/autoload.php')) {
    require_once '../vendor/autoload.php'; //Test Run
} else {
    require_once 'vendor/autoload.php'; //Create Test
}
\Ease\Shared::instanced()->loadConfig('tests/client.json');

/*
$banka = 'HLAVNI';

$prober = new FlexiPeeHP\FlexiBeeRW();
$prober->setEvidence('bankovni-ucet');
if (!$prober->recordExists(['kod' => $banka])) {
    $prober->insertToFlexiBee(['kod' => $banka,
        'nazev' => $banka
    ]);
}

$labeler = new FlexiPeeHP\Stitek();
$labeler->createNew('CHYBIFAKTURA', ['banka']);
$labeler->createNew('NEIDENTIFIKOVANO', ['banka']);
*/