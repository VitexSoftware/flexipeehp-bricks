<?php
/**
 * FlexiPeeHP - Example Accept WebHook
 *
 * @author     Vítězslav Dvořák <info@vitexsofware.cz>
 * @copyright  (G) 2017 Vitex Software
 */
namespace FlexiPeeHP\Bricks;

include_once './config.php';
include_once '../vendor/autoload.php';
include_once './common.php';

$searcher = new \FlexiPeeHP\Cenik();

$searchColumn = $oPage->getRequestValue('column');
$searchTerm = $oPage->getRequestValue('q');

$found = $searcher->getColumnsFromFlexibee(['id','kod','nazev'], ["$searchColumn like similar '$searchTerm'"]);

header('Content-Type: application/json');
echo json_encode($found);
