<?php
/**
 * FlexiPeeHP - Example how to show connection check InfoBox
 *
 * @author     Vítězslav Dvořák <info@vitexsofware.cz>
 * @copyright  (G) 2017 Vitex Software
 */

namespace FlexiPeeHP\Bricks;

include_once './config.php';
include_once '../vendor/autoload.php';
include_once './common.php';

$reminder = new Upominac();
$reminder->processAllDebts();
