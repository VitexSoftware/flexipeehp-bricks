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
     *
     * @param string|array $init    company dbNazev or initial data
     * @param array        $options Connection settings override
     */
    public function __construct($init = null, $properites = [])
    {
        parent::__construct($init, $properites);
        $infoRaw = $this->getFlexiData();
        if (!empty($infoRaw) && !array_key_exists('success', $infoRaw)) {
            $this->info = $this->reindexArrayBy($infoRaw, 'dbNazev');
        }
    }

    /**
     * Is Configured company connected ?
     * 
     * @return boolean
     */
    public function connected()
    {
        return array_key_exists($this->getCompany(), $this->info);
    }

    /**
     * Draw result
     */
    public function draw()
    {
        if ($this->connected()) {
        $myCompany = $this->getCompany();
            $return = new \Ease\TWB\LinkButton($this->url.'/c/'.$myCompany,
                $this->info[$myCompany]['nazev'], 'success');
        } else {
            $return = new \Ease\TWB\LinkButton($this->getApiURL(),
                _('Connection Problem'), 'danger');
        }

        $return->draw();
    }
}
