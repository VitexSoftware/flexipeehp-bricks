<?php

namespace FlexiPeeHP;

include_once './config.php';
include_once '../vendor/autoload.php';
include_once './common.php';

\Ease\TWB\Part::twBootstrapize();

$form = new \Ease\TWB\Form('Search');
$form->addItem(new ui\SearchBox('kod'));


$oPage->addItem(new \Ease\TWB\Container($form));

$oPage->draw();

