<?php
/**
 * FlexiPeeHP Bricks - Payment confirmation
 *
 * @author     Vítězslav Dvořák <info@vitexsofware.cz>
 * @copyright  (G) 2017-2018 Vitex Software
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
