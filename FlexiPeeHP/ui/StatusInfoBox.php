<?php
/**
 * FlexiPeeHP Bricks
 *
 * @author Vítězslav Dvořák <info@vitexsoftware.cz>
 */

namespace FlexiPeeHP\ui;

/**
 * FlexiBee connection status widget
 */
class StatusInfoBox extends \FlexiPeeHP\Company
{
    /**
     * FlexiBee Status
     * @var array
     */
    public $info = [];

    /**
     * Try to connect to FlexiBee
     */
    public function __construct()
    {
        parent::__construct();
        $infoRaw = $this->getFlexiData();
        if (count($infoRaw) && !array_key_exists('success', $infoRaw)) {
            $this->info = $this->reindexArrayBy($infoRaw, 'dbNazev');
        }
    }

    /**
     * Draw result
     */
    public function draw()
    {
        $myCompany = $this->getCompany();
        if (array_key_exists($myCompany, $this->info)) {
            $return = new \Ease\TWB\LinkButton($this->url.'/c/'.$myCompany,
                $this->info[$myCompany]['nazev'], 'success');
        } else {
            $return = new \Ease\TWB\LinkButton($this->getApiURL(),
                _('Chyba komunikace'), 'danger');
        }

        $return->draw();
    }
}
