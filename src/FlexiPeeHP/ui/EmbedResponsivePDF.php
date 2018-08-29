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
class EmbedResponsivePDF extends EmbedResponsive
{

    /**
     * Ebed Document's PDF to Page
     * 
     * @link https://www.flexibee.eu/api/dokumentace/ref/pdf/ PDF Export
     *
     * @param \FlexiPeeHP\FlexiBeeRO $source object with document
     * @param string                 $feeder script can send us the pdf
     * @param string                 $report printSet name
     */
    public function __construct($source, $feeder = 'getpdf.php', $report = null)
    {
        $addParams = ['evidence'=> $source->getEvidence(),'embed'=>'true'];

        if(!empty($source->getMyKey())){
            $addParams['id'] = $source->getMyKey();
        }
        
        if(!empty($report)){
            $addParams['report-name'] = urlencode($report);
        }
        
        $url = \Ease\Shared::addUrlParams($feeder, $addParams);
        
        parent::__construct('<object data=\''.$url.'\' type=\'application/pdf\' width=\'100\'></object>',
            ['class' => 'embed-responsive', 'style' => 'padding-bottom:600px']);
    }
}
