<?php
/**
 * FlexiPeeHP Bricks
 *
 * @author Vítězslav Dvořák <info@vitexsoftware.cz>
 */

namespace FlexiPeeHP\ui;

/**
 * Description of EmbedResponsive
 *
 * @author vitex
 */
class EmbedResponsive extends \Ease\Html\DivTag
{
    public function finalize()
    {
        $this->addCSS('
.embed-responsive {
    position: relative;
    display: block;
    height: 0;
    padding: 0;
    overflow: hidden;
}
');
        parent::finalize();
    }
}