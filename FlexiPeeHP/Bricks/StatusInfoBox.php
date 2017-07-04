<?php
/**
 * Description of SysFlexiBeeStatus.
 *
 * @author vitex
 */

namespace FlexiPeeHP\Bricks;

class StatusInfoBox extends \FlexiPeeHP\Company
{
    /**
     * FlexiBee Status
     * @var array
     */
    public $info = null;

    /**
     * Try to connect to FlexiBee
     */
    public function __construct()
    {
        parent::__construct();
        $this->info = $this->reindexArrayBy($this->getFlexiData(), 'dbNazev');
    }

    /**
     * Draw result
     */
    public function draw()
    {
        $myCompany = constant('FLEXIBEE_COMPANY');
        if (array_key_exists($myCompany, $this->info)) {
            $return = new \Ease\TWB\LinkButton(constant('FLEXIBEE_URL').'/c/'.$myCompany,
                $this->info[$myCompany]['nazev'], 'success');
        } else {
            $return = new \Ease\TWB\LinkButton(constant('FLEXIBEE_URL'),
                _('Chyba komunikace'), 'danger');
        }

        $return->draw();
    }
}