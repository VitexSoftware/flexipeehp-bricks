<?php 
$I = new AcceptanceTester($scenario);
$I->wantTo('perform actions and see result');

$I->amOnPage('/');
$I->click("ConnectionInfo.php");
$I->cantSee('Warning');

$I->seeElement("a .btn");
