<?php

namespace SpojeNet\System;

/**
 * FlexiPeeHP Brick - Send Payment confirmation
 *
 * @author     Vítězslav Dvořák <vitex@arachne.cz>
 * @copyright  2015 Spoje.Net
 */
namespace FlexiPeeHP\Bricks;

include_once './config.php';
include_once '../vendor/autoload.php';
include_once './common.php';

$faktura = new \FlexiPeeHP\FakturaVydana(['typDokl' => 'code:FAKTURA', 'firma' => 'code:VITEX',
    'sumZklZakl' => \Ease\Sand::randomNumber(1000, 9999), 'bezPolozek' => true]);
$faktura->refresh();

$potvrzovac = new PotvrzeniUhrady($faktura);
$potvrzovac->send();

