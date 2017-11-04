<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace FlexiPeeHP\Bricks;

/**
 * Description of EmbedResponsive
 *
 * @author vitex
 */
class EmbedResponsive extends \Ease\Html\Div
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