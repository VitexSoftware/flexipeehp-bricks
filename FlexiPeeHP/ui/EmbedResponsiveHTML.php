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
class EmbedResponsiveHTML extends EmbedResponsive
{

    /**
     * Ebed Document's HTML to Page
     *
     * @param \FlexiPeeHP\FlexiBeeRO $source object with document
     * @param string                 $feeder script can send us the pdf
     */
    public function __construct($source, $feeder = 'gethtml.php')
    {
        $url = $feeder.'?evidence='.$source->getEvidence().'&id='.$source->getMyKey().'&embed=true';

        parent::__construct('<iframe src=\''.$url.'\' type=\'text/html\' width=\'100%\' height=\'100%\'></iframe>',
            ['class' => 'embed-responsive', 'style' => 'padding-bottom:150%']);
    }
}