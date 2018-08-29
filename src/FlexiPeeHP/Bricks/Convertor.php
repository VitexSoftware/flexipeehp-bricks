<?php
/**
 * FlexiPeeHP Bricks - Convertor Class
 *
 * @author     Vítězslav Dvořák <info@vitexsofware.cz>
 * @copyright  (G) 2017-2018 Vitex Software
 */

namespace FlexiPeeHP\Bricks;

/**
 * Description of Convertor
 *
 * @author Vítězslav Dvořák <info@vitexsoftware.cz>
 */
class Convertor extends \Ease\Sand
{
    /**
     * Source Object
     * @var \FlexiPeeHP\FlexiBeeRO 
     */
    private $input;

    /**
     * Destination Object
     * @var \FlexiPeeHP\FlexiBeeRO 
     */
    private $output;

    /**
     *
     * @var array 
     */
    private $rules = [];

    /**
     * Convertor 
     * 
     * @param \FlexiPeeHP\FlexiBeeRO $input
     * @param \FlexiPeeHP\FlexiBeeRO $output
     */
    public function __construct(\FlexiPeeHP\FlexiBeeRO $input = null,
                                \FlexiPeeHP\FlexiBeeRO $output = null)
    {
        parent::__construct();
        if (!empty($input)) {
            $this->setSource($input);
        }
        if (!empty($output)) {
            $this->setDestination($output);
        }
    }

    /**
     * 
     * @param \FlexiPeeHP\FlexiBeeRO $source
     */
    public function setSource(\FlexiPeeHP\FlexiBeeRO $source)
    {
        $this->input = $source;
    }

    /**
     * 
     * @param \FlexiPeeHP\FlexiBeeRO $destinantion
     */
    public function setDestination(\FlexiPeeHP\FlexiBeeRO $destination)
    {
        $this->output = $destination;
    }

    public function conversion()
    {
        $this->convertDocument();
        return $this->output;
    }

    public function convertDocument()
    {
        switch (get_class($this->input)) {
            case 'FlexiPeeHP\Banka':
                switch (get_class($this->output)) {
                    case 'FlexiPeeHP\FakturaVydana':
                        // Banka['typPohybuK' => 'typPohybu.prijem'] -> FakturaVydana['typDokl' => 'ZDD']
//                        $this->output->setDataValue('firma',  \FlexiPeeHP\FlexiBeeRO::code( $this->input->getDataValue('firma')));
                        $this->rules = $this->commonItems();


                        break;
                    default :
                        throw new Exception(sprintf(_('Unsupported Source document type %s'),
                            get_class($this->output)));
                        break;
                }
                break;
            default:
                throw new Exception(sprintf(_('Unsupported Source document type %s'),
                    get_class($this->input)));
                break;
        }
    }

    public function convertItems()
    {
        
    }

    /**
     * 
     * @return array
     */
    public function commonItems()
    {
        return array_intersect(array_keys($this->input->getColumnsInfo()),
            array_keys($this->output->getColumnsInfo()));
    }
}
