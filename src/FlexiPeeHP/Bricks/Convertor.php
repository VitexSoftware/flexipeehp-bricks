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

    /**
     * Perform Conversion
     * 
     * @param boolean $keepId
     * @param boolean $addExtId
     * @param boolean $keepCode
     * 
     * @return \FlexiPeeHP\FlexiBeeRW converted object ( unsaved )
     */
    public function conversion($keepId = false, $addExtId = false,
                               $keepCode = false)
    {
        $this->prepareRules();
        $this->convertDocument($keepId, $addExtId, $keepCode);
        return $this->output;
    }

    /**
     * Get Classname without namespace prefix
     * 
     * @param object $object
     * 
     * @return string
     */
    static public function baseClassName($object)
    {
        return basename(str_replace('\\', '/', get_class($object)));
    }

    /**
     * Prepare conversion rules
     * 
     * @throws \Ease\Exception
     */
    public function prepareRules()
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

                        foreach (array_keys($this->output->getData()) as $colname) {
                            unset($this->rules[$colname]);
                        }

                        if ($this->input->getDataValue('typDokl') != $this->output->getDataValue('typDokl')) {
                            unset($this->rules['rada']);
                        }

                        foreach ($this->rules as $rule) {
                            if (preg_match('/^sum/', $rule)) {
                                unset($this->rules[$rule]);
                            }
                        }


                        $polozkyDokladu                = new \FlexiPeeHP\FakturaVydanaPolozka();
                        $itemColnames                  = array_keys($polozkyDokladu->getColumnsInfo());
                        $this->rules['polozkyFaktury'] = self::removeRoColumns(array_combine($itemColnames,
                                    $itemColnames), $polozkyDokladu);

                        break;
                    default :
                        throw new \Ease\Exception(sprintf(_('Unsupported Source document type %s'),
                            get_class($this->output)));
                        break;
                }
                break;
            default:
                throw new \Ease\Exception(sprintf(_('Unsupported Source document type %s'),
                    get_class($this->input)));
                break;
        }
        $this->convertItems($keepId, $addExtId, $keepCode);
    }

    /**
     * 
     * @param boolean $keepId   keep item IDs
     * @param boolean $addExtId add ext:originalEvidence:originalId 
     * @param boolean $keepCode keep items code
     */
    public function convertDocument($keepId = false, $addExtId = false,
                                    $keepCode = false)
    {
        $this->convertItems($keepId, $addExtId, $keepCode);
    }

    /**
     * 
     * @param string  $columnToTake usually "polozkyDokladu"
     * @param boolean $keepId       keep item IDs
     * @param boolean $keepCode     keep items code
     */
    public function convertSubitems($columnToTake, $keepId = false,
                                    $keepCode = false)
    {
        $subitemRules = $this->rules[$columnToTake];
        if (self::isAssoc($this->input->data[$columnToTake])) {
            $sourceData = [$this->input->data[$columnToTake]];
        } else {
            $sourceData = $this->input->data[$columnToTake];
        }

        $typUcOp = $this->input->getDataValue('typUcOp');

        foreach ($sourceData as $subItemData) {
            foreach (array_keys($subItemData) as $subitemColumn) {
                if (!array_key_exists($subitemColumn, $subitemRules)) {
                    unset($subItemData[$subitemColumn]);
                }
            }
            if ($typUcOp) {
                $subItemData['typUcOp'] = $typUcOp;
            } else {
                unset($subItemData['typUcOp']);
            }

            if ($keepCode === false) {
                unset($subItemData['kod']);
            }
            if ($keepId === false) {
                unset($subItemData['id']);
                unset($subItemData['external-ids']);
            }
            $this->output->addArrayToBranch($subItemData);
        }
    }

    /**
     * Convert document items
     * 
     * @param boolean $keepId   Keep original document ID
     * @param boolean $addExtId Add ExtID pointer to original document  
     * @param boolean $keepCode Keep original document Code
     */
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
                if(!empty($this->input->getSubItems())){
                    $this->convertSubitems($columnToTake, $keepId, $keepCode);
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
        foreach ($rules as $index=>$subrules) {
            if (is_array($subrules)) {
                $eback = $engine->getEvidence();
                $engine->setEvidence($engine->getEvidence().'-polozka');
                $rules[$index] = self::removeRoColumns($subrules, $engine);
                $engine->setEvidence($eback);
            } else {
                $columnInfo = $engine->getColumnInfo($subrules);
                if ($columnInfo['isWritable'] == 'false') {
                    unset($rules[$index]);
                }
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
