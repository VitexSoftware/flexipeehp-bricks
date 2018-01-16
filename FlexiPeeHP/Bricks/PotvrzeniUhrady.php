<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace FlexiPeeHP\Bricks;

/**
 * Description of PotvrzeniUhrady
 *
 * @author Vítězslav Dvořák <info@vitexsoftware.cz>
 */
class PotvrzeniUhrady extends \Ease\Mailer
{

    /**
     * Odešle potvrzení úhrady
     * @param \FlexiPeeHP\FakturaVydana $invoice
     */
    public function __construct($invoice)
    {
        $body = new \Ease\Container();

        $this->addItem(new \Ease\Html\DivTag(_('Dear customer,')));
        $this->addItem(new \Ease\Html\DivTag(sprintf(_('we confirm receipt of payment %s %s on %s '),
                    $invoice->getDataValue('sumCelkem'),
                    \FlexiPeeHP\FlexiBeeRO::uncode($invoice->getDataValue('mena')),
                    $invoice->getDataValue('kod'))));


        parent::__construct($to,
            _('Confirmation of receipt of invoice payment'), $body);
    }
}
