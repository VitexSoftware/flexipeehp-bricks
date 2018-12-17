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
     * @param \FlexiPeeHP\FlexiBeeRW $output
     */
    public function __construct(\FlexiPeeHP\FlexiBeeRO $input = null,
                                \FlexiPeeHP\FlexiBeeRW $output = null)
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

    public function conversion($keepId = false, $addExtId = true,
                               $keepCode = false)
    {
        $this->convertDocument($keepId, $addExtId, $keepCode);
        return $this->output;
    }

    static public function baseClassName($object)
    {
        return basename(str_replace('\\', '/', get_class($object)));
    }

    public function convertDocument($keepId = false, $addExtId = true,
                                    $keepCode = false)
    {
        switch (self::baseClassName($this->input)) {
            case 'FakturaVydana':
            case 'Banka':
                switch (self::baseClassName($this->output)) {
                    case 'FakturaVydana':
                        // Banka['typPohybuK' => 'typPohybu.prijem'] -> FakturaVydana['typDokl' => 'ZDD']
//                        $this->output->setDataValue('firma',  \FlexiPeeHP\FlexiBeeRO::code( $this->input->getDataValue('firma')));
                        $this->rules = array_combine($this->commonItems(),
                            $this->commonItems());
                        unset($this->rules['stavUhrK']);
                        unset($this->rules['datUcto']);
                        unset($this->rules['datUcto']);


                        foreach ($this->rules as $rule){
                            if(preg_match('/^sum/', $rule)){
                                unset($this->rules[$rule]);
                            }
                        }
                        
                        
                        $polozkyDokladu                = new \FlexiPeeHP\FakturaVydanaPolozka();
                        $itemColnames                  = array_keys($polozkyDokladu->getColumnsInfo());
                        $this->rules['polozkyFaktury'] = self::removeRoColumns(array_combine($itemColnames,
                                    $itemColnames), $polozkyDokladu);

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
        $this->convertItems($keepId, $addExtId, $keepCode);
    }

    public function convertItems($keepId = false, $addExtId = true,
                                 $keepCode = false)
    {
        if ($keepCode === false) {
            unset($this->rules['kod']);
        }
        if ($keepId === false) {
            unset($this->rules['id']);
        }
        foreach (self::removeRoColumns($this->rules, $this->output) as $columnToTake => $subitemColumns) {
        
            if (is_array($subitemColumns)) {

                if (self::isAssoc($this->input->data[$columnToTake])) {
                    $sourceData = [$this->input->data[$columnToTake]];
                } else {
                    $sourceData = $this->input->data[$columnToTake];
    }

                foreach ($sourceData as $subitemData) {
                    foreach ($subitemColumns as $subitemColumn) {
                        if (!array_key_exists($subitemColumn, $subitemColumns)) {
                            unset($subitemData[$subitemColumn]);
                        }
                    }

                    if ($keepCode === false) {
                        unset($subitemData['kod']);
                    }
                    if ($keepId === false) {
                        unset($subitemData['id']);
                    }


                    $this->output->addArrayToBranch($subitemData);
                }
            } else {

                $this->output->setDataValue($columnToTake,
                    $this->input->getDataValue($columnToTake));
            }
        }
        if ($addExtId) {
            $this->output->setDataValue('id',
                'ext:src:'.$this->input->getEvidence().':'.$this->input->getMyKey());
        }
    }

    /**
     * Returns only writable columns
     * 
     * @param array                  $rules   all Columns
     * @param \FlexiPeeHP\FlexiBeeRW $engine  saver class
     */
    public static function removeRoColumns(array $rules, $engine)
    {

        foreach ($rules as $column) {
            $columnInfo = $engine->getColumnInfo($column);
            if ($columnInfo['isWritable'] == 'false') {
                unset($rules[$column]);
            }
        }
        return $rules;
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
