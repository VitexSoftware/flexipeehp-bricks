<?php
/**
 * FlexiPeeHP Bricks
 *
 * @author Vítězslav Dvořák <info@vitexsoftware.cz>
 */

namespace FlexiPeeHP\ui;

/**
 * Description of DownloadInvoiceButton
 *
 * @author vitex
 */
class DownloadInvoiceButton extends \Ease\TWB\LinkButton
{

    /**
     * Button for download document's PDF
     *
     * @param \FlexiPeeHP\FlexiBeeRO $document
     */
    public function __construct($document)
    {
        parent::__construct('getpdf.php?evidence='.$document->getEvidence().'&id='.$document->getMyKey(),
            new \Ease\TWB\GlyphIcon('download').' '.sprintf(_('Download Your %s'),
                '<strong>'.$document->getDataValue('poznam').' '.
                $document->getDataValue('kod').'</strong>'), 'success');
    }
}
